# Symfony API Project

Ce projet est une API développée avec Symfony, fournissant une interface backend robuste pour les applications web et mobiles.

## Fonctionnalités

- Structure d'API RESTful
- Système d'authentification sécurisé par clé API
- Endpoints documentés
- Base de données relationnelle

## Prérequis

Pour installer et exécuter ce projet, vous aurez besoin de :

- PHP 8.1 ou supérieur
- Composer
- Serveur MySQL (ou autre base de données compatible avec Doctrine)
- Symfony CLI (recommandé pour le développement)

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
DATABASE_URL="mysql://username:password@127.0.0.1:3306/db_name?serverVersion=8&charset=utf8mb4"
```

### 4. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
```

### 5. Charger les fixtures (données de test, si disponibles)

```bash
php bin/console doctrine:fixtures:load
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
    ├── bin/                  # Exécutables (console Symfony)
    ├── config/               # Configuration de l'application
    ├── migrations/           # Migrations de base de données
    ├── public/               # Point d'entrée web, assets publics
    ├── src/                  # Code source de l'application
    │   ├── Controller/       # Contrôleurs API
    │   ├── Entity/           # Entités Doctrine
    │   ├── Repository/       # Repositories pour accéder aux données
    │   └── Security/         # Classes liées à la sécurité
    ├── templates/            # Templates Twig
    ├── tests/                # Tests automatisés
    └── var/                  # Fichiers générés (cache, logs)
```

## API Endpoints

L'application expose les endpoints suivants :

- `GET /api/resources` - Liste des ressources
- `GET /api/resources/{id}` - Détails d'une ressource spécifique
- `POST /api/resources` - Créer une nouvelle ressource
- `PUT /api/resources/{id}` - Mettre à jour une ressource
- `DELETE /api/resources/{id}` - Supprimer une ressource

## Authentification

L'API utilise une authentification par clé API. Pour accéder aux endpoints protégés, incluez votre clé API dans les en-têtes de requête :

```
Authorization: ApiKey YOUR_API_KEY
```

## Documentation

La documentation de l'API est disponible à l'adresse suivante lorsque l'application est en cours d'exécution :

```
http://localhost:8000/api/docs
```

## Résolution des problèmes courants

### Erreur de connexion à la base de données

Vérifiez que :
1. Votre serveur MySQL est en cours d'exécution
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

## Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir des issues ou à soumettre des pull requests.

## Licence

Ce projet est sous licence [MIT](LICENSE).

---

*Note: Adaptez ce README selon les spécificités exactes de votre projet (endpoints, fonctionnalités, configuration requise, etc.).*