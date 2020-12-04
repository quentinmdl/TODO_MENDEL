<?php

namespace App\Http\Controllers;

use App\Models\{Task, Category, Board};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $user = Auth::user();
        $categories = Category::all();
        return view('tasks.create', ["user" => $user, "categories" => $categories]); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
            'title' => 'required|string|max:255', 
            'description' => 'max:4096', 
            'due_date' => 'required|date|after:today',
            'category_id' => 'default:null|integer|exists:categories,id',
            'board_id' => 'integer|required|exists:boards,id'
        ]);
        // TODO : il faut vérifier que le board appartient bien à l'utilisateur :(
       
        Task::create($validatedData); // Nouvelle méthode création, sans avoir à affecter propriété par propriété
    }

    /**
     * Show the form for creating a new resource from a specific board.
     *
     * @param Board $board : le board pour lequel on crée une tâche
     * 
     * @return \Illuminate\Http\Response
     */
    public function createFromBoard(Board $board)
    {
        //
        $user = Auth::user();
        $categories = Category::all();
        return view('boards.tasks.create', ["user" => $user, "categories" => $categories, 'board' => $board]); 
    }

    /**
     * Store a newly created resource in storage for a given board.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Board $board le board depuis/pour lequel on créé la tâche
     * @return \Illuminate\Http\Response
     */
    public function storeFromBoard(Request $request, Board $board)
    {
        //
        $validatedData = $request->validate([
            'title' => 'required|string|max:255', 
            'description' => 'max:4096', 
            'due_date' => 'required|date|after:today',
            'category_id' => 'default:null|integer|exists:categories,id',
        ]);
        // TODO : il faut vérifier que le board appartient bien à l'utilisateur :(
        $validatedData['board_id'] = $board->id; 
        Task::create($validatedData); // Nouvelle méthode création, sans avoir à affecter propriété par propriété
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        //
    }
}
