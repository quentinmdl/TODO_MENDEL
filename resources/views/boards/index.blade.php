@extends('layouts.main')

@section('title', "User's boards")


@section('content')
    <p>Ici on va afficher les boards auxquels appartient l'utilisateur {{$user->name}}.</p>
    <div>Les boards de l'utilisateur</div>
    @foreach ($user->boards as $board)
        <p>Le board {{ $board->title }} : 
                @can('view', $board)
                <a href="{{route('boards.show', $board)}}">Voir</a>
                @endcan
                @can('update', $board)
                <a href="{{route('boards.edit', $board)}}">Edit</a>
                @endcan
                @can('delete', $board)
                <form method='POST' action="{{route('boards.destroy', $board)}}">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
                @endcan
            </p>
    @endforeach

    <form method='GET' action="{{route('boards.create', $board)}}">
        @csrf
        <button type="submit">Créer un board</button>
    </form>
@endsection