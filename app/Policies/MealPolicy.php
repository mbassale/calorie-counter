<?php

namespace App\Policies;

use App\Meal;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MealPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isManager()) {
            return false;
        }
        return null;
    }

    /**
     * Determine whether the user can view any meals.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the meal.
     *
     * @param  \App\User  $user
     * @param  \App\Meal  $meal
     * @return mixed
     */
    public function view(User $user, Meal $meal)
    {
        return $user->id == $meal->user_id;
    }

    /**
     * Determine whether the user can create meals.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the meal.
     *
     * @param  \App\User  $user
     * @param  \App\Meal  $meal
     * @return mixed
     */
    public function update(User $user, Meal $meal)
    {
        return $user->id == $meal->user_id;
    }

    /**
     * Determine whether the user can delete the meal.
     *
     * @param  \App\User  $user
     * @param  \App\Meal  $meal
     * @return mixed
     */
    public function delete(User $user, Meal $meal)
    {
        return $user->id == $meal->user_id;
    }

    /**
     * Determine whether the user can restore the meal.
     *
     * @param  \App\User  $user
     * @param  \App\Meal  $meal
     * @return mixed
     */
    public function restore(User $user, Meal $meal)
    {
        return $user->id == $meal->user_id;
    }

    /**
     * Determine whether the user can permanently delete the meal.
     *
     * @param  \App\User  $user
     * @param  \App\Meal  $meal
     * @return mixed
     */
    public function forceDelete(User $user, Meal $meal)
    {
        return $user->id == $meal->user_id;
    }
}
