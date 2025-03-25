
# Symfony Project

Ce projet est une API développée avec Symfony, fournissant une interface backend robuste pour les applications web et mobiles.

## Fonctionnalités

- Structure d'API RESTful
- Endpoints documentés
- Base de données relationnelle

## Installation

Suivez ces étapes pour installer et configurer le projet localement :

### 1. Cloner le dépôt

```bash
git clone https://github.com/votre-username/SymfonyProject.git
cd SymfonyProject
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l'environnement

Copiez le fichier `.env` en `.env.local` et configurez vos variables d'environnement :

```bash
cp .env .env.local
```

Éditez le fichier `.env.local` pour configurer votre connexion à la base de données :

```
DATABASE_URL="sqlite:///%kernel.project_dir%/var/LeNomDeVotreBase.db"
```

### 4. Créer la base de données

```bash
php bin/console make:migration
php bin/console doc:mig:mig
```

## Démarrage du serveur

Pour lancer le serveur de développement :

```bash
symfony server:start
```

Vous pouvez maintenant accéder à l'application à l'adresse : `http://localhost:8000`

## Structure du projet

```
└── SymfonyProject/
    ├── bin/                   # Exécutables Symfony
    ├── config/                # Configuration de l'application
    ├── migrations/            # Migrations de base de données
    ├── public/                # Point d'entrée web, assets publics
    │   └── uploads/           # Dossier des fichiers uploadés
    │       └── images/        # Images uploadées
    ├── src/                   # Code source de l'application
    │   ├── Command/           # Commandes personnalisées Symfony
    │   ├── Controller/        # Contrôleurs API
    │   ├── Entity/            # Entités Doctrine
    │   ├── Form/              # Formulaires Symfony
    │   ├── Repository/        # Accès aux données
    │   ├── Security/          # Gestion de la sécurité
    │   └── Kernel.php         # Fichier Kernel Symfony
    ├── templates/             # Templates Twig
    │   ├── admin/             # Interface administrateur
    │   ├── api/               # Templates pour l'API
    │   ├── artist/            # Vue artiste
    │   ├── event/             # Vue événement
    │   ├── home/              # Page d'accueil
    │   ├── registration/      # Inscription
    │   └── security/          # Sécurité et login
    ├── tests/                 # Tests automatisés
    ├── translations/          # Fichiers de traduction
    ├── var/                   # Fichiers générés (cache, logs)
    └── vendor/                # Dépendances Composer
```

## Résolution des problèmes courants

### Erreur de connexion à la base de données

Vérifiez que :
1. Votre serveur SQLite est en cours d'exécution
2. Les identifiants dans `.env.local` sont corrects
3. La base de données existe (sinon créez-la avec `php bin/console doctrine:database:create`)

### Problèmes de cache

En cas d'erreurs après des modifications de configuration :

```bash
# Sur Windows (PowerShell)
Remove-Item -Recurse -Force var/cache/*

# Sur Linux/MacOS
rm -rf var/cache/*
```

### Extension PHP manquante

Si vous rencontrez des erreurs concernant des extensions PHP manquantes, installez-les via votre gestionnaire de paquets ou modifiez votre fichier php.ini.

## Configuration PHP requise

Pour assurer le bon fonctionnement de ce projet Symfony, votre installation PHP doit avoir les extensions suivantes activées dans le fichier `php.ini` :

### Extensions PHP essentielles

```ini
extension=fileinfo   ; Nécessaire pour la validation des types de fichiers
extension=gd         ; Nécessaire pour le traitement des images
extension=intl       ; Nécessaire pour l'internationalisation
extension=mbstring   ; Nécessaire pour le support des caractères multi-octets  
extension=SQLite  ; Nécessaire pour la connexion à la base de données SQLite
extension=openssl    ; Nécessaire pour les fonctionnalités de sécurité
extension=curl       ; Nécessaire pour les requêtes HTTP
extension=exif       ; Nécessaire pour le traitement des métadonnées d'images
```

## Documentation API

La documentation de l'API est disponible à l'adresse suivante lorsque l'application est en cours d'exécution :

```
http://localhost:8000/api/docs
```
