@extends('layouts.app')

@section('title', 'プロフィール設定 - coachtechフリマ')

@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="text-2xl font-bold text-gray-900 mb-8 text-center">プロフィール設定</h1>

    <form method="POST" action="{{ route('mypage.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- プロフィール画像 --}}
        <div class="mb-8">
            <label class="block text-sm font-bold text-gray-700 mb-3">プロフィール画像</label>
            <div class="flex items-center gap-6">
                {{-- 現在の画像プレビュー --}}
                <div id="image-preview-wrapper" class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
                    @if($user->profile_image)
                        <img id="image-preview" src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <img id="image-preview" src="" alt="" class="w-full h-full object-cover hidden">
                        <div id="image-placeholder" class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    @endif
                </div>
                {{-- ファイル選択ボタン --}}
                <label class="cursor-pointer border border-red-500 text-red-500 text-sm font-bold px-6 py-2 rounded-sm hover:bg-red-50 transition">
                    画像を選択する
                    <input type="file" name="profile_image" id="profile-image-input" accept="image/*" class="hidden">
                </label>
            </div>
            @error('profile_image')
                <p class="mt-2 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- ユーザー名 --}}
        <div class="mb-6">
            <label for="name" class="block text-sm font-bold text-gray-700 mb-2">ユーザー名</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $user->name) }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-sm text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
            @error('name')
                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- 郵便番号 --}}
        <div class="mb-6">
            <label for="zipcode" class="block text-sm font-bold text-gray-700 mb-2">郵便番号</label>
            <input
                type="text"
                id="zipcode"
                name="zipcode"
                value="{{ old('zipcode', $profile->zipcode) }}"
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
                value="{{ old('address', $profile->address) }}"
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
                value="{{ old('building', $profile->building) }}"
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

    <a href="{{ route('mypage.show') }}"
       class="block w-full mt-4 text-center text-sm text-gray-500 hover:text-gray-700 transition">
        マイページに戻る
    </a>
</div>
@endsection

@section('scripts')
<script>
    // 画像選択時のプレビュー
    document.getElementById('profile-image-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('image-preview');
                const placeholder = document.getElementById('image-placeholder');
                preview.src = event.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
