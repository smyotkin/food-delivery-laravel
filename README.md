# Food Delivery Laravel

## Установка и настройка проекта

##### 1. Делаем клон проекта и переключаем ветку на `dev` (консоль):

```bash
> git clone https://github.com/smyotkin/food-delivery-laravel.git
> cd food-delivery-laravel
> git checkout dev
```

##### 2. Загружаем все зависимости через `composer` (консоль):
```bash
> composer install
```

##### 3. В корне проекта переименовываем `.env.example` в `.env` и меняем данные:
```env
DB_DATABASE=database
DB_USERNAME=username
DB_PASSWORD=password

API_DOMAIN="api.domain.ru"
SYSTEM_DOMAIN="back.domain.ru"
SITE_DOMAIN="domain.ru"

SMSCRU_LOGIN="login"
SMSCRU_SECRET="secret"
SMSCRU_SENDER="sender"

TELEGRAM_BOT_TOKEN="token"

DADATA_TOKEN="token"
DADATA_SECRET="secret"
```

##### 4. Настроить 3 домена на сервере с точкой входа в /public, например (домен => точка входа):
```bash
1. api.domain.ru => /domain/public
2. back.domain.ru => /domain/public
3. domain.ru => /domain/public
```

##### 5. Сгенерировать новый ключ приложения (консоль):
```bash
> php artisan key:generate
```

##### 6. Выполнить миграцию и запустить сидеров (консоль):
```bash
> php artisan migrate
> php artisan db:seed
```

##### 7. Зайти на домен back.domain.ru и войти под юзером:
```bash
Телефон:
+79112223344
Пароль:
123456
```
