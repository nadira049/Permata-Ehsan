@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-8 flex flex-col md:flex-row items-start gap-8">
    <!-- Profile Picture & Basic Info -->
    <div class="flex flex-col items-center w-full md:w-1/3">
        <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default-profile.png') }}"
             alt="Profile Picture"
             class="w-32 h-32 rounded-full object-cover mb-4 border-4 border-gray-200">
        <div class="text-lg font-bold">{{ Auth::user()->username }}</div>
    </div>
    <!-- Profile Details Table -->
    <div class="w-full md:w-2/3">
        <table class="min-w-full bg-white rounded shadow mb-8">
            <tr>
                <th class="px-4 py-2 text-left">Full Name</th>
                <td class="px-4 py-2">{{ Auth::user()->full_name }}</td>
            </tr>
            <tr>
                <th class="px-4 py-2 text-left">Username</th>
                <td class="px-4 py-2">{{ Auth::user()->username }}</td>
            </tr>
            <tr>
                <th class="px-4 py-2 text-left">Email</th>
                <td class="px-4 py-2">{{ Auth::user()->email }}</td>
            </tr>
            <tr>
                <th class="px-4 py-2 text-left">Role</th>
                <td class="px-4 py-2">{{ ucfirst(Auth::user()->role) }}</td>
            </tr>
            <tr>
                <th class="px-4 py-2 text-left">Address</th>
                <td class="px-4 py-2">{{ Auth::user()->address }}</td>
            </tr>
            @if(Auth::user()->role === 'parent')
            <tr>
                <th class="px-4 py-2 text-left align-top">Children</th>
                <td class="px-4 py-2">
                    @php $children = Auth::user()->children; @endphp
                    @if($children->count())
                        <ul class="list-disc pl-4">
                            @foreach($children as $child)
                                <li>{{ $child->name }}
                                    @if($child->class)
                                        ({{ $child->class->name }} - Year {{ $child->class->year }})
                                    @else
                                        (-)
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-gray-400">No children registered.</span>
                    @endif
                </td>
            </tr>
            @endif
        </table>
    </div>
</div>
@endsection
