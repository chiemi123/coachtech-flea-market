# coachtech-flea-market

`coachtech-flea-market` は、ユーザー同士が商品を売買できるフリーマーケットアプリです。  
ログイン後、商品の **出品・購入** が可能になります。

## **主な機能（ユーザー向け）**

-   ユーザー登録・ログイン
-   商品の出品
-   商品の購入
-   お気に入り登録（いいね機能）
-   コメント機能
-   商品検索機能

簡単に商品を売買できるマーケットプレイスを提供します！

## 作成した目的

模擬案件を通して実践に近い開発経験を積み、定義された要件を実装する能力を身につけること。

## **技術的な機能一覧（開発者向け）**

-   **認証機能:** Laravel Fortify（ログイン、メール認証）
    -   メール受信テストは MailHog を使用
-   **商品管理:** 商品一覧・詳細ページでの検索機能
-   **決済機能:** Stripe を用いた商品購入処理
-   **その他:** Docker による開発環境構築、Webhooks による決済処理管理

## 使用技術（実行環境）

-   PHP7.4
-   Laravel8.83.27
-   MySQL8.0.26

## テーブル設計

本プロジェクトの詳細なテーブル設計は、以下の Google スプレッドシートにまとめています。

**[テーブル設計シート (Google スプレッドシート)](https://docs.google.com/spreadsheets/d/1AUlHz8zNAvwpKfZsBWg9MHVZcBCvu6NGlVGuadmne2k/edit?gid=1188247583#gid=1188247583)**

### **1. users テーブル（ユーザー情報）**

| カラム名          | データ型     | NOT NULL | 主キー | 外部キー | 説明                           |
| ----------------- | ------------ | -------- | ------ | -------- | ------------------------------ |
| id                | BIGINT       | ○        | ○      |          | ユーザー ID                    |
| name              | VARCHAR(255) | ○        |        |          | ユーザー名                     |
| email             | VARCHAR(255) | ○        |        |          | メールアドレス（ユニーク制約） |
| profile_image     | VARCHAR(255) |          |        |          | プロフィール画像               |
| username          | VARCHAR(255) | ○        |        |          | ユーザー名（ニックネームなど） |
| postal_code       | VARCHAR(255) |          |        |          | 郵便番号                       |
| address           | VARCHAR(255) |          |        |          | 住所                           |
| building_name     | VARCHAR(255) |          |        |          | 建物名                         |
| password          | VARCHAR(255) | ○        |        |          | ハッシュ化されたパスワード     |
| profile_completed | TINYINT(1)   | ○        |        |          | プロフィール完了フラグ         |
| created_at        | TIMESTAMP    |          |        |          | 作成日時                       |
| updated_at        | TIMESTAMP    |          |        |          | 更新日時                       |

---

### **2. conditions テーブル（商品の状態）**

| カラム名   | データ型     | NOT NULL | 主キー | 外部キー | 説明                       |
| ---------- | ------------ | -------- | ------ | -------- | -------------------------- |
| id         | BIGINT       | ○        | ○      |          | 商品状態の ID              |
| name       | VARCHAR(255) | ○        |        |          | 状態名（良好・傷ありなど） |
| created_at | TIMESTAMP    |          |        |          | 作成日時                   |
| updated_at | TIMESTAMP    |          |        |          | 更新日時                   |

---

### **3. addresses テーブル（配送先住所）**

| カラム名      | データ型     | NOT NULL | 主キー | 外部キー  | 説明                    |
| ------------- | ------------ | -------- | ------ | --------- | ----------------------- |
| id            | BIGINT       | ○        | ○      |           | 住所 ID                 |
| user_id       | BIGINT       | ○        |        | users(id) | ユーザー ID（外部キー） |
| postal_code   | VARCHAR(8)   | ○        |        |           | 郵便番号                |
| address       | VARCHAR(255) | ○        |        |           | 住所                    |
| building_name | VARCHAR(255) |          |        |           | 建物名                  |
| created_at    | TIMESTAMP    |          |        |           | 作成日時                |
| updated_at    | TIMESTAMP    |          |        |           | 更新日時                |

---

### **4. items テーブル（商品情報）**

| カラム名       | データ型     | NOT NULL | 主キー | 外部キー       | 説明                      |
| -------------- | ------------ | -------- | ------ | -------------- | ------------------------- |
| id             | BIGINT       | ○        | ○      |                | 商品 ID                   |
| user_id        | BIGINT       | ○        |        | users(id)      | 出品者 ID（外部キー）     |
| condition_id   | BIGINT       | ○        |        | conditions(id) | 商品の状態 ID（外部キー） |
| name           | VARCHAR(255) | ○        |        |                | 商品名                    |
| brand          | VARCHAR(255) | ○        |        |                | ブランド名                |
| description    | VARCHAR(255) | ○        |        |                | 商品説明                  |
| price          | INTEGER      | ○        |        |                | 価格                      |
| item_image     | VARCHAR(255) |          |        |                | 商品画像                  |
| sold_out       | TINYINT(1)   | ○        |        |                | 売り切れフラグ            |
| likes_count    | INTEGER      | ○        |        |                | いいね数                  |
| comments_count | INTEGER      | ○        |        |                | コメント数                |
| created_at     | TIMESTAMP    |          |        |                | 作成日時                  |
| updated_at     | TIMESTAMP    |          |        |                | 更新日時                  |

---

### **5. categories テーブル（カテゴリ情報）**

| カラム名   | データ型     | NOT NULL | 主キー | 外部キー | 説明        |
| ---------- | ------------ | -------- | ------ | -------- | ----------- |
| id         | BIGINT       | ○        | ○      |          | カテゴリ ID |
| name       | VARCHAR(255) | ○        |        |          | カテゴリ名  |
| created_at | TIMESTAMP    |          |        |          | 作成日時    |
| updated_at | TIMESTAMP    |          |        |          | 更新日時    |

---

### **6. category_items テーブル（カテゴリと商品を紐づける中間テーブル）**

| カラム名    | データ型  | NOT NULL | 主キー | 外部キー       | 説明                    |
| ----------- | --------- | -------- | ------ | -------------- | ----------------------- |
| id          | BIGINT    | ○        | ○      |                | カテゴリアイテム ID     |
| category_id | BIGINT    | ○        |        | categories(id) | カテゴリ ID（外部キー） |
| item_id     | BIGINT    | ○        |        | items(id)      | 商品 ID（外部キー）     |
| created_at  | TIMESTAMP |          |        |                | 作成日時                |
| updated_at  | TIMESTAMP |          |        |                | 更新日時                |

---

### **7. purchases テーブル（購入情報）**

| カラム名       | データ型     | NOT NULL | 主キー | 外部キー      | 説明                                           |
| -------------- | ------------ | -------- | ------ | ------------- | ---------------------------------------------- |
| id             | BIGINT       | ○        | ○      |               | 購入 ID                                        |
| user_id        | BIGINT       | ○        |        | users(id)     | 購入者 ID（外部キー）                          |
| item_id        | BIGINT       | ○        |        | items(id)     | 購入した商品 ID（外部キー）                    |
| address_id     | BIGINT       | ○        |        | addresses(id) | 配送先住所 ID（外部キー）                      |
| payment_method | VARCHAR(255) | ○        |        |               | 支払い方法                                     |
| status         | VARCHAR(255) | ○        |        |               | 購入ステータス                                 |
| transaction_id | VARCHAR(255) |          |        |               | 取引 ID（決済プロバイダのトランザクション ID） |
| created_at     | TIMESTAMP    |          |        |               | 作成日時                                       |
| updated_at     | TIMESTAMP    |          |        |               | 更新日時                                       |

---

### **8. likes テーブル（お気に入り情報）**

| カラム名   | データ型  | NOT NULL | 主キー | 外部キー  | 説明                    |
| ---------- | --------- | -------- | ------ | --------- | ----------------------- |
| id         | BIGINT    | ○        | ○      |           | いいね ID               |
| user_id    | BIGINT    | ○        |        | users(id) | ユーザー ID（外部キー） |
| item_id    | BIGINT    | ○        |        | items(id) | 商品 ID（外部キー）     |
| created_at | TIMESTAMP |          |        |           | 作成日時                |
| updated_at | TIMESTAMP |          |        |           | 更新日時                |

---

### **9. comments テーブル（コメント情報）**

| カラム名   | データ型     | NOT NULL | 主キー | 外部キー  | 説明                                    |
| ---------- | ------------ | -------- | ------ | --------- | --------------------------------------- |
| id         | BIGINT       | ○        | ○      |           | コメント ID                             |
| user_id    | BIGINT       | ○        |        | users(id) | コメント投稿者のユーザー ID（外部キー） |
| item_id    | BIGINT       | ○        |        | items(id) | コメント対象の商品 ID（外部キー）       |
| content    | VARCHAR(255) | ○        |        |           | コメント内容                            |
| created_at | TIMESTAMP    |          |        |           | 作成日時                                |
| updated_at | TIMESTAMP    |          |        |           | 更新日時                                |

---

## ER 図

![alt text](.drawio.png)

## 環境構築

### **1.リポジトリのクローン**

GitHub からプロジェクトをローカル環境にクローンします。

```
git clone https://github.com/chiemi123/coachtech-flea-market.git
```

```
cd coachtech-flea-market
```

### **2.Docker 環境のセットアップ**

Docker コンテナの起動
以下のコマンドで Docker コンテナを起動します。

```
docker-compose up -d --build
```

### **3.Laravel のセットアップ**

以下のコマンドで php コンテナにログインします。

```
docker-compose exec php bash
```

Laravel パッケージのインストール
以下のコマンドで Laravel パッケージのインストールをします。

```
composer install
```

.env ファイルを作成
プロジェクトルートに .env ファイルを作成し、.env.example をコピーします。

```
cp .env.example .env
```

### **4.アプリケーションキーの作成**

以下のコマンドでアプリケーションキーを生成します。

```
php artisan key:generate
```

### **5.マイグレーションの実行**

以下のコマンドでデータベースのマイグレーションを実行します。

```
php artisan migrate
```

マイグレーションの実行後、ブラウザで以下にアクセスできるか確認します。

http://localhost

権限エラーが発生する場合は、以下のコマンドを実行します。

```
sudo chmod -R 777 *
```

### **6.シーダーの実行**

以下のコマンドでシーダーを実行します。

```
php artisan db:seed
```

## 🔑 認証機能について

本アプリケーションでは、ユーザー認証の仕組みに [Laravel Fortify](https://laravel.com/docs/fortify) を使用しています。

### 使用バージョン

-   Laravel Fortify v1.19.1

### 主な機能

-   ログイン・新規登録
-   パスワードのリセット
-   メールアドレス認証（オプション）

### 導入手順

Fortify は `composer install` 実行時に自動でインストールされます。  
また、環境構築時にデータベースが適切にセットアップされていれば、追加の設定は不要です。


## テスト用アカウント

メールアドレス：test@yahoo.co.jp

パスワード　　：yamadayamada

## **商品画像の保存仕様**

本アプリでは、出品された商品の画像は **Laravel のストレージ（storage フォルダ）** に保存されます。  
デフォルトでは `storage/app/public/item_images` に画像が格納され  
`public/storage` にシンボリックリンクを作成することで、Web からアクセス可能になります。

#### **1. 画像の保存先**

| ディレクトリ                      | 説明                               |
| --------------------------------- | ---------------------------------- |
| `storage/app/public/item_images/` | アップロードされた画像の保存場所   |
| `public/storage/item_images/`     | Web アクセス用のシンボリックリンク |

シンボリックリンクの作成

画像を public ディレクトリからアクセス可能にするために、以下のコマンドを実行してください。

```
php artisan storage:link
```

## MailHog のセットアップ（開発環境用メール送信）

MailHog を使用すると、開発環境で送信されるメールをローカルで確認できます。

MailHog の起動  
MailHog は docker-compose up -d の時点で起動しています。  
ブラウザで以下にアクセスすると、送信されたメールを確認できます。

http://localhost:8025

.env のメール設定
.env ファイルを以下のように変更します。

```env

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="example@example.com"
MAIL_FROM_NAME="${APP_NAME}"

```

その後、Docker コンテナを以下のコマンドで再起動します。

```
docker-compose restart
```

アプリケーションの起動

nginx / Apache を使用する場合

```
docker-compose up -d
```

ブラウザで http://localhost にアクセスしてください。

php artisan serve を使用する場合

```
docker-compose exec app php artisan serve --host=0.0.0.0 --port=8000
```

ブラウザで http://localhost:8000 にアクセスしてください。

## Stripe 決済のセットアップ

### **1. Stripe アカウントを作成**

Stripe の API キーを取得するには、まず **Stripe の公式サイトでアカウントを作成** する必要があります。

🔹 **Stripe 公式サイト:** [https://dashboard.stripe.com/register](https://dashboard.stripe.com/register)

1. 上記のリンクから **Stripe アカウントを作成**
2. [Stripe ダッシュボード](https://dashboard.stripe.com/) にログイン
3. **「開発者」 → 「API キー」** から **「公開可能キー」 (`STRIPE_KEY`)** と **「シークレットキー」 (`STRIPE_SECRET`)** を取得

### **2. `.env` に Stripe の API キーを設定**

Stripe ダッシュボード で 公開可能キー と シークレットキー を取得し、.env に設定します。

```ini

STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxxxxx

```
STRIPE_WEBHOOK_SECRET の取得方法  
以下のコマンドを実行し、Webhook のリスニングを開始します。

```
docker exec -it stripe_cli stripe listen --forward-to http://nginx/webhook/stripe
```

実行すると、以下のようなメッセージが表示されます。

```
> Ready! You are using Stripe API Version [2025-01-27.acacia].
Your webhook signing secret is whsec_d14f4c8e1f6ffe5f24e3aeddce46088719e00a66aab0c26c6018742d4dbf8813
(^C to quit)
```

この whsec_ から始まる値（whsec_xxxxxxxxxxxxxxxxxxxxxxxx）を .env にコピーします。

その後、Docker コンテナを以下のコマンドで再起動します。

```
docker-compose restart
```
または、以下のコマンドを実行してください。

```
docker-compose up -d
```


### **3. Stripe のライブラリがインストールされているか確認する**

以下のコマンドを実行し、`stripe/stripe-php` がインストール済みであることを確認してください。

```
docker-compose exec php composer show stripe/stripe-php
```

もし stripe/stripe-php が表示されない場合は、以下のコマンドでインストールしてください。

```
docker-compose exec php composer require stripe/stripe-php:^12.0
```


### **4.stripe のテスト環境**

Docker コンテナ内で stripe login を実行します。  
初回のみ Stripe にログインしてください。ブラウザが開くので、認証を行います。

```
docker exec -it stripe_cli stripe login
```

以下のコマンドで、Webhook のリスニングを開始します。

```
docker exec -it stripe_cli stripe listen --forward-to http://nginx/webhook/stripe

```

以下のコマンドで、Webhook のテストを実行します。

```
docker exec -it stripe_cli stripe trigger checkout.session.completed
```

#### **1.クレジットカード決済のテスト**
Stripe のテスト環境では、以下のカード番号を使用して決済テストができます。

| カード番号          | カード種別 | 成功 or 失敗        |
| ------------------- | ---------- | ------------------- |
| 4242 4242 4242 4242 | Visa       | ✅ 成功             |
| 4000 0000 0000 0002 | Visa       | ❌ 失敗（決済拒否） |
| 5555 5555 5555 4444 | Mastercard | ✅ 成功             |


#### **2.コンビニ払いの決済テスト**
以下の手順で決済テストができます。

① コンビニ払いを選択し、購入ボタンを押します。  
stripe決済画面でメールアドレスと名前を入力後、支払うボタンを押します。

② Webhook のシミュレーション  
以下のコマンドを実行すると、実際に「コンビニで支払いが完了した」状態をシミュレーションできます。  

```
docker exec -it stripe-cli stripe trigger payment_intent.succeeded
```

コンビニ決済では、クレジットカード決済のように「決済完了ページ」には自動で遷移しません。  
そのため、以下の方法で購入完了を手動で確認してください。

🔹 方法①：マイページ (http://localhost/mypage) を開いてください。  
購入した商品が表示されていることを確認してください。

🔹 方法②：データベースで購入テーブルの status が completed になっていることを確認するため  
以下のコマンドを実行してください。

```
docker-compose exec php php artisan tinker
```

```
use App\Models\Purchase;
```

```
Purchase::where('status', 'completed')->get();
```

