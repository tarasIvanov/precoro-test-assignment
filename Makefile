migrate:
	symfony console doctrine:migrations:migrate

entity:
	symfony console make:entity

migration:
	symfony console make:migration

serve:
	symfony serve

clcache:
	php bin/console cache:clear



#symfony console app:make-admin taras.ivanov.ua@gmail.com