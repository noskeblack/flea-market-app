@extends('layouts.app')

@section('title', '商品一覧 - coachtechフリマ')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- タブ切り替え --}}
    <div class="flex border-b border-gray-300 mb-8">
        <a href="{{ route('items.index', ['tab' => 'recommend', 'keyword' => $keyword]) }}"
           class="px-8 py-3 text-sm font-bold transition
                  {{ $tab !== 'mylist' ? 'text-red-500 border-b-2 border-red-500' : 'text-gray-500 hover:text-gray-700' }}">
            おすすめ
        </a>
        <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => $keyword]) }}"
           class="px-8 py-3 text-sm font-bold transition
                  {{ $tab === 'mylist' ? 'text-red-500 border-b-2 border-red-500' : 'text-gray-500 hover:text-gray-700' }}">
            マイリスト
        </a>
    </div>

    {{-- マイリストで未ログイン時のメッセージ --}}
    @if($tab === 'mylist' && !auth()->check())
        <div class="text-center py-16">
            <p class="text-gray-500 text-sm">マイリストを見るには<a href="{{ route('login') }}" class="text-red-500 underline">ログイン</a>してください。</p>
        </div>
    @elseif($items->isEmpty())
        <div class="text-center py-16">
            <p class="text-gray-500 text-sm">商品が見つかりませんでした。</p>
        </div>
    @else
        {{-- 商品カード一覧 --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @foreach($items as $item)
                <a href="{{ route('items.show', ['item_id' => $item->id]) }}" class="group block">
                    <div class="relative bg-white rounded overflow-hidden shadow-sm hover:shadow-md transition">
                        {{-- 商品画像 --}}
                        <div class="aspect-square bg-gray-200 overflow-hidden">
                            @if($item->image)
                                <img
                                    src="{{ $item->image_url }}"
                                    alt="{{ $item->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Sold ラベル --}}
                            @if($item->is_sold)
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                    <span class="text-white text-lg font-bold">Sold</span>
                                </div>
                            @endif
                        </div>

                        {{-- 商品名 --}}
                        <div class="p-3">
                            <p class="text-sm text-gray-800 truncate">{{ $item->name }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection
