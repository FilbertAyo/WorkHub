<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Document Types Constants
     */
    public const TYPE_WEEKLY_PLAN = 'weekly_plan';
    public const TYPE_WEEKLY_REPORT = 'weekly_report';
    public const TYPE_MONTHLY_REPORT = 'monthly_report';
    public const TYPE_WEEKLY_MINUTES = 'weekly_minutes';

    /**
     * Document State Constants
     */
    public const STATE_DRAFT = 'draft';
    public const STATE_SUBMITTED = 'submitted';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'data',
        'state',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get all available document types
     *
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_WEEKLY_PLAN => 'Weekly Plan',
            self::TYPE_WEEKLY_REPORT => 'Weekly Report',
            self::TYPE_MONTHLY_REPORT => 'Monthly Report',
            self::TYPE_WEEKLY_MINUTES => 'Weekly Minutes',
        ];
    }

    /**
     * Get all available document states
     *
     * @return array
     */
    public static function getStates(): array
    {
        return [
            self::STATE_DRAFT => 'Draft',
            self::STATE_SUBMITTED => 'Submitted',
        ];
    }

    /**
     * Get human-readable type name
     *
     * @return string
     */
    public function getTypeNameAttribute(): string
    {
        return self::getTypes()[$this->type] ?? ucwords(str_replace('_', ' ', $this->type));
    }

    /**
     * Get human-readable state name
     *
     * @return string
     */
    public function getStateNameAttribute(): string
    {
        return self::getStates()[$this->state] ?? ucfirst($this->state);
    }

    /**
     * Check if document is draft
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->state === self::STATE_DRAFT;
    }

    /**
     * Check if document is submitted
     *
     * @return bool
     */
    public function isSubmitted(): bool
    {
        return $this->state === self::STATE_SUBMITTED;
    }

    /**
     * Submit the document
     *
     * @return bool
     */
    public function submit(): bool
    {
        return $this->update(['state' => self::STATE_SUBMITTED]);
    }

    /**
     * Revert document to draft
     *
     * @return bool
     */
    public function revertToDraft(): bool
    {
        return $this->update(['state' => self::STATE_DRAFT]);
    }

    /**
     * Get a specific data field
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getDataField(string $key, $default = null)
    {
        return data_get($this->data, $key, $default);
    }

    /**
     * Set a specific data field
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setDataField(string $key, $value): void
    {
        $data = $this->data ?? [];
        data_set($data, $key, $value);
        $this->data = $data;
    }

    /**
     * Relationship: Document belongs to User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Document has many Comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * Scope: Filter by type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Filter by state
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $state
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfState($query, string $state)
    {
        return $query->where('state', $state);
    }

    /**
     * Scope: Filter by user
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get only draft documents
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDrafts($query)
    {
        return $query->where('state', self::STATE_DRAFT);
    }

    /**
     * Scope: Get only submitted documents
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubmitted($query)
    {
        return $query->where('state', self::STATE_SUBMITTED);
    }

    /**
     * Scope: Get weekly plans
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWeeklyPlans($query)
    {
        return $query->where('type', self::TYPE_WEEKLY_PLAN);
    }

    /**
     * Scope: Get weekly reports
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWeeklyReports($query)
    {
        return $query->where('type', self::TYPE_WEEKLY_REPORT);
    }

    /**
     * Scope: Get monthly reports
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMonthlyReports($query)
    {
        return $query->where('type', self::TYPE_MONTHLY_REPORT);
    }

    /**
     * Scope: Get weekly minutes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWeeklyMinutes($query)
    {
        return $query->where('type', self::TYPE_WEEKLY_MINUTES);
    }

    /**
     * Scope: Get documents for employees (plans and reports)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmployeeDocuments($query)
    {
        return $query->whereIn('type', [
            self::TYPE_WEEKLY_PLAN,
            self::TYPE_WEEKLY_REPORT,
            self::TYPE_MONTHLY_REPORT,
        ]);
    }

    /**
     * Scope: Get documents for minutes preparer
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinutesDocuments($query)
    {
        return $query->where('type', self::TYPE_WEEKLY_MINUTES);
    }

    /**
     * Scope: Recent documents (last N days)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope: Order by most recent
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Check if a user can create this document type based on their role
     *
     * @param User $user
     * @param string $type
     * @return bool
     */
    public static function canUserCreateType(User $user, string $type): bool
    {
        // Employees can create plans and reports
        if ($user->hasRole('employee')) {
            return in_array($type, [
                self::TYPE_WEEKLY_PLAN,
                self::TYPE_WEEKLY_REPORT,
                self::TYPE_MONTHLY_REPORT,
            ]);
        }

        // Minutes preparer can only create weekly minutes
        if ($user->hasRole('minutes_preparer')) {
            return $type === self::TYPE_WEEKLY_MINUTES;
        }

        // Admin and reviewer can create any type
        if ($user->hasAnyRole(['admin', 'reviewer'])) {
            return true;
        }

        return false;
    }

    /**
     * Get document types available for a specific user role
     *
     * @param User $user
     * @return array
     */
    public static function getAvailableTypesForUser(User $user): array
    {
        $allTypes = self::getTypes();
        $availableTypes = [];

        foreach ($allTypes as $type => $name) {
            if (self::canUserCreateType($user, $type)) {
                $availableTypes[$type] = $name;
            }
        }

        return $availableTypes;
    }

    /**
     * Check if document can be edited (only drafts can be edited)
     *
     * @return bool
     */
    public function canBeEdited(): bool
    {
        return $this->isDraft();
    }

    /**
     * Check if document can be submitted (only drafts can be submitted)
     *
     * @return bool
     */
    public function canBeSubmitted(): bool
    {
        return $this->isDraft();
    }

    /**
     * Check if document can be deleted (only drafts can be deleted)
     *
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        return $this->isDraft();
    }
}
