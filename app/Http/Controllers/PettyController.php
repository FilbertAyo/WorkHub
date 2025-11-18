<?php

namespace App\Http\Controllers;

use App\Mail\FirstApprovalMail;
use App\Mail\LastApprovalMail;
use App\Mail\PettyRequestMail;
use App\Mail\RejectMail;
use App\Mail\ResubmitMail;
use App\Mail\SuccessPayment;
use App\Models\ApprovalLog;
use App\Models\Deposit;
use App\Models\Petty;
use App\Models\PettyAttachment;
use App\Models\PettyList;
use App\Models\StartPoint;
use App\Models\Stop;
use App\Models\TransMode;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use NumberToWords\NumberToWords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class PettyController extends Controller
{
    /**
     * Helper: Encode an integer ID using Hashids
     */
    private function encodeHashId(int $id): string
    {
        return Hashids::encode($id);
    }

    /**
     * Helper: Build common petty mail arguments [requesterName, reason, encodedId]
     */
    private function buildPettyMailArgs(Petty $petty): array
    {
        $requester = User::find($petty->user_id);
        $name = $requester?->name ?? 'User';
        $reason = $petty->request_for;
        $encodedId = $this->encodeHashId($petty->id);
        return [$name, $reason, $encodedId];
    }

    /**
     * Helper: Get emails of users in a department who have a specific permission
     */
    private function collectDepartmentEmails(string $permission, int $departmentId): array
    {
        return User::permission($permission)
            ->where('department_id', $departmentId)
            ->pluck('email')
            ->filter()
            ->unique()
            ->toArray();
    }

    /**
     * Helper: Notify approvers by permission within a department via email and/or SMS based on their preferences
     */
    private function notifyApprovers(string $permission, int $departmentId, object $mailable, string $smsMessage): void
    {
        $users = User::permission($permission)
            ->where('department_id', $departmentId)
            ->get(['id', 'email', 'phone', 'notification_channel']);

        if ($users->isEmpty()) {
            return;
        }

        $emails = [];
        $notifier = app(NotificationService::class);

        foreach ($users as $user) {
            $channel = $user->notification_channel ?: 'sms';
            if (in_array($channel, ['email', 'both']) && !empty($user->email)) {
                $emails[] = $user->email;
            }
            if (in_array($channel, ['sms', 'both']) && !empty($user->phone)) {
                try {
                    $notifier->sendSms($user->phone, $smsMessage);
                } catch (\Throwable $e) {
                    Log::error('Failed to send SMS to approver', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                }
            }
        }

        $emails = array_values(array_unique(array_filter($emails)));
        if (!empty($emails)) {
            $this->trySendMail($emails, $mailable);
        }
    }

    /**
     * Helper: Safely send an email and log failures. Returns true if attempted and recipients existed.
     */
    private function trySendMail(array|string $recipients, object $mailable): bool
    {
        if (empty($recipients)) {
            return false;
        }
        try {
            Mail::to($recipients)->send($mailable);
            return true;
        } catch (\Throwable $e) {
            Log::error('PettyController: Failed to send email', [
                'recipients' => $recipients,
                'mailable' => get_class($mailable),
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Helper: Generate unique petty cash code
     */
    private function generateUniquePettyCode()
    {
        do {
            $code = strtoupper(Str::random(2)) . rand(100, 999);
        } while (Petty::where('code', $code)->exists());
        return $code;
    }

    /**
     * Helper: Parse attachments from database format to array/object
     */
    private function parseAttachments($attachments)
    {
        $parsed = [];
        foreach ($attachments as $attachment) {
            $rawProducts = $attachment->product_name ?? '';
            $products = collect(explode(',', $rawProducts))
                ->map(fn($item) => trim($item))
                ->filter()
                ->map(function ($item) {
                    $parts = explode('-', $item, 2);
                    return [
                        'name' => trim($parts[0] ?? ''),
                        'qty' => trim($parts[1] ?? ''),
                    ];
                })
                ->toArray();

            // Return as object to maintain consistency with blade template expectations
            $parsed[] = (object)[
                'name' => $attachment->name,
                'products' => $products ?: [['name' => '', 'qty' => '']],
                'attachment' => $attachment->attachment ?? null
            ];
        }
        return $parsed;
    }

    /**
     * Helper: Create trip and stops
     */
    private function createTripAndStops($pettyId, $data)
    {
        if (isset($data['from_place']) && isset($data['destinations'])) {
            $trip = Trip::create([
                'petty_id' => $pettyId,
                'from_place' => $data['from_place'],
            ]);

            foreach ($data['destinations'] as $destination) {
                $trip->stops()->create(['destination' => $destination]);
            }
        }
    }

    /**
     * Helper: Update trip and stops
     */
    private function updateTripAndStops($pettyId, $data)
    {
        $trip = Trip::where('petty_id', $pettyId)->first();

        if ($trip) {
            $trip->stops()->delete();
            $trip->update(['from_place' => $data['from_place']]);
        } else {
            $trip = Trip::create([
                'petty_id' => $pettyId,
                'from_place' => $data['from_place'],
            ]);
        }

        foreach ($data['destinations'] as $destination) {
            $trip->stops()->create(['destination' => $destination]);
        }
    }

    /**
     * Helper: Save attachments to database
     */
    private function saveAttachments($pettyId, $attachments)
    {
        foreach ($attachments as $index => $attachment) {
            $productLines = [];
            foreach ($attachment['products'] as $product) {
                $productLines[] = $product['name'] . ' - ' . $product['qty'];
            }

            $filePath = null;

            // Check if new file uploaded
            if (!empty($attachment['file']) && is_object($attachment['file'])) {
                $filePath = $attachment['file']->store('attachments', 'public');
                $filePath = 'storage/' . $filePath;
            }
            // Preserve existing file if provided in hidden field (edit mode)
            elseif (isset($attachment['existing_file']) && !empty($attachment['existing_file'])) {
                $filePath = $attachment['existing_file'];
            }

            PettyAttachment::create([
                'petty_id' => $pettyId,
                'name' => $attachment['customer_name'],
                'product_name' => implode(',', $productLines),
                'attachment' => $filePath,
            ]);
        }
    }

    /**
     * Helper: Send approval request emails
     */
    private function sendApprovalRequest($petty)
    {
        $requester = Auth::user();
        [$name, $reason, $encodedId] = $this->buildPettyMailArgs($petty);
        $sms = "New petty cash request from {$name} for {$reason}. Ref: {$encodedId}";
        $this->notifyApprovers('first pettycash approval', $requester->department_id, new PettyRequestMail($name, $reason, $encodedId), $sms);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Auto-reject stale resubmission requests
        DB::table('petties')
            ->where('status', 'resubmission')
            ->where('updated_at', '<', now()->subDay())
            ->update(['status' => 'rejected']);

        Log::info('Dashboard-triggered auto-reject of stale petty cash requests.');

        // Fetch requests only for the logged-in user
        $requests = Petty::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pettycash.request', compact('requests'));
    }

    /**
     * Show the form for creating a new petty cash request.
     */
    public function create()
    {
        return view('pettycash.create');
    }

    /**
     * Store a newly created petty cash request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'department_id' => 'required|integer',
            'request_for' => 'required|string|in:Sales Delivery,Transport,Office Supplies',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:1000',
            'request_type' => 'required|string',
        ]);

        $code = $this->generateUniquePettyCode();
        $validated['code'] = $code;
        $validated['status'] = 'pending';

        $petty = Petty::create($validated);
        $this->sendApprovalRequest($petty);

        return redirect()->route('petty.index')->with('success', 'Petty cash request created successfully!');
    }

    /**
     * Autocomplete for destinations
     */
    public function autocomplete(Request $request)
    {
        $term = trim((string) $request->get('term', ''));

        if ($term === '') {
            return response()->json([]);
        }

        $results = Stop::where('destination', 'LIKE', "%{$term}%")
            ->distinct()
            ->pluck('destination')
            ->take(10)
            ->values()
            ->all();

        // Ensure we always return a plain JSON array of strings
        return response()->json($results);
    }

    // =============================================================================
    // OTHER EXISTING METHODS (UNCHANGED)
    // =============================================================================

    public function show($hashid)
    {
        $id = Hashids::decode($hashid);

        $request = Petty::with(['attachments', 'lists', 'trips.startPoint', 'trips.stops', 'transMode', 'user', 'department'])
            ->findOrFail($id[0]);
        $approval_logs = ApprovalLog::where('petty_id', $id[0])->get();

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('en');
        $amountWords = $numberTransformer->toWords($request->amount);
        $amountWords = ucwords($amountWords);
        $amountInWords = 'TZS ' . $amountWords;

        $verifiedBy = ApprovalLog::where('petty_id', $id[0])->where('action', 'approved')->first();
        $approvedBy = ApprovalLog::where('petty_id', $id[0])->where('action', 'approved')->skip(1)->take(1)->first();
        $gm = User::where('email', 'gm@marstanzania.com')->first();

        return view('pettycash.view', compact('request', 'amountInWords', 'approval_logs', 'verifiedBy', 'approvedBy', 'gm'));
    }

    public function requests_list()
    {
        $requests = Petty::orderBy('created_at', 'desc')
            ->where('department_id', Auth::user()->department_id)
            ->get();

        return view('pettycash.approval.index', compact('requests'));
    }

    public function requestsCashier()
    {
        $requests = Petty::orderBy('created_at', 'desc')
            ->where('department_id', Auth::user()->department_id)
            ->whereIn('status', ['approved', 'paid'])
            ->get();

        return view('pettycash.approval.index', compact('requests'));
    }

    public function all_requests()
    {
        $requests = Petty::orderBy('created_at', 'desc')->get();

        return view('pettycash.approval.index', compact('requests'));
    }

    public function request_show($hashid)
    {
        $id = Hashids::decode($hashid);

        $request = Petty::with(['attachments', 'lists', 'trips.startPoint', 'trips.stops', 'transMode', 'user', 'department'])
            ->findOrFail($id[0]);
        $latest = ApprovalLog::where('petty_id', $id[0])->where('user_id', Auth::user()->id)->latest()->first();
        $approval_logs = ApprovalLog::where('petty_id', $id[0])->get();
        $approval = optional($latest)->action;

        $verifiedBy = ApprovalLog::where('petty_id', $id[0])->where('action', 'approved')->first();
        $approvedBy = ApprovalLog::where('petty_id', $id[0])->where('action', 'approved')->skip(1)->take(1)->first();
        $gm = User::where('email', 'gm@marstanzania.com')->first();

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('en');
        $amountWords = $numberTransformer->toWords($request->amount);
        $amountWords = ucwords($amountWords);
        $amountInWords = 'TZS ' . $amountWords;

        return view('pettycash.approval.details', compact('request', 'amountInWords', 'approval', 'approval_logs', 'verifiedBy', 'approvedBy', 'gm'));
    }

    public function f_approve($id)
    {
        ApprovalLog::create([
            'petty_id' => $id,
            'user_id' => Auth::user()->id,
            'action' => 'approved',
        ]);

        $request = Petty::findOrFail($id);
        $request->status = 'processing';
        $request->save();

        [$name, $reason, $encodedId] = $this->buildPettyMailArgs($request);
        $sms = "Petty cash request {$encodedId} moved to processing.";
        $this->notifyApprovers('last pettycash approval', $request->department_id, new FirstApprovalMail($name, $reason, $encodedId), $sms);

        return redirect()->back()->with('success', 'Request approved and status updated');
    }

    public function l_approve($id)
    {
        ApprovalLog::create([
            'petty_id' => $id,
            'user_id' => Auth::user()->id,
            'action' => 'approved',
        ]);

        $request = Petty::findOrFail($id);
        $request->status = 'approved';
        $request->save();

        [$name, $reason, $encodedId] = $this->buildPettyMailArgs($request);
        $sms = "Petty cash request {$encodedId} approved. Awaiting payment.";
        $this->notifyApprovers('approve petycash payments', $request->department_id, new LastApprovalMail($name, $reason, $encodedId), $sms);

        return redirect()->back()->with('success', 'Request approved and status updated');
    }

    public function c_approve($id)
    {
        $request = Petty::findOrFail($id);

        // Check if already paid
        $alreadyPaid = ApprovalLog::where('petty_id', $id)
            ->where('action', 'paid')
            ->exists();

        if ($alreadyPaid) {
            return redirect()->back()->with('error', 'This petty cash request has already been paid.');
        }

        $latestDeposit = Deposit::latest()->first();

        if (!$latestDeposit) {
            return redirect()->back()->with('error', 'No deposit available.');
        }

        // Deduct amount
        $latestDeposit->remaining -= $request->amount;
        $latestDeposit->save();

        $log = ApprovalLog::create([
            'petty_id' => $id,
            'user_id' => Auth::user()->id,
            'action' => 'paid',
        ]);

        $request->status = 'paid';
        $request->paid_date = $log->created_at;
        $request->save();

        [$name, $reason, $encodedId] = $this->buildPettyMailArgs($request);
        $requester = User::find($request->user_id);
        if ($requester) {
            // Email if preferred
            if (in_array($requester->notification_channel ?? 'sms', ['email', 'both']) && $requester->email) {
                $this->trySendMail($requester->email, new SuccessPayment($name, $reason, $encodedId));
            }
            // SMS if preferred
            if (in_array($requester->notification_channel ?? 'sms', ['sms', 'both']) && $requester->phone) {
                try {
                    app(NotificationService::class)->sendSms($requester->phone, "Payment done for petty cash {$encodedId}.");
                } catch (\Throwable $e) {
                    Log::error('Failed to send payment SMS to requester', ['user_id' => $requester->id, 'error' => $e->getMessage()]);
                }
            }
        }

        return redirect()->back()->with('success', 'Payment done successfully, and the amount has been deducted from your deposit.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
            'action' => 'required|string'
        ]);

        ApprovalLog::create([
            'petty_id' => $id,
            'user_id' => Auth::user()->id,
            'action' => $request->action,
            'comment' => $request->comment,
        ]);

        $petty = Petty::find($id);

        if ($petty) {
            $petty->status = $request->action;
            $petty->save();

            [$name, $reason, $encodedId] = $this->buildPettyMailArgs($petty);
            $requester = User::find($petty->user_id);
            if ($requester) {
                $channel = $requester->notification_channel ?? 'sms';
                if ($request->action === 'rejected') {
                    if (in_array($channel, ['email', 'both']) && $requester->email) {
                        $this->trySendMail($requester->email, new RejectMail($name, $reason, $encodedId));
                    }
                    if (in_array($channel, ['sms', 'both']) && $requester->phone) {
                        app(NotificationService::class)->sendSms($requester->phone, "Your petty cash {$encodedId} was rejected.");
                    }
                    return redirect()->back()->with('success', 'This request was rejected and feedback sent successfully.');
                } else {
                    if (in_array($channel, ['email', 'both']) && $requester->email) {
                        $this->trySendMail($requester->email, new ResubmitMail($name, $reason, $encodedId));
                    }
                    if (in_array($channel, ['sms', 'both']) && $requester->phone) {
                        app(NotificationService::class)->sendSms($requester->phone, "Your petty cash {$encodedId} requires resubmission.");
                    }
                    return redirect()->back()->with('success', 'You recommended resubmission for this petty cash request and feedback was sent successfully.');
                }
            }
        }

        return redirect()->back()->with('error', 'Request not found.');
    }

    public function updateAttachment(Request $request, $id)
    {
        $request->validate([
            'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xlsx,xls|max:2048',
        ]);

        $petty = Petty::findOrFail($id);

        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');
            $attachmentName = time() . '_' . $attachment->getClientOriginalName();
            $attachment->storeAs('public/attachments', $attachmentName);
            $petty->attachment = 'storage/attachments/' . $attachmentName;
            $petty->save();

            return redirect()->back()->with('success', 'Attachment updated successfully.');
        }

        return redirect()->back()->with('error', 'No file uploaded.');
    }
}
