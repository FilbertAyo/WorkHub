<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\WorkPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $user = Auth::user();
        $query = Document::with(['user', 'period']);

        // Reviewers and admins can see all documents, others see only their own
        if (!$user->hasAnyRole(['reviewer', 'admin', 'verifier', 'approver'])) {
            $query->forUser($user->id);
        }

        // Filter by type if provided
        if ($request->has('type') && $request->type) {
            $query->ofType($request->type);
        }

        // Filter by period if provided
        if ($request->has('period_id') && $request->period_id) {
            $query->forPeriod($request->period_id);
        }

        // Filter by year if provided
        if ($request->has('year') && $request->year) {
            $query->whereHas('period', function($q) use ($request) {
                $q->where('year', $request->year);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $documents = $query->latest()->paginate(15)->withQueryString();

        $currentPeriod = WorkPeriod::getCurrent();
        $nextPeriod = $currentPeriod ? $currentPeriod->getNextPeriod() : null;
        $deadlineStatuses = null;
        if ($currentPeriod) {
            $deadlineStatuses = [
                'plan_badge' => $currentPeriod->getPlanDeadlineBadgeClass(),
                'plan_text' => $currentPeriod->getDeadlineStatusText('plan'),
                'plan_due' => $currentPeriod->plan_deadline,
                'report_badge' => $currentPeriod->getReportDeadlineBadgeClass(),
                'report_text' => $currentPeriod->getDeadlineStatusText('report'),
                'report_due' => $currentPeriod->report_deadline,
            ];
        }

        return view('documents.index', [
            'documents' => $documents,
            'periods' => WorkPeriod::orderBy('year', 'desc')->orderBy('week_number', 'desc')->get(),
            'years' => WorkPeriod::select('year')->distinct()->orderBy('year', 'desc')->pluck('year'),
            'currentPeriod' => $currentPeriod,
            'nextPeriod' => $nextPeriod,
            'deadlineStatuses' => $deadlineStatuses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $selectedType = $request->query('type');

        // Authorize create action
        $this->authorize('create', Document::class);

        // If type is specified, also check if user can create that specific type
        if ($selectedType) {
            $this->authorize('createType', [Document::class, $selectedType]);
        }

        $availableTypes = Document::getAvailableTypesForUser($user);

        if (empty($availableTypes)) {
            return redirect()->route('documents.index')
                ->with('error', 'You do not have permission to create documents.');
        }

        // If type is provided in query string, pre-select it
        if ($selectedType && isset($availableTypes[$selectedType])) {
            // Validate that user can create this type
            if (Document::canUserCreateType($user, $selectedType)) {
                return view('documents.create', compact('availableTypes', 'selectedType'));
            }
        }

        return view('documents.create', compact('availableTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'type' => ['required', 'string', 'in:weekly_plan,weekly_report,monthly_report,weekly_minutes'],
            'content' => ['required', 'string', 'min:10'],
        ]);

        // Authorize create action
        $this->authorize('create', Document::class);

        // Authorize create action with the specific type
        $this->authorize('createType', [Document::class, $request->type]);

        // Get appropriate period based on document type
        $period = null;
        if (in_array($request->type, [Document::TYPE_WEEKLY_PLAN, Document::TYPE_WEEKLY_REPORT])) {
            if ($request->type === Document::TYPE_WEEKLY_PLAN) {
                // Plan is for NEXT week (submitted during current week)
                $currentPeriod = WorkPeriod::getCurrent();
                if ($currentPeriod) {
                    $period = $currentPeriod->getNextPeriod();
                    if (!$period) {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'Next week period not found. Please contact administrator.');
                    }
                } else {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'No current work period found. Please contact administrator.');
                }
            } elseif ($request->type === Document::TYPE_WEEKLY_REPORT) {
                // Report is for CURRENT week
                $period = WorkPeriod::getCurrent();
                if (!$period) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'No current work period found. Please contact administrator.');
                }
            }
        } elseif ($request->type === Document::TYPE_MONTHLY_REPORT) {
            // Monthly report uses current period
            $period = WorkPeriod::getCurrent();
        }
        // weekly_minutes doesn't require a period (can be null)

        if ($period && !$period->isOpen()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This work period is closed. Please contact your administrator.');
        }

        // Determine state based on action
        $action = $request->input('action', 'draft');
        $state = ($action === 'submit') ? Document::STATE_SUBMITTED : Document::STATE_DRAFT;

        $document = Document::create([
            'user_id' => $user->id,
            'period_id' => $period?->id,
            'type' => $request->type,
            'data' => [
                'content' => $request->input('content'),
                'title' => $request->input('title', 'Untitled'),
            ],
            'state' => $state,
        ]);

        $message = ($action === 'submit')
            ? 'Document submitted successfully.'
            : 'Document saved as draft.';

        return redirect()->route('documents.show', Hashids::encode($document->id))
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $decodedId = Hashids::decode($id);
        $document = Document::with(['user', 'period', 'comments.user'])
            ->findOrFail($decodedId[0] ?? null);

        // Authorize view action
        $this->authorize('view', $document);

        // Get related documents (plan for report, report for plan)
        $relatedDocument = null;
        $relatedAction = null;
        if ($document->period_id) {
            if ($document->type === Document::TYPE_WEEKLY_PLAN) {
                // Get the report for this period (same period)
                $relatedDocument = Document::where('period_id', $document->period_id)
                    ->where('type', Document::TYPE_WEEKLY_REPORT)
                    ->where('user_id', $document->user_id)
                    ->first();
            } elseif ($document->type === Document::TYPE_WEEKLY_REPORT) {
                // Get the plan for this period (same period)
                $relatedDocument = Document::where('period_id', $document->period_id)
                    ->where('type', Document::TYPE_WEEKLY_PLAN)
                    ->where('user_id', $document->user_id)
                    ->first();
            }

            if (!$relatedDocument) {
                $user = Auth::user();
                if ($document->type === Document::TYPE_WEEKLY_PLAN &&
                    Document::canUserCreateType($user, Document::TYPE_WEEKLY_REPORT, $document->period)) {
                    $relatedAction = [
                        'type' => Document::TYPE_WEEKLY_REPORT,
                        'label' => 'Create Weekly Report for this Week',
                        'route' => route('documents.create', ['type' => Document::TYPE_WEEKLY_REPORT]),
                        'description' => 'Weekly report is due on ' . $document->period->report_deadline->format('M d, Y'),
                    ];
                } elseif ($document->type === Document::TYPE_WEEKLY_REPORT &&
                    Document::canUserCreateType($user, Document::TYPE_WEEKLY_PLAN, $document->period)) {
                    $relatedAction = [
                        'type' => Document::TYPE_WEEKLY_PLAN,
                        'label' => 'Create Weekly Plan for this Week',
                        'route' => route('documents.create', ['type' => Document::TYPE_WEEKLY_PLAN]),
                        'description' => 'Weekly plan should outline goals for this week.',
                    ];
                }
            }
        }

        return view('documents.show', compact('document', 'relatedDocument', 'relatedAction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $decodedId = Hashids::decode($id);
        $document = Document::findOrFail($decodedId[0] ?? null);

        // Authorize update action
        $this->authorize('update', $document);

        return view('documents.edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $decodedId = Hashids::decode($id);
        $document = Document::findOrFail($decodedId[0] ?? null);

        // Authorize update action
        $this->authorize('update', $document);

        $request->validate([
            'content' => ['required', 'string', 'min:10'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        // Determine state based on action
        $action = $request->input('action', 'draft');

        // If submitting, authorize submit action
        if ($action === 'submit') {
            $this->authorize('submit', $document);
        }

        $data = $document->data ?? [];
        $data['content'] = $request->input('content');
        if ($request->filled('title')) {
            $data['title'] = $request->input('title');
        }

        $updateData = ['data' => $data];

        // Update state if submitting
        if ($action === 'submit') {
            $updateData['state'] = Document::STATE_SUBMITTED;
        }

        $document->update($updateData);

        $message = ($action === 'submit')
            ? 'Document submitted successfully.'
            : 'Document updated successfully.';

        return redirect()->route('documents.show', $id)
            ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $decodedId = Hashids::decode($id);
        $document = Document::findOrFail($decodedId[0] ?? null);

        // Authorize delete action
        $this->authorize('delete', $document);

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    /**
     * Submit a document (change state from draft to submitted)
     */
    public function submit(Request $request, string $id)
    {
        $decodedId = Hashids::decode($id);
        $document = Document::findOrFail($decodedId[0] ?? null);

        // Authorize submit action
        $this->authorize('submit', $document);

        $document->submit();

        return redirect()->route('documents.show', $id)
            ->with('success', 'Document submitted successfully.');
    }

    /**
     * Reviewer dashboard - Show all submitted documents grouped by type
     */
    public function reviewerDashboard(Request $request)
    {
        $user = Auth::user();

        // Only reviewers and admins can access this
        if (!$user->hasAnyRole(['reviewer', 'admin'])) {
            abort(403, 'Unauthorized access.');
        }

        $baseQuery = Document::with(['user', 'comments'])
            ->submitted();

        // Filter by user if provided
        if ($request->has('user_id') && $request->user_id) {
            $baseQuery->forUser($request->user_id);
        }

        // Filter by week/month if provided
        if ($request->has('period') && $request->period) {
            $period = $request->period;
            if ($period === 'this_week') {
                $baseQuery->whereBetween('updated_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
            } elseif ($period === 'last_week') {
                $baseQuery->whereBetween('updated_at', [
                    now()->subWeek()->startOfWeek(),
                    now()->subWeek()->endOfWeek()
                ]);
            } elseif ($period === 'this_month') {
                $baseQuery->whereMonth('updated_at', now()->month)
                    ->whereYear('updated_at', now()->year);
            } elseif ($period === 'last_month') {
                $baseQuery->whereMonth('updated_at', now()->subMonth()->month)
                    ->whereYear('updated_at', now()->subMonth()->year);
            }
        }

        if ($request->has('work_period_id') && $request->work_period_id) {
            $baseQuery->where('period_id', $request->work_period_id);
        }

        if ($request->has('year') && $request->year) {
            $baseQuery->whereHas('period', function($q) use ($request) {
                $q->where('year', $request->year);
            });
        }

        $allDocuments = (clone $baseQuery)->latest()->get();

        // Group documents by type
        $groupedDocuments = [
            'weekly_plan' => $allDocuments->where('type', Document::TYPE_WEEKLY_PLAN),
            'weekly_report' => $allDocuments->where('type', Document::TYPE_WEEKLY_REPORT),
            'monthly_report' => $allDocuments->where('type', Document::TYPE_MONTHLY_REPORT),
            'weekly_minutes' => $allDocuments->where('type', Document::TYPE_WEEKLY_MINUTES),
        ];

        // Filter by type if provided (for single type view)
        $selectedType = $request->has('type') && $request->type ? $request->type : null;
        if ($selectedType && isset($groupedDocuments[$selectedType])) {
            $groupedDocuments = [$selectedType => $groupedDocuments[$selectedType]];
        }

        // Get statistics (apply same filters)
        $statsQuery = (clone $baseQuery);
        $stats = [
            'total_submitted' => $statsQuery->count(),
            'weekly_plans' => (clone $statsQuery)->weeklyPlans()->count(),
            'weekly_reports' => (clone $statsQuery)->weeklyReports()->count(),
            'monthly_reports' => (clone $statsQuery)->monthlyReports()->count(),
            'weekly_minutes' => (clone $statsQuery)->weeklyMinutes()->count(),
        ];

        $usersWithDocuments = \App\Models\User::whereHas('documents', function($q) {
            $q->submitted();
        })->orderBy('name')->get();

        $periodOptions = WorkPeriod::orderBy('year', 'desc')
            ->orderBy('week_number', 'desc')
            ->take(20)
            ->get();

        $years = WorkPeriod::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('documents.reviewer-dashboard', compact(
            'groupedDocuments',
            'stats',
            'usersWithDocuments',
            'selectedType',
            'periodOptions',
            'years'
        ));
    }

    /**
     * Employee dashboard - Show pending submissions and history
     */
    public function employeeDashboard(Request $request)
    {
        $user = Auth::user();

        // Allow employees and admins (admins can see their own dashboard too)
        // Restrict reviewers and minutes_preparer (unless they're also admin)
        if (!$user->hasRole('admin') && $user->hasAnyRole(['reviewer', 'minutes_preparer'])) {
            abort(403, 'Unauthorized access. This dashboard is for employees only.');
        }

        $availableTypes = Document::getAvailableTypesForUser($user);
        $currentPeriod = WorkPeriod::getCurrent();
        $nextPeriod = $currentPeriod ? $currentPeriod->getNextPeriod() : null;

        $periodFilterId = $request->input('period_id');
        $periodOptions = WorkPeriod::orderBy('year', 'desc')
            ->orderBy('week_number', 'desc')
            ->take(20)
            ->get();

        $deadlineStatuses = null;
        if ($currentPeriod) {
            $deadlineStatuses = [
                'plan_badge' => $currentPeriod->getPlanDeadlineBadgeClass(),
                'plan_text' => $currentPeriod->getDeadlineStatusText('plan'),
                'plan_due' => $currentPeriod->plan_deadline,
                'report_badge' => $currentPeriod->getReportDeadlineBadgeClass(),
                'report_text' => $currentPeriod->getDeadlineStatusText('report'),
                'report_due' => $currentPeriod->report_deadline,
            ];
        }

        // Get pending submissions (draft documents)
        $pendingDocumentsQuery = Document::with('comments')
            ->forUser($user->id)
            ->drafts()
            ->latest();

        $allDocumentsQuery = Document::with('comments')
            ->forUser($user->id)
            ->latest();

        if ($periodFilterId) {
            $pendingDocumentsQuery->where('period_id', $periodFilterId);
            $allDocumentsQuery->where('period_id', $periodFilterId);
        }

        $pendingDocuments = $pendingDocumentsQuery->get();

        // Get all documents (history)
        $allDocuments = $allDocumentsQuery->get();

        // Group pending by type for better organization
        $pendingByType = [
            'weekly_plan' => $pendingDocuments->where('type', Document::TYPE_WEEKLY_PLAN),
            'weekly_report' => $pendingDocuments->where('type', Document::TYPE_WEEKLY_REPORT),
            'monthly_report' => $pendingDocuments->where('type', Document::TYPE_MONTHLY_REPORT),
        ];

        // Statistics
        $stats = [
            'total_pending' => $pendingDocuments->count(),
            'total_submitted' => $allDocuments->where('state', Document::STATE_SUBMITTED)->count(),
            'weekly_plan_pending' => $pendingByType['weekly_plan']->count(),
            'weekly_report_pending' => $pendingByType['weekly_report']->count(),
            'monthly_report_pending' => $pendingByType['monthly_report']->count(),
        ];

        $periodInfo = [
            'current' => $currentPeriod,
            'next' => $nextPeriod,
        ];

        $timelineData = WorkPeriod::orderBy('year', 'desc')
            ->orderBy('week_number', 'desc')
            ->take(6)
            ->get()
            ->map(function ($period) use ($user) {
                $plan = Document::where('user_id', $user->id)
                    ->where('period_id', $period->id)
                    ->where('type', Document::TYPE_WEEKLY_PLAN)
                    ->first();

                $report = Document::where('user_id', $user->id)
                    ->where('period_id', $period->id)
                    ->where('type', Document::TYPE_WEEKLY_REPORT)
                    ->first();

                return [
                    'period' => $period,
                    'plan' => $plan,
                    'report' => $report,
                    'plan_status' => $plan?->state ?? 'missing',
                    'report_status' => $report?->state ?? 'missing',
                ];
            })
            ->sortByDesc(fn ($entry) => $entry['period']->week_start_date)
            ->values();

        $selectedPeriod = $periodOptions->firstWhere('id', $periodFilterId);

        return view('documents.employee-dashboard', compact(
            'pendingDocuments',
            'allDocuments',
            'pendingByType',
            'stats',
            'periodInfo',
            'deadlineStatuses',
            'availableTypes',
            'periodOptions',
            'selectedPeriod',
            'periodFilterId',
            'timelineData'
        ));
    }
}
