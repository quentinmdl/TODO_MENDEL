<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * 
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return Auth::user() == $user; 
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Task,User  $task,User
     * @return mixed
     */
    public function view(User $user, Task $task)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Board  $board
     * @return mixed
     */
    public function create(User $user, Board $board)
    {
        return ($board->users->find($user->id) != null && Auth::user() == $user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Task,User  $task,User
     * @return mixed
     */
    public function update(User $user, Task $task)
    {
       return( $task->board->users->find($user->id) !=null || $task->participants->find($user->id) != null);
    }

    /**
     * Determine whether the user can update the model (only status for assignedUser).
     *
     * @param  \App\Models\Task,User  $task,User
     * @return mixed
     */
    public function updateStatus(User $user, Task $task)
    {
        //
       return($task->assignedUsers->find($user->id) !=null);
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Task,User  $task,User
     * @return mixed
     */
    public function delete(User $user, Task $task)
    {
        return($task->board->owner == $user || $task->participants->find($user->id) !== null);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Task,User  $task,User
     * @return mixed
     */
    public function restore(User $user, Task $task)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Task,User  $task,User
     * @return mixed
     */
    public function forceDelete(User $user, Task $task)
    {
        //
    }
}
