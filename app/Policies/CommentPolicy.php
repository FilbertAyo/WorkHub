<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can create comments.
     * 
     * Rules:
     * - Only reviewers can create comments
     * - Admin can also create comments
     */
    public function create(User $user): bool
    {
        // Only reviewers and admins can create comments
        return $user->hasAnyRole(['reviewer', 'admin']);
    }

    /**
     * Determine whether the user can update the comment.
     * 
     * Rules:
     * - Users can update their own comments
     * - Admin can update any comment
     */
    public function update(User $user, Comment $comment): bool
    {
        // User owns the comment
        if ($comment->user_id === $user->id) {
            return true;
        }

        // Admin can update any comment
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the comment.
     * 
     * Rules:
     * - Users can delete their own comments
     * - Admin can delete any comment
     */
    public function delete(User $user, Comment $comment): bool
    {
        // User owns the comment
        if ($comment->user_id === $user->id) {
            return true;
        }

        // Admin can delete any comment
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }
}
