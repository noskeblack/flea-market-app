@extends('layouts.app')

@section('title', '商品の出品 - coachtechフリマ')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="text-2xl font-bold text-gray-900 mb-8 text-center">商品の出品</h1>

    <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- ===== 商品画像 ===== --}}
        <div class="mb-8">
            <label class="block text-sm font-bold text-gray-700 mb-3">商品画像</label>
            <div id="drop-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-red-400 transition relative">
                {{-- プレビュー --}}
                <div id="preview-container" class="hidden mb-4">
                    <img id="image-preview" src="" alt="プレビュー" class="max-h-64 mx-auto rounded">
                </div>
                {{-- アップロードUI --}}
                <div id="upload-ui">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm text-gray-500">クリックまたはドラッグ＆ドロップで画像をアップロード</p>
                </div>
                <input type="file" name="image" id="item-image-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
            </div>
            @error('image')
                <p class="mt-2 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <hr class="border-gray-200 mb-8">

        <h2 class="text-lg font-bold text-gray-900 mb-6">商品の詳細</h2>

        {{-- ===== カテゴリー（複数選択チェックボックス） ===== --}}
        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-3">カテゴリー</label>
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $category)
                    <label class="inline-flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            name="categories[]"
                            value="{{ $category->id }}"
                            class="hidden peer"
                            {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                        >
                        <span class="px-4 py-2 text-sm rounded-full border border-gray-300 text-gray-600
                                     peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
                                     hover:bg-gray-100 transition select-none">
                            {{ $category->name }}
                        </span>
                    </label>
                @endforeach
            </div>
            @error('categories')
                <p class="mt-2 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- ===== 商品の状態 ===== --}}
        <div class="mb-8">
            <label for="condition_id" class="block text-sm font-bold text-gray-700 mb-2">商品の状態</label>
            <select
                id="condition_id"
                name="condition_id"
                class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm bg-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
                <option value="" disabled {{ old('condition_id') ? '' : 'selected' }}>選択してください</option>
                @foreach($conditions as $condition)
                    <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                        {{ $condition->name }}
                    </option>
                @endforeach
            </select>
            @error('condition_id')
                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        <hr class="border-gray-200 mb-8">

        <h2 class="text-lg font-bold text-gray-900 mb-6">商品名と説明</h2>

        {{-- ===== 商品名 ===== --}}
        <div class="mb-6">
            <label for="name" class="block text-sm font-bold text-gray-700 mb-2">商品名</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
            @error('name')
                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- ===== ブランド名 ===== --}}
        <div class="mb-6">
            <label for="brand" class="block text-sm font-bold text-gray-700 mb-2">ブランド名</label>
            <input
                type="text"
                id="brand"
                name="brand"
                value="{{ old('brand') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
            @error('brand')
                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- ===== 商品の説明 ===== --}}
        <div class="mb-6">
            <label for="description" class="block text-sm font-bold text-gray-700 mb-2">商品の説明</label>
            <textarea
                id="description"
                name="description"
                rows="6"
                class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- ===== 販売価格 ===== --}}
        <div class="mb-8">
            <label for="price" class="block text-sm font-bold text-gray-700 mb-2">販売価格</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm">¥</span>
                <input
                    type="number"
                    id="price"
                    name="price"
                    value="{{ old('price') }}"
                    min="0"
                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                >
            </div>
            @error('price')
                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- ===== 出品ボタン ===== --}}
        <button
            type="submit"
            class="w-full bg-red-500 text-white font-bold py-3 rounded-sm hover:bg-red-600 transition"
        >
            出品する
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // 画像プレビュー
    document.getElementById('item-image-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('image-preview').src = event.target.result;
                document.getElementById('preview-container').classList.remove('hidden');
                document.getElementById('upload-ui').classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
