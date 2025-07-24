@extends('layouts.app')

@section('content')
    <h1>{{ $learningMaterial->title }}</h1>
    <p>{{ $learningMaterial->content }}</p>
    <p>File Path: {{ $learningMaterial->file_path }}</p>
    <a href="{{ route('learning-materials.index') }}">Back to Learning Materials</a>
@endsection 