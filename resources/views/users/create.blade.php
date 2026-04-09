@extends('layouts.app')

@section('content')

<div class="card">

<h2>Create User</h2>

@if ($errors->any())
    <div style="background:#ffecec;padding:10px;border-radius:6px;margin-bottom:10px;">
        <ul style="margin:0;">
            @foreach ($errors->all() as $error)
                <li style="color:red;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('user.store') }}">
@csrf

<div class="flex">
    <div style="width:100%">
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name') }}">
    </div>

    <div style="width:100%">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">
    </div>
</div>

<div class="flex">
    <div style="width:100%">
        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone') }}">
    </div>

    <div style="width:100%">
        <label>Password</label>
        <input type="password" name="password">
    </div>
</div>

<h4>Profile</h4>

<label>Address</label>
<textarea name="address">{{ old('address') }}</textarea>

<div class="flex">
    <div style="width:100%">
        <label>Bio</label>
        <input type="text" name="bio" value="{{ old('bio') }}">
    </div>

    <div style="width:100%">
        <label>City</label>
        <input type="text" name="city" value="{{ old('city') }}">
    </div>
</div>

<br>

<button class="btn btn-success">Save</button>
<a href="{{ route('user.index') }}" class="btn btn-danger">Back</a>

</form>

</div>

@endsection