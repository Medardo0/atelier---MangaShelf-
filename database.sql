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
    slug  VARCHAR(100)  NOT NULL,
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
