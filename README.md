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

- Schéma SQL : `database.sql` à importer dans phpMyAdmin
- 7 tables : `operator`, `item`, `item_tag`, `tag`, `message`, `collection`, `collection_item`
- Données de base : 1 admin (`admin` / `password`)

### Router MVC (`core/http.php`, `core/router.php`, `core/html.php`)

- `http_in()` — lit `$_SERVER['REQUEST_URI']` et retourne des segments propres
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

- `app/models/manga.php` — lecture publique + CRUD admin (create, update, delete, slug, sync tags, upload couverture)
- `app/models/tag.php` — lecture publique + CRUD admin (create, update, delete)
- `app/models/operator.php` — inscription, connexion, verrouillage
- `app/models/collection.php` — listes utilisateur, appartenance manga, ajout/retrait
- `app/models/message.php` — création, liste, lecture, suppression, compteur non-lus

### Controllers

| Controller | Fonctions | Description |
|---|---|---|
| `home.php` | `home_index()` | Page d'accueil — mangas récents + genres |
| `catalogue.php` | `catalogue_index()` | Catalogue filtrable + pagination |
| `manga.php` | `manga_show()` | Fiche détail + collections + mangas similaires |
| `auth.php` | `auth_connexion()`, `auth_inscription()`, `auth_logout()` | Auth utilisateur public |
| `collection.php` | `collection_index()`, `collection_add()`, `collection_remove()` | Collections personnelles |
| `contact.php` | `contact_index()` | Formulaire de contact public |
| `admin.php` | `admin_login()`, `admin_dashboard()`, `admin_logout()` | Session admin |
| `admin.php` | `admin_mangas()`, `admin_manga_create()`, `admin_manga_edit()`, `admin_manga_delete()` | CRUD mangas |
| `admin.php` | `admin_genres()`, `admin_genre_create()`, `admin_genre_edit()`, `admin_genre_delete()` | CRUD genres/tags |
| `admin.php` | `admin_messages()`, `admin_message_show()`, `admin_message_delete()` | Gestion messages |

### Catalogue (`/catalogue`)

Grammaire URL choisie : **query string** (`/catalogue?q=...&genres[]=...&page=2&sort=title`)

- Filtres : recherche texte (titre + auteur), genres (checkboxes), tri, page
- Pagination : 12 mangas par page, navigation avec conservation des filtres (`http_build_query`)
- `manga_count()` pour le total, `manga_get_all()` avec `LIMIT`/`OFFSET`
- Barre de recherche rapide dans le header (soumet vers `/catalogue?q=`)

### Collections utilisateur (`/collection`)

- 4 collections créées automatiquement à l'inscription : Favoris, Wishlist, En cours, Terminés
- Boutons sur la fiche manga : rouge = déjà dans la collection (clic → retirer), gris = absent (clic → ajouter)
- Sécurité : `collection_belongs_to_user()` avant tout ajout/retrait + vérification CSRF
- Page `/collection` liste les 4 collections avec les mangas et boutons de retrait

### CRUD admin

#### Mangas (`/admin/manga_*`)

- Liste avec statut, nombre de tomes, couverture
- Création/édition : titre, auteur, synopsis, statut, genres, tags (checkboxes), upload couverture
- Upload couverture : validation MIME via `finfo` (JPG/PNG/WebP, max 2 Mo), slug-based filename
- Suppression : confirmation JS, suppression des liens `item_tag` en cascade
- Actions via noms composés (`manga_create`, `manga_edit`, `manga_delete`) — pas d'extension du router

#### Genres & Tags (`/admin/genres`)

- CRUD complet : nom, slug auto-généré, type (genre ou tag)
- Suppression des liens `item_tag` en cascade avant suppression du tag

#### Messages (`/admin/messages`)

- Liste : non-lus en gras, compteur dans la sidebar
- Vue détail : marquage automatique comme lu à l'ouverture
- Suppression avec confirmation

### Formulaire de contact (`/contact`)

- Champs : nom, email, sujet, corps du message
- Validation serveur avec affichage des erreurs
- Stockage en base via `message_create()`
- Redirection `?sent=1` après envoi

### Bugs corrigés

- `?>` dans un commentaire PHP causait une erreur de parsing dans `core/html.php`
- 404 "controller 'mangashelf' does not exist" — préfixe `/mangashelf/public` non strippé dans `index.php`
- `SQLSTATE[HY093] Invalid parameter number` — paramètre PDO `:q` utilisé deux fois → remplacé par `:q_title` et `:q_author`
- `home.php` contenait `catalogue_index()` au lieu de `home_index()` — 404 sur la page d'accueil
- Redirections admin pointaient vers `/admin/dashboard` au lieu de `/mangashelf/public/admin/dashboard`
- Parse error `list.php` : caractère `»` à l'intérieur d'un bloc `<?=` — corrigé en pré-construisant la chaîne dans une variable PHP
- Vues `admin/genres` et `admin/messages` manquantes — créées avec leurs controllers

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
- [x] Collections utilisateur (favoris, wishlist, en cours, terminés)
- [x] CRUD admin mangas (liste, création, édition, suppression + upload couverture)
- [x] CRUD admin genres & tags
- [x] Gestion admin des messages de contact
- [x] Formulaire de contact public
- [x] CSS complet (palette rouge/blanc/noir, cards manga, responsive)
- [x] Hero slider (3 slides avec animation, flèches, dots, swipe tactile)
- [x] Barre de recherche dans le header
- [ ] Tests et déploiement

## Ce qu'il reste à faire

### Tests et déploiement

- [ ] Tests manuels des parcours principaux (inscription → collection → déconnexion)
- [ ] Mise en production (hébergement, `.htaccess`, variables d'environnement)
- [ ] Suppression des données de test avant mise en ligne
- [ ] Vérification `.htaccess` sur serveur de production

## Installation locale

1. Cloner le dépôt dans le dossier servi par Apache
2. Configurer le virtual host local `mangashelf.local` vers le dossier `public/`
3. Démarrer Apache et MySQL via XAMPP
4. Importer `database.sql` dans phpMyAdmin (base : `mangashelf`)
5. Accéder à `http://mangashelf.local/`

Identifiants admin : `admin` / `password`
