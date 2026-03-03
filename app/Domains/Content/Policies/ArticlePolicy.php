<?php

namespace App\Domains\Content\Policies;

use App\Domains\Content\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view articles');
    }

    public function view(User $user, Article $article): bool
    {
        // Todos podem ver artigos publicados
        if ($article->isPublished) {
            return true;
        }

        // Para não publicados, precisa ter permissão
        return $user->hasPermissionTo('view articles') && 
               $article->canBeEditedBy($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create articles');
    }

    public function update(User $user, Article $article): bool
    {
        return $article->canBeEditedBy($user);
    }

    public function delete(User $user, Article $article): bool
    {
        return $article->canBeDeletedBy($user);
    }

    public function restore(User $user, Article $article): bool
    {
        return $user->hasPermissionTo('restore articles');
    }

    public function forceDelete(User $user, Article $article): bool
    {
        return $user->hasPermissionTo('force delete articles');
    }

    public function publish(User $user, Article $article): bool
    {
        return $user->hasPermissionTo('publish articles');
    }

    public function archive(User $user, Article $article): bool
    {
        return $user->hasPermissionTo('archive articles') || 
               $article->canBeEditedBy($user);
    }
}