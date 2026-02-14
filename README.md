# coachtechフリマ

ユーザー間で商品の出品・購入・コメント・いいねができるフリマアプリケーションです。

## 機能一覧

- **会員登録・ログイン** (Laravel Fortify / メール認証対応)
- **商品一覧表示** (おすすめ・マイリスト切替、検索機能)
- **商品詳細表示** (カテゴリ・状態・コメント・いいね数表示)
- **商品出品** (画像アップロード、カテゴリ・状態選択)
- **商品購入** (Stripe決済、配送先住所変更)
- **コメント機能** (商品への投稿・削除)
- **いいね機能** (お気に入りトグル)
- **プロフィール管理** (プロフィール画像・住所・名前の編集)
- **マイページ** (出品した商品・購入した商品一覧)

## 技術スタック

| 項目 | 技術 |
|---|---|
| 言語 | PHP 8.x |
| フレームワーク | Laravel 8.x |
| 認証 | Laravel Fortify |
| データベース | MySQL 8.0 |
| インフラ | Docker / Docker Compose |
| Webサーバー | Nginx |
| 決済 | Stripe API |
| メール (開発) | MailHog |
| 画像保存 | Laravel Storage (public) |

## アーキテクチャ

```
Controller → Service → Model
```

- **Controller**: リクエスト受付・レスポンス返却のみ
- **Service**: ビジネスロジック (データ加工・条件判定・外部API連携)
- **Model**: Eloquentリレーション・データアクセス
- **FormRequest**: バリデーションロジック (コントローラーから分離)

## ディレクトリ構成 (主要部分)

```
src/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # コントローラー
│   │   └── Requests/        # FormRequestバリデーション
│   ├── Models/              # Eloquentモデル
│   ├── Providers/           # サービスプロバイダ
│   └── Services/            # ビジネスロジック層
├── database/
│   ├── factories/           # テストデータ用ファクトリ
│   ├── migrations/          # マイグレーション
│   └── seeders/             # シーダー
├── resources/
│   └── views/               # Bladeテンプレート
├── routes/
│   └── web.php              # ルーティング
└── storage/
    └── app/public/items/    # 商品画像保存先
```

## データベース設計 (主要テーブル)

| テーブル名 | 説明 |
|---|---|
| `users` | ユーザー情報 |
| `items` | 出品商品 |
| `categories` | カテゴリマスタ |
| `category_item` | カテゴリ×商品 中間テーブル |
| `conditions` | 商品状態マスタ |
| `comments` | コメント |
| `favorites` | いいね (お気に入り) |
| `purchases` | 購入履歴 |
| `addresses` | 配送先住所 |

## 環境構築

### 前提条件

- Docker / Docker Compose がインストール済みであること

### セットアップ手順

```bash
# 1. リポジトリをクローン
git clone <repository-url>
cd flea-market-app

# 2. Dockerコンテナをビルド・起動
docker compose up -d --build

# 3. Composerパッケージのインストール
docker compose exec php composer install

# 4. 環境設定ファイルの準備
docker compose exec php cp .env.example .env

# 5. アプリケーションキーの生成
docker compose exec php php artisan key:generate

# 6. マイグレーションの実行
docker compose exec php php artisan migrate

# 7. シーダーの実行
docker compose exec php php artisan db:seed

# 8. ストレージのシンボリックリンク作成
docker compose exec php php artisan storage:link
```

### アクセスURL

| サービス | URL |
|---|---|
| アプリケーション | http://localhost |
| phpMyAdmin | http://localhost:8080 |
| MailHog | http://localhost:8025 |

## 命名規則

| 対象 | ルール | 例 |
|---|---|---|
| モデル / コントローラー | アッパーキャメルケース | `ItemController`, `UserProfile` |
| マイグレーション / カラム名 | スネークケース | `user_id`, `item_name` |
| バリデーション | FormRequestクラスで分離 | `StoreItemRequest` |
| サービス | アッパーキャメルケース + Service | `ItemService`, `PaymentService` |
