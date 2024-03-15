# Laravel + nginx

## hosts

```
127.0.0.1			localhost.laravel.com
```

## docker

啟動

```
docker compose up -d
```

關閉容器

```
docker compose down
```

進入容器終端 安裝 composer 並複製.env

```
docker exec -it php /bin/bash
composer install
cp .env.example .env
```

Laravel api 圖片連結建立

```
php artisan storage:link
```

Laravel passport 套件建立 key

```
php artisan passport:keys
```
