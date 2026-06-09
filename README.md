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

## Architecture

Le projet suit une architecture MVC sans framework :

- `public/` → Point d'entrée unique (`index.php`) + assets
- `app/controllers/` → Contrôleurs (logique métier)
- `app/models/` → Modèles (accès base de données via PDO)
- `app/views/` → Vues (templates PHP)
- `core/` → Router, Auth, Database, helpers HTML
- `config/` → Configuration BDD et routes

Seul le dossier `public/` est exposé au navigateur.
Toutes les requêtes sont redirigées vers `public/index.php`
via la règle de réécriture Apache dans `public/.htaccess`.

### Base de données

- Schéma SQL : `mangashelf_schema.sql` à importer dans phpMyAdmin
- 8 tables : `operator`, `item`, `item_tag`, `tag`, `message`, `collection`, `collection_item`, `search`
- Données de test : 6 mangas, 13 genres/tags, 1 admin (`admin` / `password`), 1 utilisateur, 3 messages

### Router MVC (`core/http.php`, `core/router.php`, `core/html.php`)

- `http_in()` — lit `$_SERVER['REQUEST_URI']` et retourne des segments propres (strip du préfixe `/mangashelf/public`)
- `route()` — convention `/entity/action/id`
- `is_safe_segment()` — valide les segments avant construction des noms de fichiers
- `run()` — charge le controller et appelle la fonction correspondante
- `render()` / `render_in_layout()` — charge une vue, injecte les données via `extract()`
- `http_out()` — seul endroit qui fait `echo`

### Authentification (`core/Auth.php`)

- `session_start()` centralisé
- `csrf_token()` / `verify_csrf()` — protection des formulaires POST
- `is_logged_in()` — vérifie si un utilisateur public est connecté
- `current_role()` — retourne le rôle de la session
- `require_auth()` — protection des pages admin + expiration de session
- Verrouillage du compte après 5 tentatives échouées (`locked_until`)

### Base de données (`core/Database.php`)

- Singleton PDO — une seule connexion partagée
- `ATTR_EMULATE_PREPARES = false` — paramètres nommés non réutilisables dans la même requête
- Gestion des erreurs avec `error_log()`

### Modèles

- `app/models/manga.php` — `manga_get_all()`, `manga_count()`, `manga_get_one()`, `manga_get_recent()`, `manga_get_similar()`
- `app/models/tag.php` — `tag_get_genres()`, `tag_get_all()`
- `app/models/operator.php` — `operator_find_by_email()`, `operator_create()`, `operator_increment_failed()`, `operator_reset_failed()`

### Controllers

| Controller | Fonctions | Description |
|---|---|---|
| `home.php` | `home_index()` | Page d'accueil — mangas récents + genres |
| `catalogue.php` | `catalogue_index()` | Catalogue filtrable + pagination |
| `manga.php` | `manga_show()` | Fiche détail + mangas similaires |
| `auth.php` | `auth_connexion()`, `auth_inscription()`, `auth_logout()` | Auth utilisateur public |
| `admin.php` | `admin_login()`, `admin_dashboard()`, `admin_logout()` | Back-office admin |

### Catalogue (`/catalogue`)

Grammaire URL choisie : **query string** (`/catalogue?q=...&genres[]=...&page=2&sort=title`)

- Filtres : recherche texte (titre + auteur), genres (checkboxes), tri, page
- Pagination : 12 mangas par page, navigation avec conservation des filtres (`http_build_query`)
- `manga_count()` pour le total, `manga_get_all()` avec `LIMIT`/`OFFSET`

### Authentification utilisateur public

- Inscription : validation email, longueur mot de passe, unicité email/pseudo, `password_hash()`
- Connexion : `password_verify()`, verrouillage après 5 échecs
- Déconnexion : POST avec CSRF, destruction de session
- Création automatique des 4 collections à l'inscription (favoris, wishlist, en cours, terminés)

### Bugs corrigés

- `?>` dans un commentaire PHP causait une erreur de parsing dans `core/html.php`
- 404 "controller 'mangashelf' does not exist" — préfixe `/mangashelf/public` non strippé dans `index.php`
- `SQLSTATE[HY093] Invalid parameter number` — paramètre PDO `:q` utilisé deux fois → remplacé par `:q_title` et `:q_author`
- `home.php` contenait `catalogue_index()` au lieu de `home_index()` — 404 sur la page d'accueil
- Schéma SQL : genres Isekai/Action/Romance/Horreur classifiés en `type='tag'`, Berserk `on_hold`

## Avancement

- [x] Session 1 — Cadrage et documentation
- [x] Session 2 — Wireframes
- [x] Session 3 — HTML sémantique (pages publiques)
- [x] Session 4 — HTML administration
- [x] Session 5 — Base HTML finalisée
- [x] Session 6 — Architecture MVC et cartographie des URL
- [x] Session 7 — Système de login (Auth, sessions, CSRF)
- [x] Session 8 — Router MVC (http_in, route, run, render)
- [x] Session 9 — Modèles et connexion BDD (PDO, manga/tag/operator models)
- [x] Session 10 — Authentification utilisateur public (connexion, inscription, déconnexion)
- [x] Session 11 — Pagination du catalogue
- [ ] Collections utilisateur
- [ ] CRUD admin mangas
- [ ] CSS / mise en page
- [ ] Formulaire de contact
- [ ] Tests et déploiement

## Ce qu'il reste à faire

### Collections utilisateur (`/collection`)

- [ ] `app/models/collection.php` — `collection_get_by_user()`, `collection_item_add()`, `collection_item_remove()`, `collection_item_exists()`
- [ ] `app/controllers/collection.php` — afficher, ajouter, retirer un manga d'une collection
- [ ] Boutons "Ajouter aux favoris / wishlist / en cours / terminés" sur la fiche manga
- [ ] Page collections de l'utilisateur connecté
- [ ] Protéger ces routes avec `require_auth()`

### CRUD admin mangas (`/admin/manga/...`)

- [ ] Étendre le router pour supporter 4 segments (`/admin/manga/edit/42`)
- [ ] `app/models/manga.php` — `manga_create()`, `manga_update()`, `manga_delete()`
- [ ] `app/controllers/admin.php` — `admin_manga_list()`, `admin_manga_create()`, `admin_manga_edit()`, `admin_manga_delete()`
- [ ] Formulaire d'ajout / édition manga (titre, auteur, synopsis, genres, tags, couverture)
- [ ] Upload de couverture (image)

### CSS / mise en page

- [ ] Style global (typographie, couleurs, espacement)
- [ ] Responsive mobile
- [ ] Style du catalogue (grille de cards)
- [ ] Style des formulaires
- [ ] Style de la pagination

### Formulaire de contact

- [ ] `app/controllers/contact.php` — afficher le formulaire, traiter l'envoi
- [ ] Enregistrement en BDD (table `message`) ou envoi par email
- [ ] Page de confirmation

### Tests et déploiement

- [ ] Tests manuels des parcours principaux
- [ ] Mise en production (hébergement, `.htaccess`, variables d'environnement)
- [ ] Suppression des données de test avant mise en ligne

## Installation locale

1. Cloner le dépôt dans `htdocs/mangashelf/`
2. Démarrer Apache et MySQL via XAMPP
3. Importer `mangashelf_schema.sql` dans phpMyAdmin (base : `mangashelf`)
4. Accéder à `http://localhost/mangashelf/public/`

Identifiants admin : `admin` / `password`
