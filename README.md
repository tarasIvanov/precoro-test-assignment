Тестове завдання для Precoro

Вимоги для запуску:
- Docker та Docker Compose
- Symfony CLI
- PHP 8.x
- Composer

Опис кроків для запуску:
- `git clone ..`
- `cd precoro-test-assignment`
- `composer install`
- `docker compose up -d`
- `symfony console doctrine:migrations:migrate`
- `symfony console doctrine:fixtures:load --no-interaction`
- `symfony serve`


Фікстури є лише для користувачів та продуктів.
Після запуску фікстур буде доступ до акаунту адміна:
admin@gmail.com
admin123


Аби зробити юзера адміном потрібно після створення акаунту виконати команду з консолі вказавши його пошту - 
`symfony console app:make-admin {email}`
