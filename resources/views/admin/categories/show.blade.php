@extends('adminlte::page')

@section('content')

@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

<h1>Product show page</h1>
<a href="{{ route('admin.categories.edit', $category->id) }}">Edit</a>

<form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                {{ __('Delete') }}
            </button>
        </div>
    </div>
</form>

<h3>Name: {{ $category->name }}</h3>
<h3>Parent: {{ $category->parent_id }}</h3>
<h3>Created by: {{ $category->user ? $category->user->name : '' }}</h3>

@endsection
