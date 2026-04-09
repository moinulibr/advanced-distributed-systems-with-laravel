@extends('layouts.app')

@section('content')

<div class="card">

<h2>Edit User</h2>

<form method="POST" action="{{ route('user.update',$user->id) }}">
@csrf
@method('PUT')

<div class="flex">
    <div style="width:100%">
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name',$user->name) }}">
    </div>

    <div style="width:100%">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email',$user->email) }}">
    </div>
</div>

<div class="flex">
    <div style="width:100%">
        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone',$user->phone) }}">
    </div>

    <div style="width:100%">
        <label>Password</label>
        <input type="password" name="password">
    </div>
</div>

<h4>Profile</h4>

<label>Address</label>
<textarea name="address">{{ old('address',$user->profile->address ?? '') }}</textarea>

<div class="flex">
    <input type="text" name="bio" value="{{ old('bio',$user->profile->bio ?? '') }}">
    <input type="text" name="city" value="{{ old('city',$user->profile->city ?? '') }}">
</div>

<br>

<button class="btn btn-success">Update</button>
<a href="{{ route('user.index') }}" class="btn btn-danger">Back</a>

</form>

</div>

@endsection