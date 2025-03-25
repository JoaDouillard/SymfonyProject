
# Symfony Project

Ce projet est une API développée avec Symfony, fournissant une interface backend robuste pour les applications web et mobiles.

## Fonctionnalités

- Structure d'API RESTful
- Endpoints documentés
- Base de données relationnelle

## Prérequis

Pour installer et exécuter ce projet, vous aurez besoin de :

- PHP 8.1 ou supérieur
- Composer
- Serveur SQLite 
- Symfony CLI

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
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
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

## API Endpoints

L'application expose les endpoints suivants :

- `GET /api/resources` - Liste des ressources
- `GET /api/resources/{id}` - Détails d'une ressource spécifique
- `POST /api/resources` - Créer une nouvelle ressource
- `PUT /api/resources/{id}` - Mettre à jour une ressource
- `DELETE /api/resources/{id}` - Supprimer une ressource

## Documentation

La documentation de l'API est disponible à l'adresse suivante lorsque l'application est en cours d'exécution :

```
http://localhost:8000/api/docs
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
extension=pdo_mysql  ; Nécessaire pour la connexion à la base de données MySQL
extension=openssl    ; Nécessaire pour les fonctionnalités de sécurité
extension=curl       ; Nécessaire pour les requêtes HTTP
extension=exif       ; Nécessaire pour le traitement des métadonnées d'images
extension=mysqli     ; Alternative pour la connexion MySQL
```

### Comment vérifier et activer ces extensions

1. **Localiser votre fichier php.ini utilisé** :
   ```php
   <?php
   echo php_ini_loaded_file();
   ```

2. **Ouvrir le fichier php.ini** et décommenter (retirer le `;` devant) les lignes des extensions mentionnées ci-dessus.

3. **Redémarrer votre serveur web** après avoir modifié le fichier php.ini.

4. **Vérifier que les extensions sont actives** :
   ```php
   <?php
    echo "curl : " . (extension_loaded('curl') ? 'OK' : 'MANQUANT') . "<br>";
    echo "exif : " . (extension_loaded('exif') ? 'OK' : 'MANQUANT') . "<br>";
    echo "fileinfo : " . (extension_loaded('fileinfo') ? 'OK' : 'MANQUANT') . "<br>";
    echo "gd : " . (extension_loaded('gd') ? 'OK' : 'MANQUANT') . "<br>";
    echo "intl : " . (extension_loaded('intl') ? 'OK' : 'MANQUANT') . "<br>";
    echo "mbstring : " . (extension_loaded('mbstring') ? 'OK' : 'MANQUANT') . "<br>";
    echo "mysqli : " . (extension_loaded('mysqli') ? 'OK' : 'MANQUANT') . "<br>";
    echo "openssl : " . (extension_loaded('openssl') ? 'OK' : 'MANQUANT') . "<br>";
    echo "pdo_mysql : " . (extension_loaded('pdo_mysql') ? 'OK' : 'MANQUANT') . "<br>";

   ```

### Limitations de téléchargement

Si vous prévoyez d'uploader des fichiers volumineux, assurez-vous également de configurer ces valeurs dans votre fichier php.ini :

```ini
; Taille maximale de téléchargement
upload_max_filesize = 20M
post_max_size = 21M
; Temps d'exécution maximum pour les scripts
max_execution_time = 120
; Limite de mémoire
memory_limit = 256M
```

## Problèmes courants liés aux extensions PHP

### Erreur "fileinfo extension not loaded"
Cette erreur indique que l'extension fileinfo n'est pas activée. Cette extension est nécessaire pour valider les types de fichiers uploadés.

### Erreur "Gd driver not installed" 
Cette erreur indique que l'extension GD n'est pas activée. Cette extension est nécessaire pour le traitement des images (redimensionnement, recadrage, etc.).

### Erreur "Intl extension is missing"
Cette erreur indique que l'extension Intl n'est pas activée. Cette extension est nécessaire pour le support de l'internationalisation.
