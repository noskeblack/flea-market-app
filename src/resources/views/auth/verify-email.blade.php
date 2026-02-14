@extends('layouts.app')

@section('title', 'メール認証 - coachtechフリマ')

@section('content')
<div class="min-h-[calc(100vh-64px)] flex items-center justify-center py-12">
    <div class="w-full max-w-md text-center">
        <h1 class="text-2xl font-bold mb-6">メール認証</h1>

        <p class="text-sm text-gray-600 mb-6">
            登録いただいたメールアドレスに認証メールを送信しました。<br>
            メール内のリンクをクリックして認証を完了してください。
        </p>

        @if (session('status') == 'verification-link-sent')
            <p class="text-green-600 text-sm mb-4">新しい認証リンクを送信しました。</p>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="bg-red-500 text-white font-bold px-8 py-3 rounded-sm hover:bg-red-600 transition">
                認証メールを再送する
            </button>
        </form>
    </div>
</div>
@endsection
