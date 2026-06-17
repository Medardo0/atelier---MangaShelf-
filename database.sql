CREATE DATABASE IF NOT EXISTS mangashelf CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mangashelf;

CREATE TABLE IF NOT EXISTS item (
    id                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    title             VARCHAR(255)    NOT NULL,
    slug              VARCHAR(255)    NOT NULL UNIQUE,
    author            VARCHAR(255)    NOT NULL DEFAULT '',
    volumes           SMALLINT        NOT NULL DEFAULT 0,
    series_status     VARCHAR(50)     NOT NULL DEFAULT 'ongoing',
    content           TEXT,
    short_description TEXT,
    main_image        VARCHAR(255)    DEFAULT NULL,
    status            VARCHAR(20)     NOT NULL DEFAULT 'published',
    created_at        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tag (
    id    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name  VARCHAR(100)  NOT NULL,
    slug  VARCHAR(100)  NOT NULL UNIQUE,
    type  VARCHAR(20)   NOT NULL DEFAULT 'tag',
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS item_tag (
    item_id INT UNSIGNED NOT NULL,
    tag_id  INT UNSIGNED NOT NULL,
    PRIMARY KEY (item_id, tag_id),
    FOREIGN KEY (item_id) REFERENCES item(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id)  REFERENCES tag(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS operator (
    id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    username        VARCHAR(100)  NOT NULL UNIQUE,
    email           VARCHAR(255)  NOT NULL UNIQUE,
    password        VARCHAR(255)  NOT NULL,
    role            VARCHAR(20)   NOT NULL DEFAULT 'user',
    failed_attempts TINYINT       NOT NULL DEFAULT 0,
    locked_until    DATETIME      DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS collection (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    operator_id INT UNSIGNED NOT NULL,
    type        VARCHAR(20)  NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_operator_collection_type (operator_id, type),
    FOREIGN KEY (operator_id) REFERENCES operator(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS collection_item (
    collection_id INT UNSIGNED NOT NULL,
    item_id       INT UNSIGNED NOT NULL,
    PRIMARY KEY (collection_id, item_id),
    FOREIGN KEY (collection_id) REFERENCES collection(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id)       REFERENCES item(id)       ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS message (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    sender_name  VARCHAR(150)  NOT NULL,
    sender_email VARCHAR(255)  NOT NULL,
    subject      VARCHAR(255)  NOT NULL DEFAULT '',
    body         TEXT          NOT NULL,
    is_read      TINYINT(1)    NOT NULL DEFAULT 0,
    created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Compte admin par defaut : admin / password
INSERT INTO operator (username, email, password, role)
VALUES ('admin', 'admin@mangashelf.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE id = id;

INSERT INTO tag (name, slug, type) VALUES
('Action', 'action', 'genre'),
('Aventure', 'aventure', 'genre'),
('Comedie', 'comedie', 'genre'),
('Drame', 'drame', 'genre'),
('Fantasy', 'fantasy', 'genre'),
('Mystere', 'mystere', 'genre'),
('Shonen', 'shonen', 'genre'),
('Slice of life', 'slice-of-life', 'genre'),
('Classique', 'classique', 'tag'),
('Combat', 'combat', 'tag'),
('Nouveaute', 'nouveaute', 'tag'),
('Pirates', 'pirates', 'tag'),
('Recommande', 'recommande', 'tag')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    type = VALUES(type);

INSERT INTO item (title, slug, author, volumes, series_status, content, short_description, main_image, status) VALUES
(
    'One Piece',
    'one-piece',
    'Eiichiro Oda',
    108,
    'ongoing',
    'Monkey D. Luffy part en mer pour devenir le roi des pirates et trouver le tresor legendaire One Piece.',
    'Une grande aventure de pirates pleine d''humour, de combats et d''amitie.',
    'one-piece-b652bf5b.jpg',
    'published'
),
(
    'Kagurabachi',
    'kagurabachi',
    'Takeru Hokazono',
    2,
    'ongoing',
    'Chihiro poursuit une vengeance liee aux sabres forges par son pere.',
    'Un shonen sombre centre sur des sabres, la vengeance et la magie.',
    'kagurabachi-8a2cc974.jpg',
    'published'
),
(
    'Naruto',
    'naruto',
    'Masashi Kishimoto',
    72,
    'completed',
    'Naruto Uzumaki reve de devenir Hokage pour etre reconnu par son village.',
    'Un recit initiatique de ninjas, de rivalites et de perseverance.',
    NULL,
    'published'
),
(
    'Fullmetal Alchemist',
    'fullmetal-alchemist',
    'Hiromu Arakawa',
    27,
    'completed',
    'Deux freres alchimistes cherchent la pierre philosophale pour reparer les consequences d''une transmutation interdite.',
    'Une aventure fantasy dense, entre action, drame et alchimie.',
    NULL,
    'published'
),
(
    'Death Note',
    'death-note',
    'Tsugumi Ohba',
    12,
    'completed',
    'Light Yagami trouve un carnet capable de tuer toute personne dont le nom y est inscrit.',
    'Un duel psychologique sombre autour de la justice et du pouvoir.',
    NULL,
    'published'
),
(
    'Yotsuba&!',
    'yotsuba',
    'Kiyohiko Azuma',
    15,
    'ongoing',
    'Yotsuba decouvre le quotidien avec curiosite, energie et beaucoup de naturel.',
    'Une serie douce et drole sur les petites aventures du quotidien.',
    NULL,
    'published'
)
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    author = VALUES(author),
    volumes = VALUES(volumes),
    series_status = VALUES(series_status),
    content = VALUES(content),
    short_description = VALUES(short_description),
    main_image = VALUES(main_image),
    status = VALUES(status);

INSERT INTO item_tag (item_id, tag_id)
SELECT i.id, t.id
FROM (
    SELECT 'one-piece' AS item_slug, 'action' AS tag_slug UNION ALL
    SELECT 'one-piece', 'aventure' UNION ALL
    SELECT 'one-piece', 'shonen' UNION ALL
    SELECT 'one-piece', 'pirates' UNION ALL
    SELECT 'one-piece', 'recommande' UNION ALL
    SELECT 'kagurabachi', 'action' UNION ALL
    SELECT 'kagurabachi', 'shonen' UNION ALL
    SELECT 'kagurabachi', 'combat' UNION ALL
    SELECT 'kagurabachi', 'nouveaute' UNION ALL
    SELECT 'naruto', 'action' UNION ALL
    SELECT 'naruto', 'aventure' UNION ALL
    SELECT 'naruto', 'shonen' UNION ALL
    SELECT 'naruto', 'classique' UNION ALL
    SELECT 'fullmetal-alchemist', 'aventure' UNION ALL
    SELECT 'fullmetal-alchemist', 'drame' UNION ALL
    SELECT 'fullmetal-alchemist', 'fantasy' UNION ALL
    SELECT 'fullmetal-alchemist', 'recommande' UNION ALL
    SELECT 'death-note', 'drame' UNION ALL
    SELECT 'death-note', 'mystere' UNION ALL
    SELECT 'death-note', 'classique' UNION ALL
    SELECT 'yotsuba', 'comedie' UNION ALL
    SELECT 'yotsuba', 'slice-of-life' UNION ALL
    SELECT 'yotsuba', 'recommande'
) AS links
JOIN item i ON i.slug = links.item_slug
JOIN tag t ON t.slug = links.tag_slug
ON DUPLICATE KEY UPDATE item_id = item_id;
