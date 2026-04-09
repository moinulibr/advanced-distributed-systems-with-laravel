@extends('layouts.app')

@section('content')

<div class="card">

    <div class="top-bar">
        <h3>👤 Users</h3>

        <div class="search-box">
            <form method="GET" action="{{ route('user.index') }}" class="search-box">
                <input type="text" name="email" placeholder="Search by email..." value="{{ request('email') }}">
                <button class="btn btn-primary">Search</button>
            </form>

            <a href="{{ route('user.create') }}" class="btn btn-success">+ Add</a>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            @forelse($users as $i => $user)
                <tr>
                    <td>{{ $i+1 }}</td>

                    <td>
                        <strong>{{ $user->name }}</strong><br>
                        <small>{{ $user->email }}</small>
                    </td>

                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->profile->city ?? '-' }}</td>

                    <td>
                        <div class="action-btns">
                            <a href="{{ route('user.show',$user->phone) }}" class="btn btn-primary">View</a>
                            <a href="{{ route('user.create') }}?edit={{ $user->id }}" class="btn btn-warning">Edit</a>

                            <button onclick="deleteUser({{ $user->id }})" class="btn btn-danger">Delete</button>

                            <form id="delete-form-{{ $user->id }}" method="POST" action="{{ route('user.delete',$user->id) }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">No Data</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:15px;">
            {{-- {{ $users->links() }} --}}
        </div>
    </div>

</div>

@endsection