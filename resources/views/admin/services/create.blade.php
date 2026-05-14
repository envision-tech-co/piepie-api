@extends('admin.layouts.app')

@section('title', 'New Category')
@section('page_title', 'Create Service Category')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-5xl">
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.services.store') }}" method="POST">
        @include('admin.services._form')

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.services.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Cancel</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition">Create</button>
        </div>
    </form>
</div>
@endsection
