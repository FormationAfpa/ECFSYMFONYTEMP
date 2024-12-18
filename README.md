# Projet de Gestion de Bibliothèque 
 une application web de gestion de bibliothèque développée avec Symfony 6. Elle permet la gestion des livres, des emprunts, et des abonnements des utilisateurs.

Technologies Utilisées
- Symfony 6
- PHP 8
- Doctrine ORM
- Twig Template Engine
- JavaScript
- CSS
- MySQL

Structure du Projet

Backend
- `/src/Controller/` : Contrôleurs de l'application
- `/src/Entity/` : Entités Doctrine
- `/src/Form/` : Formulaires Symfony
- `/src/Repository/` : Repositories pour l'accès aux données

Frontend
- `/templates/` : Templates Twig
- `/public/css/` : Feuilles de style CSS
- `/public/js/` : Scripts JavaScript
- `/assets/` : Resources frontend (images, etc.)

Fonctionnalités Principales

Gestion des Livres
- Catalogue complet des livres
- Système de recherche
- Gestion des emprunts
- Système de notation et commentaires

### Gestion des Utilisateurs
- Inscription et authentification
- Profils utilisateurs
- Gestion des abonnements
- Historique des emprunts

### Administration
- Interface d'administration sécurisée
- Gestion du catalogue
- Gestion des utilisateurs
- Suivi des emprunts

## Configuration et Installation

### Prérequis
- PHP 8.0 ou supérieur
- Composer
- Symfony CLI
- MySQL/MariaDB

### Installation
1. Cloner le repository
2. Installer les dépendances :
   ```bash
   composer install
   ```
3. Configurer la base de données dans `.env.local`
4. Créer la base de données :
   ```bash
   php bin/console doctrine:database:create
   ```
5. Exécuter les migrations :
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
6. Lancer le serveur de développement :
   ```bash
   symfony server:start
   ```

## Structure de la Base de Données

### Tables Principales
- `user` : Informations des utilisateurs
- `book` : Catalogue des livres
- `loan` : Gestion des emprunts
- `subscription` : Abonnements
- `book_comment` : Commentaires sur les livres

Securité
 Authentification utilisateur
- Rôles et permissions
- Protection CSRF
- Validation des données


