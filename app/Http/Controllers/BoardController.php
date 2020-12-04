<?php

namespace App\Http\Controllers;

use App\Models\{Board, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Renvoi une vue à laquelle on transmet les boards de l'utilisateurs (ceux auxquels il participe)
        $user = Auth::user();
        return view('boards.index', ['user' => $user]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('boards.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * Permet de stocker un nouveau board pour l'utilisateur dans la base de données
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255', 
            'description' => 'max:4096'
        ]);
        $board = new Board(); 
        $board->title = $validatedData['title'];
        $board->description = $validatedData['description'];
        $board->user_id = Auth::user()->id; 

        $board->save(); 
        return redirect('/boards');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function show(Board $board)
    {
        // On récupère les ids des utilisateurs de la board : 
        $boardUsersIds = $board->users->pluck('id'); 
        // on récupère ici tous les utilisateurs qui ne sont pas dans la board. 
        // Notez le get, qui permet d'obtenir la collection (si on ne le met pas, on obtient un query builder mais la requête n'est pas executée)
        $usersNotInBoard  = User::whereNotIn('id', $boardUsersIds)->get();
        return view('boards.show', ['board' => $board, 'users' => $usersNotInBoard]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function edit(Board $board)
    {
        //
        return view('boards.edit', ['board' => $board]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Board $board)
    {
        //
        $validatedData = $request->validate([
                'title' => 'required|string|max:255', 
                'description' => 'max:4096'
            ]
        );
        $board->title = $validatedData['title']; 
        $board->description = $validatedData['description']; 
        $board->update(); 

        return redirect('/boards');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function destroy(Board $board)
    {
        //
        $board->delete();
        return redirect('/boards');
    }
}
