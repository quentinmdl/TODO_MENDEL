<?php

namespace App\Http\Controllers;

use App\Models\{Task, Comment,User};
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Ajoute un commantaire dans une tâche en utilisant le modèle comment
     *
     * @param Task $task la tâche pour lequel on ajoute le commentaire
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Task $task) {
        $validatedData = $request->validate([
            'comment' => 'required|text|exists:user_id,task_id',
        ]);
        $comment = new Comment(); 
        $comment->user_id = $validatedData['user_id']; 
        $comment->task_id = $task->id; 
        $comment->save(); 
        return redirect(route('tasks.show', $task));
    }


    public function destroy(Comment $comment) { 
        $task = $Comment->task;
        $comment(->delete();
        return redirect(route('tasks.show', $task));
    }
}
