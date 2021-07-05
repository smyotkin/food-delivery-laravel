# Ferone Laravel

## Установка и настройка проекта

##### 1. Делаем клон проекта и переключаем ветку на `dev` (консоль)`:

```bash
> git clone https://smyotkin@bitbucket.org/ivanov-team/ferone-mvc.git
> cd ferone-mvc
> git checkout dev
```

##### 2. Загружаем все зависимости через `composer` (консоль):
```bash
> composer update
```

##### 3. В корне проекта переименовываем `.env.example` в `.env` и меняем данные для входа в БД:
```env
DB_DATABASE=database
DB_USERNAME=username
DB_PASSWORD=password
```

##### 4. Настроить 3 домена на сервере с точкой входа в /public, например (домен => точка входа):
```bash
1. api.ferone-mvc.ru => /ferone-mvc/public
2. back.ferone-mvc.ru => /ferone-mvc/public
3. suare-mvc.su => /ferone-mvc/public
```

##### 5. В файле `config/custom.php` заменить ссылки на домены
```php
return [
    'subdomain' => [
        'api' => 'api.ferone-mvc.ru',
        'ferone' => 'back.ferone-mvc.ru',
        'site' => 'suare-mvc.su',
    ],
];
```

##### 6. Сгенерировать новый ключ приложения (консоль):
```bash
> php artisan key:generate
```

##### 7. Выполнить миграцию и запустить сидеров (консоль):
```bash
> php artisan migrate
> php artisan db:seed
```

##### 8. Зайти на домен back.ferone-mvc.ru и войти под юзером:
```bash
Телефон:
+79112223344
Пароль:
123456
```
