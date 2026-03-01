<?php
namespace App\Policies;

use App\Domain\Content\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /* public function viewAny(User $user): bool
    {
        return $user->can('view articles');
    }

    public function view(User $user, Article $article): bool
    {
        if ($user->can('view articles')) {
            return true;
        }

        return $article->isPublished();
    }

    public function create(User $user): bool
    {
        return $user->can('create articles');
    }

    public function update(User $user, Article $article): bool
    {
        if ($user->can('edit any articles')) {
            return true;
        }

        if ($user->can('edit articles') && $user->id === $article->author_id) {
            return $article->canBeEdited();
        }

        return false;
    }

    public function delete(User $user, Article $article): bool
    {
        if ($user->can('delete any articles')) {
            return true;
        }

        if ($user->can('delete articles') && $user->id === $article->author_id) {
            return $article->isDraft();
        }

        return false;
    }

    public function publish(User $user, Article $article): bool
    {
        return $user->can('publish articles') && $article->canBeEdited();
    }

    public function archive(User $user, Article $article): bool
    {
        return $user->can('archive articles');
    }

    public function restore(User $user, Article $article): bool
    {
        return $user->can('restore articles');
    }

    public function forceDelete(User $user, Article $article): bool
    {
        return $user->can('force delete articles');
    } */
}