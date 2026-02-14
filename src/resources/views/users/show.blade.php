@extends('layouts.app')

@section('title', 'マイページ - coachtechフリマ')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ===== プロフィールヘッダー ===== --}}
    <div class="flex items-center gap-6 mb-8">
        {{-- プロフィール画像 --}}
        <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
            @if($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            @endif
        </div>

        {{-- ユーザー名 + 編集ボタン --}}
        <div class="flex items-center gap-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
            <a href="{{ route('mypage.edit') }}"
               class="border border-red-500 text-red-500 text-sm font-bold px-6 py-2 rounded-sm hover:bg-red-50 transition">
                プロフィールを編集
            </a>
        </div>
    </div>

    {{-- ===== タブ切り替え ===== --}}
    <div class="flex border-b border-gray-300 mb-6">
        <a href="{{ route('mypage.show', ['tab' => 'sell']) }}"
           class="px-6 py-3 text-base font-semibold {{ $tab === 'sell' ? 'text-red-500 border-b-2 border-red-500' : 'text-gray-500 hover:text-red-500' }}">
            出品した商品
        </a>
        <a href="{{ route('mypage.show', ['tab' => 'buy']) }}"
           class="ml-4 px-6 py-3 text-base font-semibold {{ $tab === 'buy' ? 'text-red-500 border-b-2 border-red-500' : 'text-gray-500 hover:text-red-500' }}">
            購入した商品
        </a>
    </div>

    {{-- ===== 商品一覧グリッド ===== --}}
    @if($items->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($items as $item)
                <a href="{{ route('items.show', $item->id) }}" class="block bg-white rounded-lg shadow-md overflow-hidden relative">
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                        @if($item->image)
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-gray-400">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        @if($item->is_sold)
                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                <span class="text-white text-xl font-bold">Sold</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm font-semibold text-gray-800 truncate">{{ $item->name }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center text-gray-500 py-12">
            @if($tab === 'buy')
                購入した商品はありません。
            @else
                出品した商品はありません。
            @endif
        </div>
    @endif

</div>
@endsection
