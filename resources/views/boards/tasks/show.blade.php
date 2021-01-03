@extends('layouts.main')
@section('title', "Board's tasks")

@section('content')
    <p>Ici on va afficher les infos de la tache {{$task->title}}.</p>
    <p>{{$task->description}}</p>
    <p>A faire pour le {{$task->due_date}}</p>
    <p>status {{$task->state}}</p>


    <div>Les utilisateurs assignés à la taches : </div>
    @foreach ($task->assignedUsers as $users)
        <p>{{$user->email}} : {{$user->name}}</p>
    @endforeach


    <div>Voici les commentaires de la tâches : </div>
    <form method="GET" action="{{route('tasks.show', $task)}}">
        @csrf
        <label for="comment"></label>
        <textarea rows="20" cols="5">
        
        </textarea>
        <button type="submit">Ajouter</button>
    </form>

    
@endsection