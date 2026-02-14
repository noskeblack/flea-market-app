@extends('layouts.app')

@section('title', '会員登録 - coachtechフリマ')

@section('content')
<div class="min-h-[calc(100vh-64px)] flex items-center justify-center py-12">
    <div class="w-full max-w-md">
        <h1 class="text-2xl font-bold text-center mb-8">会員登録</h1>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            {{-- ユーザー名 --}}
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 mb-1">ユーザー名</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    required
                >
                @error('name')
                    <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- メールアドレス --}}
            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 mb-1">メールアドレス</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    required
                >
                @error('email')
                    <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- パスワード --}}
            <div>
                <label for="password" class="block text-sm font-bold text-gray-700 mb-1">パスワード</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    required
                >
                @error('password')
                    <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            {{-- パスワード確認 --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1">確認用パスワード</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    required
                >
            </div>

            {{-- 登録ボタン --}}
            <button type="submit" class="w-full bg-red-500 text-white font-bold py-3 rounded-sm hover:bg-red-600 transition">
                登録する
            </button>
        </form>

        <p class="text-center mt-6 text-sm text-gray-500">
            <a href="{{ route('login') }}" class="text-red-500 hover:underline">ログインはこちら</a>
        </p>
    </div>
</div>
@endsection
