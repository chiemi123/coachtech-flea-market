# アプリケーション名

フリマアプリ
ログイン後、商品の出品と購入が出来ます。

## 作成した目的

模擬案件を通して実践に近い開発経験を積み、定義された要件を実装する能力を身につけること。

## 機能一覧

ログイン機能、メール認証機能（mailhog）、商品一覧と詳細ページで商品名の検索機能、商品の出品と購入（stripe決済）機能

## 使用技術（実行環境）

・PHP7.4
・Laravel8.83.27
・MySQL8.0.26

## テーブル設計

本プロジェクトの詳細なテーブル設計は、以下の Google スプレッドシートにまとめています。

**[テーブル設計シート (Google スプレッドシート)](https://docs.google.com/spreadsheets/d/1AUlHz8zNAvwpKfZsBWg9MHVZcBCvu6NGlVGuadmne2k/edit?gid=1188247583#gid=1188247583)**


## ER 図

![alt text](.drawio.png)

## 環境構築

➀リポジトリのクローン

GitHub からプロジェクトをローカル環境にクローンします。

```
git clone https://github.com/chiemi123/coachtech-flea-market.git
```

```
cd coachtech-flea-market
```

➁Docker 環境のセットアップ

Docker コンテナの起動
以下のコマンドで Docker コンテナを起動します。

```
docker-compose up -d --build
```

```
code .
```

➂Laravel のセットアップ

以下のコマンドでphpコンテナにログインします。

```
docker-compose exec php bash
````
Laravel パッケージのインストール
以下のコマンドでLaravel パッケージのインストールをします。

```
composer install
```

.env ファイルを作成
プロジェクトルートに .env ファイルを作成し、.env.example をコピーします。

```
cp .env.example .env
```

➃アプリケーションキーの作成

以下のコマンドでアプリケーションキーを生成します。

```
php artisan key:generate
```

➄マイグレーションの実行

以下のコマンドでデータベースのマイグレーションを実行します。

```
php artisan migrate
```

マイグレーションの実行後、ブラウザで以下にアクセスできるか確認します。

http://localhost


## その他

➀MailHog のセットアップ（開発環境用メール送信）

MailHog を使用すると、開発環境で送信されるメールをローカルで確認できます。

MailHog の起動
MailHog は docker-compose up -d の時点で起動しています。
ブラウザで以下にアクセスすると、送信されたメールを確認できます。

http://localhost:8025

.env のメール設定
.envファイルを以下のように変更します。

```env

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="example@example.com"
MAIL_FROM_NAME="Example"

```

その後、Dockerコンテナを以下のコマンドで再起動します。

```
docker-compose restart
```

➁アプリケーションの起動
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

➂Stripe 決済のセットアップ
.env に Stripe の API キーを設定
Stripe ダッシュボード で 公開可能キー と シークレットキー を取得し、.env に設定します。

STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxxxxxx

その後、Dockerコンテナを再起動

```
docker-compose restart
```

以下のコマンドで、laravelにStripe の公式 PHP ライブラリ (stripe/stripe-php) をインストールします。

```
docker-compose exec app composer require stripe/stripe-php
```

インストールが完了したら、以下のコマンドでパッケージが正しくインストールされたか確認できます。

```
docker-compose exec app php artisan about | grep "Stripe"
```

## Stripe のテスト環境

Stripe のテスト環境では、以下のカード番号を使用して決済テストができます。

| カード番号              | カード種別    | 成功 or 失敗      |
|----------------------|------------|----------------|
| 4242 4242 4242 4242 | Visa       | ✅ 成功        |
| 4000 0000 0000 0002 | Visa       | ❌ 失敗（決済拒否） |
| 5555 5555 5555 4444 | Mastercard | ✅ 成功        |



コンビニ払いの決済テスト
このプロジェクトでは、Webhook をセットアップせずにコンビニ払いの決済テストが可能 です。
Stripe CLI を使うと、開発環境で Webhook をテストできます。

以下のコマンドで、Docker 環境で Stripe CLI を起動します。

```
docker run --rm -it stripe/stripe-cli:latest
```

以下のコマンドで、Webhook のリスニングを開始します。

```
docker exec -it stripe-cli stripe listen --forward-to app:8000/stripe/webhook
```

以下のコマンドで、Webhook のテストを実行します。

```
docker exec -it stripe-cli stripe trigger payment_intent.succeeded
```