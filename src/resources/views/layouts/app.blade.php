<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'coachtechフリマ')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    {{-- ===== ヘッダー ===== --}}
    <header class="bg-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- ロゴ --}}
                <a href="{{ route('items.index') }}" class="flex-shrink-0">
                    <span class="text-white text-2xl font-bold tracking-wide">
                        COACHTECH
                    </span>
                </a>

                {{-- 検索フォーム --}}
                <div class="flex-1 max-w-lg mx-8">
                    <form action="{{ route('items.index') }}" method="GET">
                        @if(request('tab'))
                            <input type="hidden" name="tab" value="{{ request('tab') }}">
                        @endif
                        <input
                            type="text"
                            name="keyword"
                            value="{{ request('keyword') }}"
                            placeholder="なにをお探しですか？"
                            class="w-full px-4 py-2 rounded-sm text-sm bg-gray-700 text-white placeholder-gray-400 border-none focus:outline-none focus:ring-2 focus:ring-red-500"
                        >
                    </form>
                </div>

                {{-- ナビゲーション --}}
                <nav aria-label="グローバルメニュー">
                    <ul class="flex items-center space-x-6">
                        @auth
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-white text-sm hover:text-gray-300 transition">
                                        ログアウト
                                    </button>
                                </form>
                            </li>
                            <li>
                                <a href="{{ route('mypage.show') }}" class="text-white text-sm hover:text-gray-300 transition">
                                    マイページ
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('login') }}" class="text-white text-sm hover:text-gray-300 transition">
                                    ログイン
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('register') }}" class="text-white text-sm hover:text-gray-300 transition">
                                    会員登録
                                </a>
                            </li>
                        @endauth
                        <li>
                            <a href="{{ route('items.create') }}" class="bg-white text-black text-sm font-bold px-6 py-2 rounded-sm hover:bg-gray-200 transition">
                                出品
                            </a>
                        </li>
                    </ul>
                </nav>

            </div>
        </div>
    </header>

    {{-- ===== フラッシュメッセージ ===== --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 text-sm px-4 py-3 rounded relative" role="alert">
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 text-sm px-4 py-3 rounded relative" role="alert">
                {{ session('error') }}
            </div>
        </div>
    @endif
    @if(session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-blue-100 border border-blue-400 text-blue-700 text-sm px-4 py-3 rounded relative" role="alert">
                {{ session('info') }}
            </div>
        </div>
    @endif

    {{-- ===== メインコンテンツ ===== --}}
    <main class="flex-1">
        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
