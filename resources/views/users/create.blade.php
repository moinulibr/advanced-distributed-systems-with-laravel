@extends('layouts.app')

@section('content')

<div class="card">

<h3>{{ isset($user) ? 'Edit User' : 'Create User' }}</h3>

<form method="POST" action="{{ isset($user) ? route('user.update',$user->id) : route('user.store') }}">
@csrf
@if(isset($user)) @method('PUT') @endif

<div class="flex">
    <input type="text" name="name" placeholder="Name" value="{{ $user->name ?? '' }}">
    <input type="email" name="email" placeholder="Email" value="{{ $user->email ?? '' }}">
</div>

<div class="flex">
    <input type="text" name="phone" placeholder="Phone" value="{{ $user->phone ?? '' }}">
    <input type="password" name="password" placeholder="Password">
</div>

<h4>Profile</h4>

<textarea name="address" placeholder="Address">{{ $user->profile->address ?? '' }}</textarea>

<div class="flex">
    <input type="text" name="bio" placeholder="Bio" value="{{ $user->profile->bio ?? '' }}">
    <input type="text" name="city" placeholder="City" value="{{ $user->profile->city ?? '' }}">
</div>

<br>

<button class="btn btn-success">Save</button>
<a href="{{ route('user.index') }}" class="btn btn-danger">Back</a>

</form>

</div>

@endsection