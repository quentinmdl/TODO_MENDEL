@extends('layouts.main')

@section('title', "Add a task for a board")


@section('content')
    <h2>Ajouter une tâche</h2>
    <form action="{{route('tasks.update', [$board, $task])}}" method="POST">
        @method('PUT')
        @csrf
        <label for="title">Title</label>
        <input type="text" name='title' id ='title' class="@error('title') is-invalid @enderror" value="{{$task->title}}" required><br>
        @error('title')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        <label for="description">Description</label>
        <input type="text" name='description' id ='description' class="@error('description') is-invalid @enderror" value="{{$task->description}}" ><br>
        <label for="due_date">Date de fin</label>
        <input type="date" name='due_date' id ='due_date' class="@error('due_date') is-invalid @enderror" value="{{$task->due_date}}" ><br>
        {{-- On ne gère pas l'état lors de la création  --}}

        <select name="state" id="state" value="{{$task->state}}">
            @foreach(['todo', 'ongoing', 'done'] as $state) 
                <option value="{{$state}}">{{$state}}</option>
            @endforeach
        </select>
        <label for="category">Category</label>
        <select name="category_id" id="category_id" >
            @foreach($categories as $category) 
                <option value="{{$category->id}}">{{$category->name}}</option>
            @endforeach
        </select>
        <br>

        <button type="submit">Save</button>
    </form>
    
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
@endsection