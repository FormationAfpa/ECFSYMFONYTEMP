# Bibliothèque en ligne

## Configuration du projet

1. Clonez le dépôt :
```bash
git clone [URL_DU_REPO]
cd [NOM_DU_PROJET]
```

2. Installez les dépendances :
```bash
composer install
npm install
```

3. Configuration de l'environnement :
   - Copiez le fichier `.env.example` en `.env.local`
   - Modifiez les variables dans `.env.local` selon votre environnement :
     - `DATABASE_URL` : Configurez votre connexion à la base de données
     - `APP_SECRET` : Générez une nouvelle clé secrète
     - `MAILER_DSN` : Configurez votre service d'envoi d'emails

4. Créez la base de données :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Chargez les fixtures (données de test) si nécessaire :
```bash
php bin/console doctrine:fixtures:load
```

6. Lancez le serveur de développement :
```bash
symfony serve -d
```

## Fonctionnalités

- Gestion des livres
- Système de commentaires
- Système de notation
- Gestion des abonnements
- Authentification des utilisateurs

## Sécurité

- Ne jamais commiter les fichiers `.env` contenant des informations sensibles
- Toujours utiliser `.env.local` pour les configurations locales
- Les secrets de production doivent être gérés via les variables d'environnement

## Contribution

1. Créez une branche pour votre fonctionnalité
2. Committez vos changements
3. Créez une Pull Request

## License

[Votre licence]
