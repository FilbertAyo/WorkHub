<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     * 
     * Rules:
     * - Employees: Can view their own documents
     * - Reviewers: Can view all documents
     * - Minutes Preparer: Can view their own documents
     * - Admin: Can view all documents
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view documents (filtered by ownership in controller)
        // Reviewers and admins can see all, others see only their own
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * 
     * Rules:
     * - Employees: Can view their own documents
     * - Reviewers: Can view all documents
     * - Minutes Preparer: Can view their own documents
     * - Admin: Can view all documents
     */
    public function view(User $user, Document $document): bool
    {
        // User owns the document
        if ($document->user_id === $user->id) {
            return true;
        }

        // Reviewers and admins can view all documents
        if ($user->hasAnyRole(['reviewer', 'admin'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     * 
     * Rules:
     * - Employees: Can create weekly_plan, weekly_report, monthly_report
     * - Minutes Preparer: Can create weekly_minutes only
     * - Reviewers: Cannot create (read-only)
     * - Admin: Can create any type
     */
    public function create(User $user): bool
    {
        // Admin can create any type
        if ($user->hasRole('admin')) {
            return true;
        }

        // Reviewers cannot create documents (read-only)
        if ($user->hasRole('reviewer')) {
            return false;
        }

        // Check if user has any available document types
        $availableTypes = Document::getAvailableTypesForUser($user);
        return !empty($availableTypes);
    }

    /**
     * Determine whether the user can create a specific document type.
     */
    public function createType(User $user, string $type): bool
    {
        // Admin can create any type
        if ($user->hasRole('admin')) {
            return true;
        }

        // Reviewers cannot create documents (read-only)
        if ($user->hasRole('reviewer')) {
            return false;
        }

        // Check if user can create this specific type
        return Document::canUserCreateType($user, $type);
    }

    /**
     * Determine whether the user can update the model.
     * 
     * Rules:
     * - Employees: Can update their own draft documents
     * - Minutes Preparer: Can update their own draft documents
     * - Reviewers: Cannot update (read-only)
     * - Admin: Can update any draft document
     */
    public function update(User $user, Document $document): bool
    {
        // Reviewers cannot update documents (read-only)
        if ($user->hasRole('reviewer')) {
            return false;
        }

        // Only draft documents can be updated
        if (!$document->isDraft()) {
            return false;
        }

        // User owns the document
        if ($document->user_id === $user->id) {
            return true;
        }

        // Admin can update any draft document
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * 
     * Rules:
     * - Employees: Can delete their own draft documents
     * - Minutes Preparer: Can delete their own draft documents
     * - Reviewers: Cannot delete (read-only)
     * - Admin: Can delete any draft document
     */
    public function delete(User $user, Document $document): bool
    {
        // Reviewers cannot delete documents (read-only)
        if ($user->hasRole('reviewer')) {
            return false;
        }

        // Only draft documents can be deleted
        if (!$document->isDraft()) {
            return false;
        }

        // User owns the document
        if ($document->user_id === $user->id) {
            return true;
        }

        // Admin can delete any draft document
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can submit the document.
     * 
     * Rules:
     * - Employees: Can submit their own draft documents
     * - Minutes Preparer: Can submit their own draft documents
     * - Reviewers: Cannot submit (read-only)
     * - Admin: Can submit any draft document
     */
    public function submit(User $user, Document $document): bool
    {
        // Reviewers cannot submit documents (read-only)
        if ($user->hasRole('reviewer')) {
            return false;
        }

        // Only draft documents can be submitted
        if (!$document->isDraft()) {
            return false;
        }

        // User owns the document
        if ($document->user_id === $user->id) {
            return true;
        }

        // Admin can submit any draft document
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        // Only admin can restore deleted documents
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        // Only admin can permanently delete documents
        return $user->hasRole('admin');
    }
}
