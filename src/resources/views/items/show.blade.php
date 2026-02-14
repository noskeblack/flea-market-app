@extends('layouts.app')

@section('title', $item->name . ' - coachtechフリマ')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col md:flex-row gap-12">

        {{-- ===== 左カラム：商品画像 ===== --}}
        <div class="md:w-1/2">
            <div class="aspect-square bg-gray-200 rounded overflow-hidden">
                @if($item->image)
                    <img
                        src="{{ $item->image_url }}"
                        alt="{{ $item->name }}"
                        class="w-full h-full object-cover"
                    >
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
            </div>
        </div>

        {{-- ===== 右カラム：商品情報 ===== --}}
        <div class="md:w-1/2">

            {{-- 商品名 --}}
            <h1 class="text-2xl font-bold text-gray-900">{{ $item->name }}</h1>

            {{-- ブランド名 --}}
            @if($item->brand)
                <p class="text-sm text-gray-500 mt-1">{{ $item->brand }}</p>
            @endif

            {{-- 価格 --}}
            <p class="text-3xl font-bold text-gray-900 mt-4">
                <span class="text-base font-normal">¥</span>{{ number_format($item->price) }}
                <span class="text-sm font-normal text-gray-500">（税込）</span>
            </p>

            {{-- いいね数・コメント数 --}}
            <div class="flex items-center gap-6 mt-4">
                {{-- いいねアイコン --}}
                <div class="flex flex-col items-center">
                    @auth
                        @if($isFavorited)
                            <form method="POST" action="{{ route('favorites.destroy', ['item_id' => $item->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-yellow-400 hover:text-yellow-500 transition">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('favorites.store', ['item_id' => $item->id]) }}">
                                @csrf
                                <button type="submit" class="text-gray-400 hover:text-yellow-400 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    @else
                        <span class="text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </span>
                    @endauth
                    <span class="text-xs text-gray-500 mt-1">{{ $favoritesCount }}</span>
                </div>

                {{-- コメントアイコン --}}
                <div class="flex flex-col items-center">
                    <span class="text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </span>
                    <span class="text-xs text-gray-500 mt-1">{{ $commentsCount }}</span>
                </div>
            </div>

            {{-- 購入手続きへボタン --}}
            @if(!$item->is_sold)
                <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}"
                   class="block w-full mt-8 bg-red-500 text-white text-center font-bold py-3 rounded-sm hover:bg-red-600 transition">
                    購入手続きへ
                </a>
            @else
                <div class="block w-full mt-8 bg-gray-400 text-white text-center font-bold py-3 rounded-sm cursor-not-allowed">
                    売り切れました
                </div>
            @endif

            {{-- 区切り線 --}}
            <hr class="my-8 border-gray-300">

            {{-- 商品説明 --}}
            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-3">商品説明</h2>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $item->description }}</p>
            </div>

            {{-- 区切り線 --}}
            <hr class="my-8 border-gray-300">

            {{-- 商品の情報 --}}
            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-3">商品の情報</h2>

                {{-- カテゴリー --}}
                <div class="mb-4">
                    <span class="text-sm font-bold text-gray-600">カテゴリー</span>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($item->categories as $category)
                            <span class="inline-block bg-gray-200 text-gray-700 text-xs font-bold px-3 py-1 rounded-full">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- 商品の状態 --}}
                <div>
                    <span class="text-sm font-bold text-gray-600">商品の状態</span>
                    <p class="text-sm text-gray-700 mt-1">{{ $item->condition->name }}</p>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== コメントセクション ===== --}}
    <div class="mt-12 max-w-3xl">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            コメント ({{ $commentsCount }})
        </h2>

        {{-- コメント一覧 --}}
        @foreach($item->comments as $comment)
            <div class="flex gap-4 mb-6">
                {{-- ユーザーアイコン --}}
                <div class="flex-shrink-0">
                    @if($comment->user->profile_image)
                        <img
                            src="{{ asset('storage/' . $comment->user->profile_image) }}"
                            alt="{{ $comment->user->name }}"
                            class="w-10 h-10 rounded-full object-cover"
                        >
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- コメント本文 --}}
                <div class="flex-1">
                    <p class="text-sm font-bold text-gray-800">{{ $comment->user->name }}</p>
                    <p class="text-sm text-gray-700 mt-1 whitespace-pre-wrap">{{ $comment->content }}</p>
                </div>
            </div>
        @endforeach

        {{-- コメント投稿フォーム --}}
        <div class="mt-6">
            <h3 class="text-sm font-bold text-gray-700 mb-2">商品へのコメント</h3>

            @auth
                <form method="POST" action="{{ route('comments.store', ['item_id' => $item->id]) }}">
                    @csrf
                    <textarea
                        name="content"
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                        placeholder="コメントを入力してください"
                    >{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="mt-3 w-full bg-red-500 text-white font-bold py-3 rounded-sm hover:bg-red-600 transition">
                        コメントを送信する
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-500">
                    コメントするには<a href="{{ route('login') }}" class="text-red-500 hover:underline">ログイン</a>してください。
                </p>
            @endauth
        </div>
    </div>
</div>
@endsection
