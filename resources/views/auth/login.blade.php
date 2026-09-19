@extends('layouts.public')

@section('title', 'Masuk | BelajarData')

@section('content')
    <div class="page-shell site-container auth-page">
        <div class="auth-card">
            <p class="eyebrow">Akun BelajarData</p>
            <h1>Masuk untuk menyimpan perjalanan belajar.</h1>
            <p class="lede">Belajar tetap bisa dimulai tanpa login. Masuk jika kamu ingin menyimpan progress dan melanjutkan dari perangkat lain.</p>
            <form class="auth-form" method="POST" action="{{ route('login.store') }}">
                @csrf
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
                @error('password')<p class="form-error">{{ $message }}</p>@enderror
                <label class="auth-form__check"><input name="remember" type="checkbox" value="1" @checked(old('remember'))> Ingat saya di perangkat ini</label>
                <button class="button button--primary" type="submit">Masuk</button>
            </form>
            <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Buat akun</a>.</p>
        </div>
    </div>
@endsection
