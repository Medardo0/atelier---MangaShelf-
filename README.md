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

## Avancement

## Avancement

- [x] Session 1 — Cadrage et documentation
- [x] Session 2 — Wireframes
- [x] Session 3 — HTML sémantique (pages publiques)
- [x] Session 4 — HTML administration
- [x] Session 5 — Base HTML finalisée
- [x] Session 6 — Architecture MVC et cartographie des URL
- [ ] Session 7
- [ ] Session 8
- [ ] Session 9
- [ ] Session 10
- [ ] Session 11
