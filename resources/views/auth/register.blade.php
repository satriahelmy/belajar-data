@extends('layouts.public')

@section('title', 'Buat akun | BelajarData')

@section('content')
    <div class="page-shell site-container auth-page">
        <div class="auth-card">
            <p class="eyebrow">Akun BelajarData</p>
            <h1>Simpan perjalanan belajar kamu.</h1>
            <p class="lede">Buat akun untuk menyimpan progress. Materi tetap terbuka tanpa prerequisite lock.</p>
            <form class="auth-form" method="POST" action="{{ route('register.store') }}">
                @csrf
                <label for="name">Nama</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required>
                @error('password')<p class="form-error">{{ $message }}</p>@enderror
                <label for="password_confirmation">Ulangi password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                <button class="button button--primary" type="submit">Buat akun</button>
            </form>
            <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>.</p>
        </div>
    </div>
@endsection
