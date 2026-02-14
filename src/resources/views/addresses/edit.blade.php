@extends('layouts.app')

@section('title', '住所の変更 - coachtechフリマ')

@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="text-2xl font-bold text-gray-900 mb-8 text-center">住所の変更</h1>

    <form method="POST" action="{{ route('address.update', ['item_id' => $item->id]) }}">
        @csrf

        {{-- 郵便番号 --}}
        <div class="mb-6">
            <label for="zipcode" class="block text-sm font-bold text-gray-700 mb-2">郵便番号</label>
            <input
                type="text"
                id="zipcode"
                name="zipcode"
                value="{{ old('zipcode', $shippingAddress['zipcode']) }}"
                placeholder="例: 150-0001"
                class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
            @error('zipcode')
                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="mb-6">
            <label for="address" class="block text-sm font-bold text-gray-700 mb-2">住所</label>
            <input
                type="text"
                id="address"
                name="address"
                value="{{ old('address', $shippingAddress['address']) }}"
                placeholder="例: 東京都渋谷区神宮前1-1-1"
                class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
            @error('address')
                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="mb-8">
            <label for="building" class="block text-sm font-bold text-gray-700 mb-2">建物名</label>
            <input
                type="text"
                id="building"
                name="building"
                value="{{ old('building', $shippingAddress['building']) }}"
                placeholder="例: マンション101"
                class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
            @error('building')
                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- 更新ボタン --}}
        <button
            type="submit"
            class="w-full bg-red-500 text-white font-bold py-3 rounded-sm hover:bg-red-600 transition"
        >
            更新する
        </button>
    </form>

    <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}"
       class="block w-full mt-4 text-center text-sm text-gray-500 hover:text-gray-700 transition">
        購入画面に戻る
    </a>

</div>
@endsection
