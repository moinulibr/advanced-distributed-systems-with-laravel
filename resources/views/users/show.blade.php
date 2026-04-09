@extends('layouts.app')

@section('content')

<div class="card">

<h2>User Profile</h2>

<div style="display:flex;gap:20px;flex-wrap:wrap">

<div style="flex:1">
    <h4>Basic Info</h4>

    <p><b>Name:</b> {{ $user->name }}</p>
    <p><b>Email:</b> {{ $user->email }}</p>
    <p><b>Phone:</b> {{ $user->phone }}</p>
</div>

<div style="flex:1">
    <h4>Profile Info</h4>

    <p><b>Address:</b> {{ $user->profile->address ?? '-' }}</p>
    <p><b>Bio:</b> {{ $user->profile->bio ?? '-' }}</p>
    <p><b>City:</b> {{ $user->profile->city ?? '-' }}</p>
</div>

</div>

<br>

<a href="{{ route('user.index') }}" class="btn btn-primary">Back</a>

</div>

@endsection