@extends('layouts.app')

@section('content')

<div class="card">

<h3>User Details</h3>

<p><strong>Name:</strong> {{ $user->name }}</p>
<p><strong>Email:</strong> {{ $user->email }}</p>
<p><strong>Phone:</strong> {{ $user->phone }}</p>

<hr>

<h4>Profile</h4>

<p><strong>Address:</strong> {{ $user->profile->address ?? '-' }}</p>
<p><strong>Bio:</strong> {{ $user->profile->bio ?? '-' }}</p>
<p><strong>City:</strong> {{ $user->profile->city ?? '-' }}</p>

<br>

<a href="{{ route('user.index') }}" class="btn btn-primary">Back</a>

</div>

@endsection