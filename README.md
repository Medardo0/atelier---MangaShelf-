# MangaShelf

Catalogue en ligne de mangas — projet de fin de formation

## Description

MangaShelf est un site web dynamique permettant de consulter et filtrer
un catalogue de mangas. Il propose un espace public de navigation et
de collections personnelles, ainsi qu'un back-office d'administration.

## Public visé

Tous publics — amateurs et passionnés de manga (adolescents et adultes).

## Objectifs principaux

- Consulter et filtrer un catalogue de mangas par genre et tags
- Accéder aux fiches détaillées (auteur, synopsis, tomes, couverture)
- Gérer une collection personnelle (favoris, wishlist, lus)
- Administrer le catalogue via un back-office sécurisé (MVC, PHP 8.1+)

## Technologies

PHP 8.1+ | MySQL | HTML5 | CSS3 | JavaScript ES6+ | Architecture MVC

## Structure des fichiers

```
public/
├── index.html          ← Accueil
├── catalogue.html      ← Catalogue avec filtres
├── manga.html          ← Fiche détail
├── contact.html        ← Formulaire de contact
├── connexion.html      ← Connexion utilisateur
└── inscription.html    ← Inscription utilisateur

admin/
├── login.html          ← Connexion administrateur
├── dashboard.html      ← Tableau de bord
├── manga-form.html     ← Création / édition d'un manga
└── messages.html       ← Gestion des messages
```

## Architecture

Le projet suit une architecture MVC simple :

- `public/` → Point d'entrée unique (index.php) + assets
- `app/controllers/` → Contrôleurs (logique métier)
- `app/models/` → Modèles (accès base de données via PDO)
- `app/views/` → Vues (templates PHP)
- `core/` → Router, classes mères Controller et Model
- `config/` → Configuration et définition des routes

Seul le dossier `public/` est exposé au navigateur.
Toutes les requêtes sont redirigées vers `public/index.php`
via la règle de réécriture Apache dans `public/.htaccess`.

### Base de données

- Schéma SQL complet importé dans phpMyAdmin (`mangashelf_schema.sql`)
- 8 tables : `operator`, `item`, `item_tag`, `tag`, `message`, `collection`, `collection_item`, `search`
- Données de test : 6 mangas, 13 genres/tags, 1 admin, 1 utilisateur, 3 messages

### Authentification (`core/Auth.php`)

- `session_start()` centralisé
- `require_auth()` — protection des pages admin
- `is_logged_in()` — vérifie si un utilisateur est connecté
- `csrf_token()` / `verify_csrf()` — protection des formulaires POST
- Expiration de session après inactivité

### Base de données (`core/Database.php`)

- Singleton PDO — une seule connexion partagée
- Gestion des erreurs avec `error_log()`

### Router MVC (`core/http.php`, `core/router.php`, `core/html.php`)

- `http_in()` — lit `$_SERVER['REQUEST_URI']` et retourne des segments propres
- `route()` — applique la convention `/entity/action/id`
- `is_safe_segment()` — valide les segments avant construction des noms de fichiers
- `run()` — charge le controller et appelle la fonction correspondante
- `render()` — charge une vue, injecte les données, retourne le HTML
- `render_in_layout()` — imbrique une vue dans un layout
- `http_out()` — seul endroit qui fait `echo`

### Controllers mis en place

- `app/controllers/home.php` — page d'accueil
- `app/controllers/catalogue.php` — liste des mangas
- `app/controllers/manga.php` — fiche détail
- `app/controllers/admin.php` — login, dashboard, logout

### Bugs corrigés

- `?>` dans un commentaire PHP causait une erreur de parsing
- 404 sur l'URL de base — résolu avec `RewriteBase` dans `.htaccess`
- Statut `on_hold` manquant dans l'ENUM de la table `item`
- Mot de passe root MySQL réinitialisé après restauration du dossier `data`

## Avancement

- [x] Session 1 — Cadrage et documentation
- [x] Session 2 — Wireframes
- [x] Session 3 — HTML sémantique (pages publiques)
- [x] Session 4 — HTML administration
- [x] Session 5 — Base HTML finalisée
- [x] Session 6 — Architecture MVC et cartographie des URL
- [x] Session 7 — Système de login (Auth, sessions, CSRF)
- [x] Session 8 — Router MVC (http_in, route, run, render)
- [ ] Session 9 — Modèles et connexion BDD
- [ ] Session 10 — CSS
- [ ] Session 11 — Tests et déploiement
