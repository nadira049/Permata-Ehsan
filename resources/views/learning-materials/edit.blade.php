@extends('layouts.app')

@section('content')
    <h1>Edit Learning Material</h1>
    <form action="{{ route('learning-materials.update', $learningMaterial) }}" method="POST">
        @csrf
        @method('PUT')
        <label>Title: <input type="text" name="title" value="{{ $learningMaterial->title }}"></label><br>
        <label>Content: <textarea name="content">{{ $learningMaterial->content }}</textarea></label><br>
        <label>File Path: <input type="text" name="file_path" value="{{ $learningMaterial->file_path }}"></label><br>
        <button type="submit">Update</button>
    </form>
@endsection 