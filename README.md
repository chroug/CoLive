# CoLive - Guide de Développement

Bienvenue sur le projet **CoLive**. Ce guide contient toutes les instructions pour configurer ton environnement, régénérer la base de données avec des données réalistes et lancer les tests.

---

## 1. Installation du projet

1.  **Cloner le projet** :
2. **Installer les dépendances PHP via Composer** :
    ```bash
    composer install
    ```

3. **Configuration des bases de données** :
   Copiez le fichier d'environnement et modifiez la ligne DATABASE_URL avec vos accès locaux :
    ```bash
    cp .env .env.local
    ```
4. **Ajout de la liaison avec la base de donnée** :
   Dans .env.local, modifiez la ligne comme ceci
    ````env
   DATABASE_URL="mysql://USER:PASSWORD@127.0.0.1:3306/colive?serverVersion=8.0"
    ````
5. **Initialiser la base de données**
    ````bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate --no-interaction
   ````
6. **Charger les données de test (Fixtures)** :
   Cette étape crée automatiquement des annonces, des avis et les comptes utilisateurs par défaut (dont l'admin).
    ````bash
   php bin/console doctrine:fixtures:load --no-interaction
    ````
---

## Identifiants de test

Grâce aux fixtures, vous pouvez vous connecter immédiatement avec les comptes suivants :

| Rôle | Email | Mot de passe |
| :--- | :--- | :--- |
| **Administrateur** | `admin@colive.com` | `admin` |
| **Étudiant** | `test@example.com` | `password` |

- **Accès Admin** : Le dashboard de modération est accessible sur [http://localhost:8000/admin/dashboard](http://localhost:8000/admin/dashboard).

---

## Exécuter les Tests

Le projet inclut une suite de tests fonctionnels avec **Codeception** pour garantir la sécurité et le bon fonctionnement de la modération.

### Préparer la base de test :
```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:create --env=test
```

## Démarrer le serveur
Utilisez le serveur local de Symfony pour lancer l'application :
```bash
composer start
```
- L'application est maintenant disponible sur http://localhost:8000

