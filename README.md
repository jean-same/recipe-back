# recipe-back

## Project setup

### Renseigner ces infos dans un fichier .env
```
 DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7&charset=utf8mb4"
```

Créer la base de données
```
 php bin/console doctrine:database:create
```

Lancer les migrations
```
php bin/console doctrine:migrations:migrate
```

Lnacer les fixtures pour remplir la bdd 
```
php bin/console doctrine:fixtures:load
```

Lancer le serveur php/symfony
```
php -S localhost:8000 -t public ou symfony serve
```
