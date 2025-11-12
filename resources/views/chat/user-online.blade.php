@extends('layouts.long-page')
@section('title')Chat Center @stop
@section('style')
    <link rel="stylesheet" href="/css/alone/chat.css">
@endsection
@section('content')
    <div class="building-section user-online">
        <div class="container">
            <div class="control col-sx-12">
                <table border-collapse="2px">
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Last seen</th>
                    </tr>
                @foreach($users as $user)
                    <tr>
                        <td>{{$user->name}}</td>
                        <td><span class="{{$user->online ? 'online' : 'offline'}}">{{$user->online ? 'Online' : 'Offline'}}</span></td>
                        <td>{{$user->last_seen}}</td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
