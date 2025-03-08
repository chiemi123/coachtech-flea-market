# アプリケーション名

フリマアプリ
ログイン後、商品の出品と購入

## 作成した目的

模擬案件を通して実践に近い開発経験を積み、定義された要件を実装する能力を身につけること。

## 機能一覧

ログイン機能、メール認証機能、商品の出品と購入（stripe決済）機能

## 使用技術（実行環境）

・PHP7.4
・Laravel8.83.27
・MySQL8.0.26

## テーブル設計

![alt text](image.png)

## ER 図

![alt text](image-1.png)

## 環境構築

Docker ビルド

```
git clone git@github.com:chiemi123/attendance-management-system.git
```
①DockerDesktop アプリを立ち上げる

```
docker-compose up -d --build
```

Laravel 環境構築

```
docker-compose exec php bash
````

```
composer install
```

「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.env ファイルを作成
.env に以下の環境変数を追加
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

アプリケーションキーの作成

```
php artisan key:generate
```

マイグレーションの実行

```
php artisan migrate
```

## その他


