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

## Points vérifiés

- Hiérarchie des titres cohérente (h1 unique par page)
- Lien d'évitement sur toutes les pages
- Balises sémantiques : header, nav, main, section, article, aside, footer, ol
- Tous les label associés à leur champ (for/id)
- Fieldset + legend sur tous les groupes de champs
- Formulaires avec novalidate + csrf_token
- Tables avec caption + scope sur les en-têtes
- Fil d'ariane en ol sur les pages profondes
- aria-current="page" sur le lien actif
- role="alert" + aria-live sur les zones de messages dynamiques
- meta robots noindex sur les pages admin
