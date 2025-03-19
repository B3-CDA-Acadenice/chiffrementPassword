# Documentation du Projet Laravel - Centralis

## 📌 Introduction

Ce projet est une application web développée en **Laravel**. Elle permet de gérer les utilisateurs et leurs rôles dans le cadre de la plateforme de **marché public Centralis**. L’application utilise notamment **Filament** pour fournir une interface d’administration puissante et ergonomique, et **Laravel Sanctum** pour l’authentification sécurisée des API. L’objectif est de proposer un système robuste de gestion des accès (rôles et permissions) adapté aux besoins d’une plateforme de marché public, tout en s’appuyant sur des solutions modernes du framework Laravel.

---

## 🛠️ Technologies utilisées

- **Laravel** (v12.1) – Framework PHP pour la création d’applications web.
- **Laravel Sanctum** – Bibliothèque Laravel pour la gestion de l'authentification API via des tokens.
- **Filament (Admin Panel)** – Tableau de bord moderne pour gérer les données (utilisateurs, rôles, etc.) via une interface web.
- **MySQL / MariaDB** – Base de données relationnelle utilisée pour stocker les utilisateurs, rôles et autres informations.
- *(Optionnel : **Spatie Laravel Permission** – package pour une gestion avancée des permissions, envisagé pour de futures évolutions.)*

---

## ⚙️ Installation & Configuration

### 1️⃣ Prérequis

Avant d’installer le projet, assurez-vous d’avoir installé sur votre système :

- **PHP 8.3+** (et les extensions requises, ex : OpenSSL, PDO, Mbstring, etc.)
- **Composer** – Gestionnaire de dépendances PHP.
- **Node.js & NPM** – Pour la compilation des assets front-end (si nécessaire).
- **MySQL ou MariaDB** – Système de gestion de base de données pour stocker les données de l’application.

### 2️⃣ Installation du projet

**Cloner le dépôt :** Récupérez le code du projet depuis le dépôt GitHub, puis placez-vous dans le dossier du projet.

```bash
git clone https://github.com/VotreRepo/Centralis-Laravel.git
cd Centralis-Laravel
```

**Installer les dépendances :** Utilisez Composer pour installer les packages PHP, puis NPM pour les packages front-end. Enfin, compilez les assets (si le projet en comporte, par exemple le CSS/JS pour l’admin).

```bash
composer install
npm install && npm run build
```

**Configurer l’environnement :** Copiez le fichier d’exemple **.env.example** vers **.env**, puis ouvrez-le pour ajuster les paramètres (base de données, mail, etc.).

```bash
cp .env.example .env   # Sur Linux/Mac
# Sur Windows (Invite de commandes PowerShell) utilisez la commande 'copy' :
# copy .env.example .env
```

Dans le fichier **.env**, éditez les valeurs de connexion à la base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=centralis
DB_USERNAME=root
DB_PASSWORD=VotreMotDePasseIci
```

Assurez-vous également de définir les autres variables nécessaires (par exemple `APP_URL`, configuration mail si besoin, etc.).

**Générer la clé d’application :** Laravel utilise une clé secrète pour chiffrer certaines données. Générez-la via la commande artisan :

```bash
php artisan key:generate
```

### 3️⃣ Migrations et données initiales

Exécutez les migrations ainsi que les seeders fournis pour créer les tables et insérer les données initiales (notamment les rôles par défaut et un utilisateur administrateur de test) :

```bash
php artisan migrate --seed
```

Cette commande va créer la structure de la base de données et insérer les rôles par défaut. Un utilisateur **Admin** de test est également créé (email : **test@example.com**, mot de passe : **password**) pour vous permettre de vous connecter immédiatement à l’application.

**Démarrer le serveur local :** Une fois l’installation terminée, vous pouvez lancer le serveur de développement Laravel :

```bash
php artisan serve
```

Par défaut, votre application sera accessible à l’adresse : **http://127.0.0.1:8000**. Vous pouvez ensuite accéder aux différentes fonctionnalités (site web, interface admin, API).

#### ℹ️ Remarques sur Windows et Linux

- *Sous **Windows** :* Si vous n’utilisez pas WSL ou Git Bash, adaptez les commandes de copie de fichiers (`cp` → `copy`) et d’export de variables selon la syntaxe Windows. Assurez-vous que PHP, Composer et Node.js sont bien ajoutés à votre `%PATH%` pour pouvoir exécuter les commandes ci-dessus. Vous pouvez utiliser des environnements tels que **XAMPP**, **Wamp** ou **Laragon** pour faciliter la configuration PHP/MySQL sous Windows, ou simplement utiliser la commande `php artisan serve` comme indiqué (qui fonctionne si PHP est installé).
- *Sous **Linux** :* Vérifiez les permissions des dossiers de cache et de logs (par exemple, `storage/` et `bootstrap/cache/`). En cas de problème de droits, vous pouvez exécuter `chmod -R 775 storage bootstrap/cache` pour donner les permissions adéquates (ou ajuster propriétaires/utilisateurs selon votre configuration). Assurez-vous que le service MySQL/MariaDB est démarré et que les identifiants configurés correspondent à votre utilisateur de base de données Linux.

---

## 🛡️ Gestion des rôles et permissions via la base de données

L’application utilise un système de rôles pour gérer les autorisations d’accès des utilisateurs aux différentes fonctionnalités. Quatre rôles de base sont définis par défaut :

| Rôle            | Description des accès                             |
| --------------- | ------------------------------------------------- |
| **Admin**       | Accès **complet** à toutes les fonctionnalités.    |
| **Gestionnaire**| Peut gérer les **utilisateurs** et les **rôles**. |
| **Prestataire** | Accès limité aux données de son propre compte.    |
| **Client**      | Consultation uniquement de ses informations.      |

Chaque utilisateur possède un champ `role_id` dans la table **users** qui fait référence à l’un de ces rôles (stockés dans la table **roles**). Lors de l’exécution des seeders, les rôles ci-dessus sont insérés en base de données. Le fait de stocker les rôles dans la base de données (plutôt que codés en dur) permet de faire évoluer la liste des rôles ou leurs intitulés sans modifier le code de l’application.

**Gestion des autorisations :** La logique d’autorisation s’appuie sur ces rôles. Par exemple, un middleware dédié (voir section _Middleware_ ci-dessous) peut vérifier le rôle de l’utilisateur connecté et autoriser ou non l’accès à une route (ex: seulement les utilisateurs de rôle Admin peuvent accéder aux routes d’administration). Pour l’instant, les permissions fines (par exemple, autoriser un rôle à effectuer une action spécifique sans créer un nouveau rôle) ne sont pas gérées individuellement. Si des besoins en permissions plus granulaires se présentent, il sera possible d’introduire un système de permissions complémentaire (par exemple via le package **Spatie Laravel Permission**) pour associer aux rôles une liste de permissions stockées en base de données.

En résumé, le système actuel couvre les besoins courants en séparant les utilisateurs par rôles généraux. Toute la gestion des rôles se fait via la base de données (vous pouvez ajouter/modifier des rôles soit par interface d’admin Filament, soit via des migrations/seeders en cas de déploiement). Les contrôles dans le code (middlewares, contrôleurs) se basent sur le rôle de l’utilisateur pour appliquer les restrictions nécessaires.

---

## 📊 Interface d’administration (Filament)

Pour l’interface d’administration, nous avons mis en place **Filament** qui fournit un back-office complet prêt à l’emploi. Filament a été installé via Composer (`composer require filament/filament`) et configuré pour fonctionner avec notre application Laravel. Une fois le projet lancé, le panneau d’administration est accessible à l’adresse :

```
http://127.0.0.1:8000/admin
```

Lorsque vous naviguez vers ce chemin, vous arrivez sur l’écran de **login** de Filament. Utilisez un compte administrateur pour vous authentifier. Par exemple, vous pouvez utiliser le compte administrateur créé par le seeder initial (**test@example.com** avec le mot de passe **password**). Après connexion, vous accédez au tableau de bord Filament.

**Fonctionnalités du panneau d’administration :** L’interface Filament permet aux administrateurs de gérer les ressources principales du système. Par défaut, nous avons créé des pages Filament pour gérer les **Utilisateurs** et les **Rôles**. Depuis le panneau admin, un administrateur peut notamment :

- 📋 **Lister** les utilisateurs enregistrés et créer de nouveaux utilisateurs.
- ✏️ **Éditer** le profil d’un utilisateur (modifier ses informations et son rôle).
- 🔐 **Assigner** un rôle particulier à chaque utilisateur (ou changer son rôle si nécessaire).
- 🗑️ **Supprimer** des utilisateurs ou des rôles (avec les précautions d’usage).
- ➕ **Créer ou modifier des rôles** (par exemple ajouter un rôle si de nouveaux types d’utilisateurs sont requis).

*(À noter : actuellement, toutes les actions d’administration sont liées aux rôles entiers. La modification fine de "permissions" individuelles n’est pas implémentée faute de système de permissions distinct. Le terme « permissions » dans ce contexte réfère aux privilèges conférés par chaque rôle.)*

Les fichiers de configuration et de ressources de Filament se trouvent principalement dans le répertoire `app/Filament/` (par exemple `app/Filament/Resources/UserResource.php` pour la gestion des utilisateurs). Filament crée aussi un **Provider** spécifique (par exemple `app/Providers/Filament/AdminPanelProvider.php`) qui détermine certaines configurations globales du panneau (comme l’URL d’accès `/admin`, les middlewares utilisés par Filament, etc.). Si vous souhaitez personnaliser le comportement de l’admin (changer l’URL d’accès, configurer un thème, etc.), vous pouvez publier le fichier de configuration (`php artisan vendor:publish --tag=filament-config`) ou modifier le code du provider fourni.

**Restriction d’accès à l’admin :** Par défaut, en environnement de développement, tous les utilisateurs authentifiés peuvent accéder au panel Filament. Cependant, pour la production, il est **vivement recommandé** de restreindre cet accès aux seuls administrateurs (ou rôles autorisés). Pour ce faire, on implémente généralement l’interface `Filament\Contracts\FilamentUser` dans le modèle **User**. Cela permet de définir la méthode `canAccessPanel(Panel $panel): bool` et d’y inclure une logique, par exemple :

```php
public function canAccessPanel(\Filament\Panel $panel): bool
{
    return $this->role && $this->role->name === 'admin';
}
```

Avec cette configuration, Filament n’autorisera l’accès au panel `/admin` qu’aux utilisateurs ayant le rôle **Admin**. Cette mesure renforce la sécurité en production en s’assurant que même s’ils sont connectés, les utilisateurs non administrateurs ne pourront pas accéder par inadvertance à l’interface d’administration. 

En résumé, Filament nous offre une interface d’administration clé en main pour Centralis, ce qui accélère le développement (pas besoin de coder toutes les pages d’admin). Grâce à ce panneau, la gestion quotidienne des utilisateurs et des rôles se fait via une UI conviviale plutôt qu’avec des commandes en base de données. 

---

## 🔑 Authentification API (Laravel Sanctum)

Pour l’API, nous avons mis en place **Laravel Sanctum** afin de gérer l’authentification des utilisateurs via des tokens. Sanctum est idéal pour ce projet car il permet de délivrer des **Jetons d’accès personnels** à nos utilisateurs et de sécuriser les endpoints de l’API simplement, sans la complexité d’OAuth.

**Installation & configuration de Sanctum :** Sanctum a été ajouté au projet via Composer (`composer require laravel/sanctum`). Lors de l’installation, Sanctum fournit une migration pour créer la table `personal_access_tokens` qui stockera les jetons générés. Cette migration est exécutée lors du `php artisan migrate --seed` vu précédemment. Nous avons également veillé à ajouter le trait `HasApiTokens` sur le modèle **User** afin de permettre la génération de tokens. Aucune configuration particulière n’a été nécessaire dans `config/sanctum.php` en dehors des valeurs par défaut, le projet étant une API first-party (consommée par un client de confiance, par exemple notre front-end ou outil de test).

**Génération de tokens d’authentification :** Lorsqu’un utilisateur s’enregistre ou se connecte via l’API, l’application génère un token Sanctum pour cet utilisateur. Par exemple, dans le contrôleur d’authentification API, lors d’une requête de **login**, on vérifie les identifiants puis on appelle `$user->createToken('authToken')->plainTextToken`. Ce jeton (une longue chaîne hexadécimale) est renvoyé au client. Côté client, il faudra conserver ce token de manière sécurisée (en mémoire de l’application front, ou dans un stockage sécurisé s’il s’agit d’une application mobile) pour l’utiliser lors des appels suivants.

**Protection des routes :** Les routes d’API qui nécessitent que l’utilisateur soit authentifié sont protégées par le middleware **`auth:sanctum`**. Dans le fichier `routes/api.php`, on regroupe ces routes dans un `Route::middleware('auth:sanctum')->group(...)`. Ainsi, toute requête arrivant sur ces endpoints doit être accompagnée d’un token Sanctum valide dans le header **Authorization**. Si le token est absent ou invalide/expiré, Laravel renverra une réponse **401 Unauthorized** automatiquement, sans même exécuter le contrôleur. 

Concrètement, pour accéder à une ressource protégée, le client doit ajouter un en-tête HTTP :

```
Authorization: Bearer <token_reçu_lors_du_login>
```

Laravel Sanctum va alors identifier l'utilisateur associé à ce token et le considérer comme utilisateur courant (*`Auth::user()`* sera défini). Nous avons également mis en place un middleware de rôle (RoleMiddleware) qui peut s’ajouter en plus de `auth:sanctum` pour certaines routes afin de filtrer par rôle (par exemple s’assurer que seul un **Admin** peut effectuer telle action administrative via l’API).

**Révocation des tokens :** Laravel Sanctum n’impose pas d’expiration automatique des tokens, mais il est possible de les invalider manuellement. Dans notre implémentation, l’endpoint **logout** de l’API utilise `$request->user()->tokens()->delete()` pour révoquer **tous** les tokens actifs de l’utilisateur qui se déconnecte. Ainsi, une fois déconnecté via l’API, le token précédemment fourni ne sera plus accepté par le serveur (il sera supprimé de la base de données). Ceci renforce la sécurité en cas de besoin de déconnexion manuelle ou de rotation de clés.

En résumé, Sanctum nous offre une solution simple : chaque utilisateur peut obtenir un token après authentification, et ce token doit être présenté à chaque requête ultérieure. Cela convient parfaitement pour l’application Centralis, où l’on peut imaginer qu’une interface frontend (ex: un portail web ou mobile) consomme l’API de Laravel en envoyant ces tokens pour accéder aux données selon le rôle de l’utilisateur connecté.

---

## 🧪 Tests API via Insomnia/Postman

Pour vérifier le bon fonctionnement de l’API et de l’authentification, vous pouvez utiliser des outils comme **Insomnia** ou **Postman**. Voici un guide pas-à-pas pour tester les principales fonctionnalités de l’API Centralis :

1. **Configurer l’environnement de test :** Assurez-vous que votre application Laravel tourne (via `php artisan serve` ou via votre serveur web local) et que la base de données est bien configurée. Dans Insomnia/Postman, définissez l’URL de base de vos requêtes (par exemple `http://127.0.0.1:8000`).

2. **Tester l’inscription d’un utilisateur (endpoint `/api/register`) :**  
   Créez une requête **POST** vers l’URL `http://127.0.0.1:8000/api/register`.  
   Dans le corps (body) de la requête, sélectionnez le format **JSON** et fournissez les informations requises. Par exemple :  
   ```json
   {
     "name": "Jean Dupont",
     "email": "jean.dupont@example.com",
     "password": "motdepasse",
     "password_confirmation": "motdepasse",
     "role": "client"
   }
   ```  
   Cet appel tente de créer un nouvel utilisateur avec le rôle **client**. Si tout est correct, l’API devrait renvoyer une réponse **201 Created** avec les données du nouvel utilisateur (et possiblement un token d’authentification dans la réponse). **Remarque :** Dans notre configuration actuelle, pour simplifier, tous les rôles sont autorisés à s’inscrire. En pratique, on pourrait restreindre l’inscription à certains rôles (ex: seuls des Clients peuvent s’inscrire eux-mêmes, les autres rôles étant créés par un admin via l’interface).

3. **Tester la connexion (endpoint `/api/login`) :**  
   Créez une requête **POST** vers `http://127.0.0.1:8000/api/login` avec un corps JSON contenant les identifiants de l’utilisateur. Par exemple, pour utiliser l’utilisateur administrateur par défaut créé lors du seeding :  
   ```json
   {
     "email": "test@example.com",
     "password": "password"
   }
   ```  
   Envoyez la requête. Si les identifiants sont valides, la réponse devrait être de status **200 OK** et contenir le token d’accès. Par exemple :  
   ```json
   {
     "token": "<votre_token_api>",
     "remember_token": "<un_token_optionnel>"
   }
   ```  
   Récupérez la valeur du champ **token** dans la réponse – c’est votre jeton d’authentification API pour les prochaines requêtes. *(Le `remember_token` est également renvoyé à titre d’information, mais n’est pas utile pour les appels API ultérieurs ; il sert surtout pour d’éventuelles fonctionnalités de "remember me" côté Laravel web.)*

4. **Appeler une route protégée avec le token :**  
   Maintenant que vous avez un token, vous pouvez tester les endpoints qui nécessitent d’être connecté. Par exemple, pour **récupérer la liste des utilisateurs** via l’API (fonctionnalité normalement réservée à un admin ou gestionnaire) :  
   - Créez une requête **GET** vers `http://127.0.0.1:8000/api/users`.  
   - Dans les **Headers** de la requête, ajoutez une entrée `"Authorization"` avec pour valeur `"Bearer <votre_token_api>"` (remplacez `<votre_token_api>` par le jeton obtenu à l’étape précédente).  
   - Envoyez la requête. Si votre token est valide et que l’utilisateur associé a les droits nécessaires, la réponse contiendra la liste des utilisateurs en base (format JSON). Par exemple, le compte admin verra tous les utilisateurs. Si un token non valide est envoyé ou si aucun token n’est fourni, vous obtiendrez une réponse **401 Unauthorized**. Si le token est valide mais que l’utilisateur n’a pas le rôle requis (par ex. un **Client** tente d’accéder à la liste des users), l’API renverra généralement **403 Forbidden** (grâce au middleware de rôle qui bloque l’accès).

5. **Tester une action réservée à un rôle spécifique :**  
   Pour vérifier la gestion des rôles via l’API, vous pouvez tenter d’accéder à une route réservée aux admins avec différents comptes :  
   - D’abord, connectez-vous en tant qu’**Admin** (exemple : test@example.com) et récupérez son token. Faites une requête **GET** vers `http://127.0.0.1:8000/api/admin/dashboard` avec le header d’autorisation Bearer. Ce endpoint (fictif dans cet exemple, censé représenter une ressource d’administration) devrait répondre avec des données (ou un message de succès) car l’utilisateur est administrateur.  
   - Ensuite, connectez-vous en tant qu’un utilisateur **Client** (par exemple, en utilisant l’étape d’inscription pour créer un user client, ou en modifiant le rôle de l’utilisateur de test via Filament pour le passer en "client") et obtenez son token. Tentez d’appeler le **même endpoint** `/api/admin/dashboard` avec ce token de client. Cette fois, la réponse devrait être un refus (**403 Forbidden**), car le middleware `RoleMiddleware` intégré à cette route détectera que l’utilisateur n’a pas le rôle *admin*.  
   
   Ce test confirme que la gestion des rôles est effective sur l’API : seules les personnes autorisées par leur rôle peuvent effectuer certaines actions.

6. **Tester la déconnexion (endpoint `/api/logout`) :**  
   Enfin, il est important de tester la révocation de token. Utilisez le token obtenu après connexion (par exemple celui de l’étape 3) et effectuez une requête **POST** vers `http://127.0.0.1:8000/api/logout` avec le header `Authorization: Bearer <token>`. Si le token est valide, la réponse devrait être **200 OK** avec un message de confirmation (e.g. *"Logged out successfully"*). Vérifiez ensuite en réessayant d’appeler une route protégée avec le **même token** – elle devrait désormais renvoyer **401 Unauthorized**, prouvant que le token a bien été invalidé côté serveur.

**Conseils pour Insomnia/Postman :** Pensez à utiliser les fonctionnalités d’environnement de ces outils. Vous pouvez par exemple stocker la valeur du token dans une variable d’environnement après le login, et configurer automatiquement le header d’autorisation pour les requêtes suivantes. Cela simplifie grandement le chainage des appels sans avoir à copier-coller manuellement le token à chaque fois.

En suivant ces étapes, vous devriez être en mesure de valider l’ensemble du flux d’authentification de l’API (inscription, connexion, accès aux ressources protégées selon le rôle, puis déconnexion). N’hésitez pas à créer des scénarios supplémentaires (par ex. modifier un utilisateur via `PUT /api/users/{id}` ou le supprimer via `DELETE /api/users/{id}` en tant qu’Admin) pour couvrir tous les endpoints disponibles.

---

## 🏗️ Meilleures pratiques d’organisation du projet Laravel

Afin d’assurer la maintenabilité et la qualité du projet **Centralis**, voici quelques bonnes pratiques qui ont été suivies (ou à garder en tête) pour l’organisation et la structuration du code Laravel :

- **Respect de l’architecture MVC :** Séparez bien les responsabilités entre les différents composants. Les **Controllers** gèrent la logique applicative et orchestrent les actions (sans surcharger en code métier), les **Models** représentent les données et contiennent éventuellement des méthodes liées à celles-ci, et les **Views** (peu présentes ici car c’est essentiellement une API + panel Filament) servent à l’affichage. Cette séparation facilite la lecture et l’évolution du code.

- **Routes et contrôleurs dédiés :** Organisez vos routes par domaine fonctionnel et utilisez des contrôleurs dédiés pour l’API. Dans ce projet, par exemple, les routes d’API sont définies dans `routes/api.php` et pointent vers des contrôleurs dans le namespace `App\Http\Controllers\API`. Cela évite de mélanger logiques web et API, et rend plus clair le périmètre de chaque contrôleur.

- **Middleware pour la sécurité et l’accès :** Utilisez les **middleware** pour centraliser la gestion de certaines règles transversales. Ici, nous avons recours au middleware `auth:sanctum` pour vérifier l’authentification sur les routes API, et un `RoleMiddleware` personnalisé pour restreindre certaines routes en fonction du rôle utilisateur. C’est une bonne pratique de regrouper ce genre de logique dans des middlewares (ou Policies/Gates) plutôt que de la répéter manuellement dans chaque méthode de contrôleur.

- **Migrations et seeders :** Toute modification de la structure de la base de données est gérée via des **migrations** versionnées, ce qui permet de reproduire l’environnement facilement (important pour les nouveaux développeurs ou les différentes instances du projet). De même, les **seeders** fournissent des données initiales (par exemple les rôles de base, un admin par défaut) utiles pour démarrer rapidement en dev ou en test. Pensez à les mettre à jour si vous ajoutez de nouveaux rôles ou données de référence. Évitez d’insérer manuellement des données essentielles en production sans les répercuter dans un seeder si elles doivent exister sur tous les environnements.

- **Configuration centralisée :** Gardez toutes les valeurs de configuration (accès base de données, clés API externes, etc.) dans le fichier **.env** et le fichier `config/*` correspondant. Ne dispersez pas de « magic strings » ou de constantes non configurables dans le code. Par exemple, le **choix du driver d’authentification API** (Sanctum) est géré via la config, l’URL du panel admin est configurable via Filament, etc. Cela facilite l’adaptation du projet aux différents environnements (dev, staging, production) sans toucher au code.

- **Utilisation de packages & écosystème Laravel :** Ne réinventez pas la roue lorsqu’il existe des solutions robustes dans l’écosystème Laravel. Ce projet en est un exemple : nous avons utilisé Sanctum pour l’authentification API et Filament pour l’admin afin de gagner du temps et bénéficier de composants testés par la communauté. De même, pour des besoins futurs, des packages comme Spatie Permission ou Laravel Scout (recherche) pourront être envisagés. S’appuyer sur ces outils éprouvés renforce la fiabilité et la sécurité du projet tout en réduisant l’effort de développement.

- **Qualité du code :** Maintenez un code lisible et cohérent. Respectez les conventions de nommage Laravel (par exemple, nom des tables au pluriel, contrôleurs en singular avec suffixe Controller, etc.). Le projet inclut **Laravel Pint** pour le formatage automatique du code selon les standards PSR-12 – il est recommandé de l’utiliser régulièrement pour homogénéiser le style du code. Pensez également à des outils comme **PHPStan/Larastan** pour détecter les erreurs potentielles. Documentez les portions complexes du code (via des commentaires ou du README) afin d’aider les futurs mainteneurs.

- **Tests et validation :** Idéalement, implémentez des tests automatisés (tests unitaires pour la logique, tests d’intégration pour les endpoints API). À défaut de tests automatisés complets, utilisez un outil comme Postman/Insomnia pour conserver une collection de requêtes de test et valider rapidement que chaque fonctionnalité API fonctionne après vos modifications. Sur ce projet, chaque fonctionnalité critique (inscription, login, restrictions par rôle) a été manuellement testée via Insomnia – l’objectif futur serait de convertir ces scénarios en tests automatisés pour prévenir les régressions.

En appliquant ces bonnes pratiques, on s’assure que le projet reste **pérenne**, **facilement évolutif**, et compréhensible par tout nouveau développeur rejoignant l’équipe. Laravel fournit une structure par défaut déjà bien pensée, s’y conformer le plus possible est généralement payant en termes de gain de temps et de réduction des bugs.

---

## 🐛 Correction du bug de Middleware dans `bootstrap/app.php`

Lors de la migration vers Laravel 11+ (et donc dans notre cas Laravel 12), le mécanisme d’enregistrement des middleware globaux a changé. Auparavant, les middleware étaient enregistrés dans la classe `App\Http\Kernel`. Désormais, le fichier **`bootstrap/app.php`** est utilisé pour configurer les middleware et les exceptions au niveau de l’application. Cette modification a pu entraîner un bug bien connu si l’on utilise une ancienne méthode de configuration.

**Symptôme du bug :** En essayant de définir des alias de middleware dans `bootstrap/app.php` de la manière suivante (par exemple) :

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting( /* ... */ )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->configure([
            'aliases' => [
                'auth'      => \App\Http\Middleware\Authenticate::class,
                'adminAuth' => \App\Http\Middleware\AdminAuth::class,
                // ...
            ],
        ]);
    })
    ->withExceptions(function(Exceptions $exceptions) { /* ... */ })
    ->create();
```

on obtient une erreur **"Call to undefined method Illuminate\Foundation\Configuration\Middleware::configure()"** au démarrage de l’application. En effet, la méthode `configure()` sur l’objet Middleware n’existe plus dans la nouvelle implémentation.

**Cause :** Laravel a modifié son API interne pour la configuration des middleware. La documentation de Laravel 11/12 indique que l’on doit désormais utiliser la méthode `alias()` au lieu de `configure()` pour définir les alias de middleware. Le code ci-dessus tente donc d’appeler une méthode inexistante, provoquant une erreur fatale lors du bootstrap de l’application.

**Solution :** Pour corriger ce bug, il faut ajuster le code de configuration des middleware dans `bootstrap/app.php`. Voici comment procéder correctement :

```php
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        // ... autres fichiers de routes éventuellement
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // Définition des alias de middleware globaux
            'auth'      => \App\Http\Middleware\Authenticate::class,
            'adminAuth' => \App\Http\Middleware\AdminAuth::class,
            'role'      => \App\Http\Middleware\RoleMiddleware::class,
            // Ajoutez d'autres alias si besoin
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Configuration des gestionnaires d'exceptions globaux si besoin
    })
    ->create();
```

Dans ce code corrigé, on utilise `$middleware->alias([...]);` pour enregistrer nos middleware. La clé de chaque entrée correspond au nom de l’alias que l’on pourra utiliser dans les routes, et la valeur est la classe du middleware. Par exemple, `'role' => \App\Http\Middleware\RoleMiddleware::class` permet ensuite d’utiliser le middleware avec une syntaxe du type `Route::middleware('role:admin')->...` dans nos fichiers de routes (ce qui est fait dans notre `routes/api.php` pour restreindre l’accès à certaines routes aux admins).

Après cette modification, le démarrage de l’application ne génère plus l’erreur et les middleware sont bien pris en compte. 

**Note :** Dans notre projet Centralis, le fichier `bootstrap/app.php` a été configuré avec le code corrigé ci-dessus. Si vous rencontrez une erreur liée aux middleware après une mise à jour de Laravel, assurez-vous de vérifier ce fichier en priorité. Cette évolution de Laravel vise à simplifier la configuration globale, mais peut prêter à confusion lors de la transition depuis les versions antérieures.

En cas de doute, vous pouvez vous référer à la documentation officielle de Laravel (section *Middleware* des notes de release de Laravel 11) qui explique cette nouvelle façon de déclarer les middleware globaux.

---

## 🔧 Troubleshooting (problèmes fréquents et solutions)

Malgré tous les soins apportés, il peut arriver de rencontrer des erreurs lors de l’installation ou de l’utilisation du projet **Centralis**. Voici une liste de problèmes courants et leurs solutions :

- **Erreur de connexion à la base de données (`SQLSTATE[HY000] [1045] Access denied` ou similaire)** – Cette erreur indique que Laravel n’arrive pas à se connecter à MySQL. Vérifiez que les paramètres **DB_HOST**, **DB_DATABASE**, **DB_USERNAME** et **DB_PASSWORD** dans votre fichier **.env** sont corrects. Assurez-vous que MySQL est démarré et que l’utilisateur/mot de passe a les droits d’accès à la base indiquée. Si vous avez modifié le fichier .env, relancez le serveur Laravel (`php artisan serve`) pour prendre en compte les changements, ou exécutez `php artisan config:clear` pour vider le cache de configuration.

- **Clé d’application manquante (`Application key not set`)** – Si vous voyez un message d’erreur mentionnant que la clé n’est pas définie, cela signifie que vous n’avez pas encore exécuté `php artisan key:generate` ou que le fichier .env n’est pas chargé. Génrez la clé avec la commande indiquée dans la section d’installation, puis vérifiez que le fichier **.env** est bien présent à la racine du projet. Sans cette clé, les sessions et le chiffrement ne fonctionneront pas correctement.

- **Erreur 404 en accédant à l’interface `/admin`** – Si après avoir lancé le serveur vous obtenez une page blanche ou une erreur 404 en allant sur `/admin`, plusieurs causes sont possibles : (1) Assurez-vous d’avoir exécuté les migrations/seeders – sans cela, vous n’aurez pas d’utilisateur admin pour vous connecter, mais normalement la page de login devrait s’afficher quand même. (2) Vérifiez que vous avez bien installé les assets front-end (`npm install && npm run build`) – Filament inclut ses propres assets, mais si le projet avait des assets additionnels, un oubli de compilation pourrait poser problème. (3) Si vous êtes en mode **production**, il est possible que l’accès soit refusé car aucun utilisateur n’est autorisé (voir section Filament sur `canAccessPanel`) – en environnement local ce n’est pas filtré. Pensez à créer un utilisateur Admin ou à utiliser le compte de test par défaut. En résumé, la page de login Filament devrait s’afficher sans authentification préalable ; si ce n’est pas le cas, examinez les logs Laravel (*storage/logs*) pour voir si un erreur s’y trouve (par ex. un problème de configuration Filament, ou un chargement d’une classe manquante).

- **Problème de copie du fichier .env sous Windows** – La commande `cp .env.example .env` utilisée dans la documentation ne fonctionne pas dans l’invite de commandes Windows classique. Si vous n’utilisez pas Git Bash ou WSL, utilisez la commande équivalente `copy` :  
  ```batch
  copy .env.example .env
  ```  
  Assurez-vous également que les extensions de fichier sont affichées dans l’explorateur Windows pour éviter de créer un fichier nommé `.env.txt` par mégarde. Après la copie, vous devriez avoir un fichier **.env** (sans extension) dans le dossier du projet.

- **Erreur de classe non trouvée (`Class ... not found` ou `Target class [...] does not exist`)** – Ce message apparaît généralement lorsque Laravel ne parvient pas à instancier une classe spécifiée, souvent un contrôleur ou un modèle. Causes possibles : soit la classe n’existe pas/plus, soit le chemin de la classe a changé sans que le cache ne soit mis à jour, soit il y a une faute de frappe dans le nom. Pour corriger : assurez-vous que le namespace de la classe correspond bien à son chemin (par exemple, un contrôleur dans `App\Http\Controllers\API` doit avoir `namespace App\Http\Controllers\API;` et son nom référencé dans les routes doit correspondre). Après toute création de classe ou changement de namespace, exécutez la commande `composer dump-autoload` pour régénérer l’autoloader de Composer. Enfin, vérifiez dans vos fichiers de routes que vous appelez la classe au bon endroit. Par exemple, si vous avez déplacé un contrôleur, la route doit pointer vers le chemin mis à jour. Ce genre d’erreur est également détectable via `php artisan route:clear` suivi de `php artisan route:list` qui listera les routes actives ou signalera les problèmes de liaison.

- **Erreur liée aux middlewares (ex: méthode configure introuvable)** – Si vous rencontrez une erreur du type *« undefined method Middleware::configure »* ou d’autres problèmes lors du démarrage de l’application concernant les middleware, reportez-vous à la section **Correction du bug de Middleware** ci-dessus. En résumé, pour Laravel 11+, il faut utiliser `$middleware->alias()` dans `bootstrap/app.php` au lieu de méthodes obsolètes. Ce problème ne devrait plus survenir une fois le correctif appliqué, mais il est mentionné ici car c’est une erreur fréquente lors de migrations d’une version Laravel à une autre.

- **Les changements de configuration ne sont pas pris en compte** – Si vous modifiez le fichier .env ou des fichiers de config et que l’application semble ne pas refléter ces changements, il se peut que le cache de configuration soit toujours actif. En environnement de développement, cela arrive en général si vous avez exécuté `php artisan config:cache` à un moment. La solution est d’exécuter `php artisan optimize:clear` pour nettoyer tous les caches (config, route, vues compilées…). Relancez ensuite le serveur. En développement, il est conseillé de ne pas cacher la config tant que l’application est en phase active de modifications.

- **Erreur lors de `npm install` ou `npm run build`** – Si l’installation ou la compilation des assets front-end échoue, vérifiez la version de Node.js installée (Laravel 12 utilise Vite, qui requiert une version récente de Node). Par exemple, des erreurs de dépendances ou de `node-gyp` peuvent signifier que votre Node.js est obsolète ou que des outils de build manquent (sur Windows, installer les **Windows Build Tools** peut s’avérer nécessaire pour compiler certaines dépendances natives). Assurez-vous également d’avoir la connexion internet lors du `npm install` (pour télécharger les packages). En cas de doute, consultez le message d’erreur détaillé dans le terminal, il indique souvent quel module pose problème. Dans la plupart des cas, une mise à jour de Node ou la suppression du dossier **node_modules** suivi d’une nouvelle installation propre résout le souci.

Si d’autres problèmes surviennent, n’oubliez pas de consulter les logs de Laravel (`storage/logs/laravel.log`) qui contiennent souvent le détail des erreurs non affichées à l’écran. En dernier recours, vous pouvez chercher sur internet (StackOverflow, forums Laravel) en utilisant le message précis de l’erreur – la communauté Laravel est active et il y a de fortes chances que quelqu’un ait déjà rencontré (et résolu) un problème similaire.

---

## 📌 Conclusion

En combinant **Laravel**, **Sanctum**, **Filament** et **MySQL**, le projet Centralis dispose d’une base technique solide, moderne et adaptée à une plateforme de marché public. 

- Laravel fournit un cadre structuré, une multitude de fonctionnalités prêtes à l’emploi et une grande communauté de support, ce qui accélère le développement tout en assurant la fiabilité du code. Dans le contexte de Centralis, qui gère potentiellement de nombreux utilisateurs et différentes entités de marché public, Laravel offre l’évolutivité et la maintenabilité nécessaires.

- Laravel Sanctum s’intègre parfaitement pour proposer une authentification API simplifiée et sécurisée. Ce choix technologique est pertinent car Centralis pourrait exposer des services web à d’autres applications (ex: portail web frontend, application mobile, intégration avec des systèmes tiers). Sanctum permet de gérer ces accès via des tokens sans lourdeur supplémentaire, tout en maintenant un haut niveau de sécurité (tokens liés aux utilisateurs, possibilité de les révoquer, etc.).

- Filament, de son côté, apporte une solution d’**administration back-office** clé en main. Pour une plateforme comme Centralis, il est crucial de pouvoir administrer les données (utilisateurs, rôles, éventuellement les marchés, offres, etc.) facilement. Au lieu de développer de zéro un panel d’administration, Filament nous a permis de démarrer rapidement avec un dashboard propre, responsive, et extensible. C’est un gain de temps énorme et cela garantit une cohérence UX pour les administrateurs de la plateforme. De plus, Filament est basé sur Laravel, ce qui assure une intégration fluide avec notre code (utilisation des mêmes modèles, policies, etc.).

- L’usage de **MySQL/MariaDB** comme base de données relationnelle répond aux besoins de persistance de Centralis. Ce SGBD est performant pour gérer un grand nombre d’enregistrements, fiable, et bien supporté par Laravel (via Eloquent). Pour un projet de marché public, où les données doivent être stockées de manière structurée et relationnelle (utilisateurs, rôles, éventuellement catalogues d’appels d’offre, soumissions, etc.), MySQL est un choix éprouvé qui assure intégrité et facilité de requêtage. De plus, la plupart des hébergeurs et DSI maîtrisent cette technologie, ce qui facilite le déploiement en production.

En somme, les technologies retenues offrent un excellent compromis entre **rapidité de développement**, **sécurité** et **efficacité**. Centralis bénéficie d’une architecture modulable : de nouvelles fonctionnalités pourront s’y greffer sans difficulté (grâce à la flexibilité de Laravel et à la structure déjà en place). Par exemple, l’ajout d’un module de notification, d’une gestion plus fine des permissions (via Spatie), ou l’intégration d’une interface publique pour les clients pourra se faire en s’appuyant sur le socle existant.


## Mise à jour du 13/03 : Modifications et Nouvelles Routes API

## 1. Sécurisation de l'Inscription
**Modification apportée :**
- Suppression de la possibilité pour un utilisateur de choisir son propre rôle à l'inscription.
- Attribution automatique du rôle `client` lors de la création d'un compte.

### Route concernée :
**POST** `/api/register`

#### Requête :
```json
{
    "name": "John Doe",
    "email": "johndoe@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Réponse attendue (succès) :
```json
{
    "message": "Utilisateur enregistré avec succès",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "johndoe@example.com",
        "role": "client"
    }
}
```

---

## 2. Mise en Place du Contrôle d'Accès
**Modifications apportées :**
- Vérification des permissions sur les routes API.
- Restriction de l'accès à la liste des rôles aux seuls `admins` et `gestionnaires`.
- Mise à jour du `RoleMiddleware`.

### Middleware : `RoleMiddleware.php`
- Vérifie si l'utilisateur connecté possède le rôle requis pour accéder à une route spécifique.

Exemple d'utilisation dans les routes API :
```php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index']);
});
```

### Route concernée :
**GET** `/api/roles`

#### Réponse attendue (succès) :
```json
[
    {"id": 1, "name": "admin"},
    {"id": 2, "name": "gestionnaire"}
]
```

#### Réponse en cas d'accès non autorisé :
```json
{
    "message": "Accès interdit."
}
```

---

## 3. Séparation de la Mise à Jour des Utilisateurs
### Routes concernées :

1. **PUT** `/api/user/update`
   - Permet à un utilisateur de mettre à jour ses informations personnelles (sans changement de rôle).

#### Requête :
```json
{
    "name": "Jane Doe",
    "email": "janedoe@example.com"
}
```

#### Réponse attendue (succès) :
```json
{
    "message": "Profil mis à jour avec succès",
    "user": {
        "id": 1,
        "name": "Jane Doe",
        "email": "janedoe@example.com"
    }
}
```

2. **PUT** `/api/user/updateRole`
   - Permet uniquement aux `admins` de modifier le rôle d'un utilisateur.

#### Requête :
```json
{
    "user_id": 2,
    "role": "gestionnaire"
}
```

#### Réponse attendue (succès) :
```json
{
    "message": "Rôle mis à jour avec succès",
    "user": {
        "id": 2,
        "name": "User 2",
        "role": "gestionnaire"
    }
}
```

---

## 4. Tests et Validation avec Insomnia
- **Vérification des accès selon les rôles.**
- **Gestion des erreurs HTTP :**
  - `403 Forbidden` (accès interdit si l'utilisateur n'a pas le bon rôle).
  - `404 Not Found` (route ou ressource inexistante).
  - `400 Bad Request` (erreur dans la requête).

Exemple de test d'authentification avec un `Bearer Token` :
- Ajout d'un en-tête `Authorization: Bearer <token>` pour tester les routes protégées.

---

## Mise à jour du 14/03

## 1. Suppression d'un Utilisateur (Admin uniquement)
**Route concernée :**
**DELETE** `/api/users/{id}`

- Seul un **admin** peut supprimer un utilisateur.
- Un utilisateur non admin reçoit un **403 Forbidden**.

#### Réponse attendue (succès) :
```json
{
    "message": "User deleted successfully"
}
```

#### Réponse en cas d'accès non autorisé :
```json
{
    "error": "Unauthorized"
}
```

---

## 2. Vérification de l'accès à Filament
- Restriction de l'accès au **dashboard Filament** aux **admins uniquement**.
- Un **gestionnaire ou un client** reçoit un **403 Forbidden** s'il tente d'y accéder.

---

## 3. Endpoint `/api/user/me`
- Permet de récupérer les informations de l'utilisateur actuellement connecté.

### Route concernée :
**GET** `/api/user/me`

#### Réponse attendue (exemple pour un client) :
```json
{
    "id": 5,
    "name": "User Client",
    "email": "client@example.com",
    "role": "client",
    "created_at": "2025-03-13T10:38:55.000000Z",
    "updated_at": "2025-03-13T10:38:55.000000Z"
}
```

#### Vérification de la sécurité :
- Un utilisateur **ne peut voir que ses propres informations**.
- S'il tente d'accéder au profil d'un autre utilisateur via `GET /api/user/{id}`, il reçoit un **403 Forbidden**.

---

En conclusion, ce stack technologique permet de répondre aux exigences d’une plateforme de marché public moderne, où plusieurs types d’utilisateurs coexistent, où la sécurité des données est primordiale, et où l’administration doit être aisée. **Laravel** apporte la fondation, **Sanctum** la sécurité API, **Filament** l’ergonomie d’administration, le tout formant une solution cohérente et performante pour le projet Centralis.

*N’hésitez pas à contribuer et à améliorer ce projet* – toute suggestion ou amélioration est la bienvenue afin de faire évoluer Centralis ! 🚀
! 🚀