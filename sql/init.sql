USE mediastock;
-- même que le dump.sql du prof
-- il faut enlever les accents !!!
CREATE TABLE `Item`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(50) NOT NULL,
    `model` VARCHAR(50) NULL,
    `qr_code` VARCHAR(255) NOT NULL,
    `image_url` VARCHAR(255) NOT NULL,
    `etat` ENUM('bon', 'moyen', 'mauvais') NOT NULL,
    `categorie_id` BIGINT UNSIGNED NOT NULL
);
ALTER TABLE
    `Item` ADD INDEX `item_categorie_id_index`(`categorie_id`);

CREATE TABLE `Pret`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `item_id` BIGINT UNSIGNED NOT NULL,
    `emprunteur_id` BIGINT UNSIGNED NOT NULL,
    `date_sortie` DATE NOT NULL,
    `date_retour_prevue` DATE NOT NULL,
    `date_retour_effective` DATE NULL,
    `note_debut` VARCHAR(200) NOT NULL,
    `note_fin` VARCHAR(200) NOT NULL,
    `preteur_id` BIGINT UNSIGNED NOT NULL
);
ALTER TABLE
    `Pret` ADD INDEX `pret_item_id_emprunteur_id_preteur_id_index`(
        `item_id`,
        `emprunteur_id`,
        `preteur_id`
    );
ALTER TABLE
    `Pret` ADD INDEX `pret_item_id_index`(`item_id`);
ALTER TABLE
    `Pret` ADD INDEX `pret_emprunteur_id_index`(`emprunteur_id`);
ALTER TABLE
    `Pret` ADD INDEX `pret_preteur_id_index`(`preteur_id`);
CREATE TABLE `Administrateur`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `login` VARCHAR(50) NOT NULL,
    `mot_de_passe_hash` VARCHAR(100) NOT NULL
);
CREATE TABLE `emprunteur`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `emprunteur_nom` VARCHAR(50) NOT NULL,
    `emprunteur_prenom` VARCHAR(50) NOT NULL,
    `role` ENUM('etudiant(e)', 'intervenant') NOT NULL,
    -- peut être à modifier NOT NULL sur NULL!!!!
    `formation_id` BIGINT UNSIGNED NOT NULL
);
ALTER TABLE
    `emprunteur` ADD INDEX `emprunteur_formation_id_index`(`formation_id`);
CREATE TABLE `categorie`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `categorie` VARCHAR(50) NOT NULL
);
CREATE TABLE `Formation`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `formation` VARCHAR(50) NULL
);
CREATE TABLE `sous_categorie`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `sous_categorie` VARCHAR(50) NOT NULL,
    `categorie_id` BIGINT UNSIGNED NOT NULL
);
ALTER TABLE
    `sous_categorie` ADD INDEX `sous_categorie_categorie_id_index`(`categorie_id`);
ALTER TABLE
    `Pret` ADD CONSTRAINT `pret_preteur_id_foreign` FOREIGN KEY(`preteur_id`) REFERENCES `Administrateur`(`id`);
ALTER TABLE
    `emprunteur` ADD CONSTRAINT `emprunteur_formation_id_foreign` FOREIGN KEY(`formation_id`) REFERENCES `Formation`(`id`);
ALTER TABLE
    `Item` ADD CONSTRAINT `item_categorie_id_foreign` FOREIGN KEY(`categorie_id`) REFERENCES `categorie`(`id`);
ALTER TABLE
    `Pret` ADD CONSTRAINT `pret_emprunteur_id_foreign` FOREIGN KEY(`emprunteur_id`) REFERENCES `emprunteur`(`id`);
ALTER TABLE
    `Pret` ADD CONSTRAINT `pret_item_id_foreign` FOREIGN KEY(`item_id`) REFERENCES `Item`(`id`);
ALTER TABLE
    `sous_categorie` ADD CONSTRAINT `sous_categorie_categorie_id_foreign` FOREIGN KEY(`categorie_id`) REFERENCES `categorie`(`id`);


INSERT INTO `Administrateur`(`login`, `mot_de_passe_hash`) VALUES
('admin', '$2y$10$e0NRyqZJH1QG1k1vZ1h8euXO6jFhYz1Zz1Zz1Zz1Zz1Zz1Zz1Zz1Zz1Zz1Zz1Zz');

INSERT INTO `Formation`(`formation`) VALUES
('ECS1'),
('ECS2'),
('ECS3 A Brand Digit'),
('ECS3 B Com Event'),
('ECS4 A Brand Digit'),
('ECS4 B Com Event'),
('ECS4 DA'),
('ECS5 Com Digit'),
('ECS5 Com Event'),
('NSS 1'),
('NSS 2'),
('PSL 1'),
('PSL 2'),
('PSL 3'),
('Iris 1'),
('Iris 2');

INSERT INTO `categorie`(`categorie`) VALUES
('Informatique'),
('Audio'),
('Connectique'),
('Autres');


-- Insertion des sous-catégories avec SELECT pour garantir les bons ID
INSERT INTO `sous_categorie` (`sous_categorie`, `categorie_id`) VALUES
('Souris', (SELECT id FROM categorie WHERE categorie = 'Informatique')),
('Clavier', (SELECT id FROM categorie WHERE categorie = 'Informatique')),
('Micro-cravate', (SELECT id FROM categorie WHERE categorie = 'Audio')),
('Casque', (SELECT id FROM categorie WHERE categorie = 'Audio')),
('Cable HDMI', (SELECT id FROM categorie WHERE categorie = 'Connectique')),
('Rallonge', (SELECT id FROM categorie WHERE categorie = 'Connectique')),
('Adaptateur MAC', (SELECT id FROM categorie WHERE categorie = 'Autres')),
('Cle USB', (SELECT id FROM categorie WHERE categorie = 'Autres'));
