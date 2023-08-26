# Rese
飲食店予約サービス
![_](.img/rese_top.png)

## 作成した目的
webアプリ開発の勉強のため

## アプリケーションURL
* 開発(ローカル)環境  
  http:\//localhost
* 本番(EC2)環境  
  <a href="http://ec2-18-182-45-97.ap-northeast-1.compute.amazonaws.com" target="_blank">http://ec2-18-182-45-97.ap-northeast-1.compute.amazonaws.com</a>
  
## 他のリポジトリ
* ソースコード(GitHub)  
  [https://github.com/takamasa-seto/rese](https://github.com/takamasa-seto/rese)

## 機能一覧
  * 会員登録  
    左上のプルダウンメニューの"アカウント作成"から会員登録画面を表示する。登録時は登録されたメールアドレスに確認メールが送信される。  
    <img width="200" src='.img/rese_menu_for_user.png'>
  * ログイン  
    確認メールにより認証されたユーザのみがログイン可能。  
  * ログアウト  
    左上のプルダウンメニューからログアウトできる。  
    <img width="200" src='.img/rese_menu_for_loginuser.png'>
  * 検索  
    トップページ（または左上のプルダウンメニューの"ホーム"から表示される画面）の右上のメニューから、エリア、ジャンル、店舗名ごとに店舗を検索することができる。検索実行時は、トップページに検索された店舗のみが表示される。
  * 店舗詳細表示  
    トップページ（または左上のプルダウンメニューの"ホーム"から表示される画面）の各店舗情報の"詳しくみる"ボタンを押すと店舗詳細画面を表示する。
  * 予約  
    店舗詳細画面から予約することができる。
  * マイページ表示  
    ログインしたユーザは、左上のプルダウンメニューの"マイページ"ボタンから、予約情報やお気に入り店舗情報を記載したマイページをみることができる。  
  * 予約変更・キャンセル  
    マイページに表示された各予約情報の"変更"ボタンから、予約情報の変更、キャンセルができる。
  * QRコード表示  
    マイページに表示された各予約情報の"QRコード"ボタンから、来店時に提示するためのQRコードを表示することができる。
  * お気に入り登録・解除  
    ログインしたユーザは、トップページ（または左上のプルダウンメニューの"ホーム"から表示される画面）の各店舗のハートマーク（ログインしていない時は表示されない）をクリックすることで、お気に入り登録と解除ができる。  
    お気に入り登録されるとハートマークが赤くなり、マイページに店舗情報が表示されるようになる。
  * 管理者・店舗代表者のログイン  
    上記アプリケーションURLに"admin/login"をつけたURLを直打ちするとログイン画面が表示される。  
    初回ログイン時は、ログイン画面の"forgot your password?"からパスワードを変更すること。  
  * 管理者・店舗代表者一覧表示  
    管理者は、管理者と店舗代表者の一覧を表示した管理画面を左上のプルダウンメニューの"管理者一覧へ"から表示することができる。  
    <img width="200" src='.img/rese_menu_for_admin.png'>
  * 管理者・店舗代表者の登録・変更  
    管理者は、管理画面から管理者と店舗代表者を追加することができる。
    このとき、入力したEmailアドレスが既に登録されている場合は役割や店舗情報が更新される。
    また、入力したEmailアドレスが登録されていない場合は、新規に登録される。  
    <img width="600" src='.img/rese_adminlist.png'>
  * お知らせメール送信  
    管理者は、管理画面の"作成"ボタンからユーザにお知らせメールを作成・送信することができる。  
  * 店舗情報の更新、作成  
    店舗代表者は、左上のプルダウンメニューの"店舗情報の編集"から、自身が担当する店舗の情報を編集することができる。また、"店舗情報の新規登録"から、新規店舗を作成することができる。  
    <img width="200" src='.img/rese_menu_for_staff.png'>
  * 予約情報一覧表示  
    店舗代表者は、左上のプルダウンメニューの"予約一覧"から、自身が担当する店舗の予約一覧をみることができる。  
  * 評価機能  
    予約後（予約日の夜）にアンケート協力メールが該当ユーザにバッチ処理で送信される。
    ユーザは、メールのリンク先にアクセスすることで、店の評価（スコアとコメント）を入力することができる。
    店舗代表者は、予約一覧を表示した画面から、予約ごとの"詳細"ボタンにより、各予約の詳細を表示することができ、評価結果はそこから閲覧することができる。  

## 追加機能(2023年8月)
  * 口コミ機能  
    会員登録したユーザは店舗詳細画面から口コミを投稿、編集、削除することができる。なお、同じ店舗に同一のユーザが複数の口コミを行うことはできない。  
    管理者は、左上のプルダウンメニュー”口コミ管理へ”から全ての口コミの閲覧と削除ができる。  
  * 店舗一覧ソート機能  
    トップページ（または左上のプルダウンメニューの"ホーム"から表示される画面）の右上のメニューから、同画面に表示する店舗を並び変えることができる。並び替え方法は、"ランダム"（デフォルト）、"口コミ評価が高い順"、"口コミ評価が低い順"から選択できる。  
  * csvインポート機能  
    管理者は、左上のプルダウンメニュー”店舗CSV登録へ”から、複数の新規店舗を、csvファイルをアップロードすることにより一括して追加することができる。  
    * csvファイルの仕様
      * 文字コード: UTF-8  
      * カンマ区切り  
      * 1行目はヘッダ情報  
      * 'name'列: 店舗名 50文字以内  
      * 'region'列: 地域　'東京都'、'大阪府'、'福岡県'のいずれか
      * 'genre'列: ジャンル　'寿司'、'焼肉'、'イタリアン'、'居酒屋'、'ラーメン'のいずれか  
      * 'description'列: 店舗概要 400文字以内  
      * 'image_url'列: 画像のURL ファイルの種類(拡張子)は'.jpeg'、'.jpg'、'.png'に対応  
      * 'operation_pattern'列: 店舗の営業形態 1または2から選択する  
          1: 営業時間11:00～22:00、月曜定休  
          2: 営業時間17:00～23:00、年中無休  
      * 'time_per_reservation'列: 予約あたりに座席を確保する時間 H:i形式で記載する  
          なお、時間H、分iはそれぞれ二桁の整数である  
          ※ Excelで保存するとH:i形式が変更される可能性があるので注意すること  
      * 'table_sixteen_num'列: 定員16名のテーブル数 0～255の整数  
      * 'table_eight_num'列: 定員8名のテーブル数 0～255の整数  
      * 'table_four_num'列: 定員4名のテーブル数 0～255の整数  
      * 'table_two_num'列: 定員2名のテーブル数 0～255の整数  
      * 'table_one_num'列: 定員1名のテーブル数 0～255の整数  
    <img width="900" src='.img/csv_example.png'>

## 使用技術(実行環境)
  * 開発フレームワーク  
    laravel8.83.8  
    ※ ユーザ認証にはBreezeを使用
  * 仮想サーバ  
    EC2 (本番環境のみ)
  * データベース  
    RDS (本番環境のみ)
  * ストレージ  
    s3（本番環境のみ）
  * メール送信  
    Amazon SES (本番環境)  
    MailHog (開発(ローカル)環境)

## テーブル設計
![_](.img/rese_tables.png)

## ER図
![_](.img/rese_er.png)

# 環境構築
## 開発（ローカル）環境
ローカルでのテスト環境のセットアップ手順を示します。  
* GitHubの上記リポジトリのクローンを作業フォルダに作成。
  > git clone https://github.com/takamasa-seto/rese.git
* クローンしたreseフォルダ直下に移動。
* docker-compose-dev.ymlファイルをつかってDockerコンテナを起動。
  > docker-compose -f docker-compose-dev.yml up -d --build  
* phpコンテナにログインし、composerをインストールする。
  > docker-compose -f docker-compose-dev.yml exec php bash  
  > composer install  
* srcフォルダ直下に.env.dev.exampleのコピーを作成する。
  > cp .env.dev.example .env  
* コピーした.envファイルの次の部分を編集する。
  > DB_PASSWORD=(任意のパスワード)  
* アプリケーションキーを作成する(phpコンテナにログインした状態)。
  > php artisan key:generate  
* データベースのマイグレーション(phpコンテナにログインした状態)。
  > php artisan migrate  
* 店舗画像のダウンロード(スクリプトの実行)。
  > bash download-images.sh  
* rese直下のAdminResetPassword.phpを所定の場所に配置する。
  > cp AdminResetPassword.php vendor/laravel/framework/src/illuminate/Auth/Notifications  
* 店舗情報をシーディングする。
  > php artisan db:seed --class AddShopsCsv  
* 管理者情報をシーディングする。
  ※src/database/seeders/admins.csvの内容を適切に変更してください  
  > php artisan db:seed --class AddAdminsCsv  
* ストレージのシンボリックリンクを作成。
  > php artisan storage:link  
* スケジューラの実行。  
  > php artisan schedule:work  

## 本番環境
EC2(仮想サーバ)とRDS(データベース)をつかった本番環境のセットアップ手順を示します。
* AWSのEC2インスタンス、RDS、SESのID、s3のバケットを作成しておく。
* 店舗画像のダウンロード(s3内に配置)  
  店舗の画像を、s3のバケット内"rese/image"フォルダ下に配置する。  
  画像のダウンロード先は、download-images.shの中を参照すること。  
* GitHubの上記リポジトリのクローンをEC2のインスタンスに作成。
  > git clone https://github.com/takamasa-seto/rese.git
* クローンしたreseフォルダ直下に移動。
* docker-compose-prod.ymlファイルをつかってDockerコンテナを起動。
  > docker-compose -f docker-compose-prod.yml up -d --build  
* phpコンテナにログインし、composerのインストール準備（エラー対策）をする。
  > docker-compose -f docker-compose-prod.yml exec php bash  
  > apt-get update --allow-releaseinfo-change  
  > apt-get install -y libpng-dev libjpeg62-turbo-dev && docker-php-ext-configure gd --with-jpeg && docker-php-ext-install -j$(nproc) gd  
* composerをインストールする。  
  > composer install  
* s3ドライバをインストールする
  > composer require --with-all-dependencies league/flysystem-aws-s3-v3 "^1.0"
* srcフォルダ直下に.env.prod.exampleのコピーを作成する。  
  > cp .env.prod.example .env
* コピーした.envファイルの次の部分を編集する。
  > APP_URL=(EC2のオープンアドレス)  
  > DB_HOST=(RDSのエンドポイント)  
  > DB_DATABASE=(RDSで設定したデータベース名)  
  > DB_USERNAME=(RDSで設定したユーザ名)  
  > DB_PASSWORD=(RDSで設定したパスワード)  
  > MAIL_FROM_ADDRESS=(SESで設定したメールアドレス)  
  > AWS_ACCES_KEY_ID=(AWSのアクセスキー)  
  > AWS_SECRET_ACCES_KEY_ID=(AWSのシークレットアクセスキー)  
  > AWS_DEFAULT_REGION=(SESで設定したリージョン。日本はap-northeast-1)  
  > AWS_BUCKET=(s3のバケット名)  
  > AWS_URL=https:\//s3-ap-northeast-1.amazonaws.com/(s3のバケット名)  
* アプリケーションキーを作成する(phpコンテナにログインした状態)。
  > php artisan key:generate  
* データベースのマイグレーション(phpコンテナにログインした状態)。
  > php artisan migrate  
* rese直下のAdminResetPassword.phpを所定の場所に配置する。  
  > cp AdminResetPassword.php vendor/laravel/framework/src/illuminate/Auth/Notifications  
* 店舗情報をシーディングする。
  > php artisan db:seed --class AddShopsCsv  
* 管理者情報をシーディングする。
  ※src/database/seeders/admins.csvの内容を適切に変更してください  
  > php artisan db:seed --class AddAdminsCsv  
* スケジューラの実行(phpコンテナにログインした状態)。  
  * Cronのインストール。  
    > apt-get install cron  
  * Cronの設定ファイルを編集するためにVIエディタをインストール。  
    > apt-get install vim  
  * Cronの設定ファイルをVIエディタで開く。
    > crontab -e  
  * Cronの設定ファイルに↓のコマンドを追加し、保存して閉じる。
    > \* * * * * cd (プロジェクトのフルパス(例:/var/www)) && (PHPのフルパス(例:/usr/local/bin/php)) artisan schedule:run >> /dev/null 2>&1  
  * Cronを起動。  
    > service cron start  

    ※ Cronが起動しているかの確認↓  
      > service cron status  

    ※ crontabの内容の確認↓  
      > crontab -l  
