@extends('layouts.app')

@section('title', '購入手続き - coachtechフリマ')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Stripe / 配送先エラー --}}
    @if($errors->has('stripe') || $errors->has('shipping') || $errors->has('payment_method') || $errors->has('shipping_address'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded">
            @error('stripe')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
            @error('shipping')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
            @error('payment_method')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
            @error('shipping_address')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <form method="POST" action="{{ route('payment.checkout', ['item_id' => $item->id]) }}" id="purchase-form">
        @csrf

        {{-- 配送先が設定されている場合のみ値を送信 --}}
        @if($shippingAddress['zipcode'] && $shippingAddress['address'])
            <input type="hidden" name="shipping_address" value="{{ $shippingAddress['address'] }}">
        @endif

        <div class="flex flex-col lg:flex-row gap-10">

            {{-- ===== 左カラム ===== --}}
            <div class="lg:w-3/5">

                {{-- 商品情報 --}}
                <div class="flex gap-6 pb-8 border-b border-gray-300">
                    <div class="w-32 h-32 bg-gray-200 rounded overflow-hidden flex-shrink-0">
                        @if($item->image)
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col justify-center">
                        <h2 class="text-lg font-bold text-gray-900">{{ $item->name }}</h2>
                        <p class="text-xl font-bold text-gray-900 mt-2">
                            <span class="text-sm font-normal">¥</span>{{ number_format($item->price) }}
                        </p>
                    </div>
                </div>

                {{-- 支払い方法 --}}
                <div class="py-8 border-b border-gray-300">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">支払い方法</h3>
                    <select
                        name="payment_method"
                        id="payment-method"
                        class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm bg-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    >
                        <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>選択してください</option>
                        <option value="1" {{ old('payment_method') == '1' ? 'selected' : '' }}>コンビニ支払い</option>
                        <option value="2" {{ old('payment_method') == '2' ? 'selected' : '' }}>カード支払い</option>
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 配送先 --}}
                <div class="py-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">配送先</h3>
                        <a href="{{ route('address.edit', ['item_id' => $item->id]) }}" class="text-sm text-red-500 hover:underline">
                            変更する
                        </a>
                    </div>
                    <div class="text-sm text-gray-700 space-y-1">
                        @if($shippingAddress['zipcode'])
                            <p>〒{{ $shippingAddress['zipcode'] }}</p>
                            <p>{{ $shippingAddress['address'] }}</p>
                            @if($shippingAddress['building'])
                                <p>{{ $shippingAddress['building'] }}</p>
                            @endif
                        @else
                            <p class="text-gray-400">配送先が登録されていません。<a href="{{ route('address.edit', ['item_id' => $item->id]) }}" class="text-red-500 hover:underline">登録する</a></p>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ===== 右カラム：注文サマリー ===== --}}
            <div class="lg:w-2/5">
                <div class="bg-white rounded shadow-sm border border-gray-200">
                    <table class="w-full text-sm">
                        <tr class="border-b border-gray-200">
                            <td class="px-6 py-4 font-bold text-gray-700">商品代金</td>
                            <td class="px-6 py-4 text-right text-gray-900">¥{{ number_format($item->price) }}</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="px-6 py-4 font-bold text-gray-700">支払い方法</td>
                            <td class="px-6 py-4 text-right text-gray-900" id="summary-payment">
                                {{ old('payment_method') == '1' ? 'コンビニ支払い' : (old('payment_method') == '2' ? 'カード支払い' : '未選択') }}
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- 購入ボタン --}}
                <button
                    type="submit"
                    class="w-full mt-6 bg-red-500 text-white font-bold py-3 rounded-sm hover:bg-red-600 transition"
                >
                    購入する
                </button>

                <a href="{{ route('items.show', ['item_id' => $item->id]) }}"
                   class="block w-full mt-3 text-center text-sm text-gray-500 hover:text-gray-700 transition">
                    商品詳細に戻る
                </a>
            </div>

        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // 支払い方法の選択をサマリーに反映
    document.getElementById('payment-method').addEventListener('change', function() {
        const labels = { '1': 'コンビニ支払い', '2': 'カード支払い' };
        document.getElementById('summary-payment').textContent = labels[this.value] || '未選択';
    });
</script>
@endsection
