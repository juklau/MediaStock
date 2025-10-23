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
CREATE TABLE `Emprunteur`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `emprunteur_nom` VARCHAR(50) NOT NULL,
    `emprunteur_prenom` VARCHAR(50) NOT NULL,
    `role` ENUM('etudiant(e)', 'intervenant') NOT NULL,
    -- peut être à modifier NOT NULL sur NULL!!!!
    `formation_id` BIGINT UNSIGNED NULL
);
ALTER TABLE
    `Emprunteur` ADD INDEX `emprunteur_formation_id_index`(`formation_id`);
CREATE TABLE `Categorie`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `categorie` VARCHAR(50) NOT NULL
);
CREATE TABLE `Formation`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `formation` VARCHAR(50) NULL
);
CREATE TABLE `Sous_categorie`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `sous_categorie` VARCHAR(50) NOT NULL,
    `categorie_id` BIGINT UNSIGNED NOT NULL
);
ALTER TABLE
    `Sous_categorie` ADD INDEX `sous_categorie_categorie_id_index`(`categorie_id`);
ALTER TABLE
    `Pret` ADD CONSTRAINT `pret_preteur_id_foreign` FOREIGN KEY(`preteur_id`) REFERENCES `Administrateur`(`id`);
ALTER TABLE
    `Emprunteur` ADD CONSTRAINT `emprunteur_formation_id_foreign` FOREIGN KEY(`formation_id`) REFERENCES `Formation`(`id`);
ALTER TABLE
    `Item` ADD CONSTRAINT `item_categorie_id_foreign` FOREIGN KEY(`categorie_id`) REFERENCES `Categorie`(`id`);
ALTER TABLE
    `Pret` ADD CONSTRAINT `pret_emprunteur_id_foreign` FOREIGN KEY(`emprunteur_id`) REFERENCES `Emprunteur`(`id`);
ALTER TABLE
    `Pret` ADD CONSTRAINT `pret_item_id_foreign` FOREIGN KEY(`item_id`) REFERENCES `Item`(`id`);
ALTER TABLE
    `Sous_categorie` ADD CONSTRAINT `sous_categorie_categorie_id_foreign` FOREIGN KEY(`categorie_id`) REFERENCES `Categorie`(`id`);


INSERT INTO `Administrateur`(`login`, `mot_de_passe_hash`) VALUES
('admin', '$2y$10$e0NRyqZJH1QG1k1vZ1h8euXO6jFhY');

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

INSERT INTO `Categorie`(`categorie`) VALUES
('Informatique'),
('Audio'),
('Connectique'),
('Autres');

-- Insertion des sous-catégories avec SELECT pour garantir les bons ID
-- INSERT INTO `Sous_categorie` (`sous_categorie`, `categorie_id`) VALUES
-- ('Souris', (SELECT id FROM Categorie WHERE Categorie = 'Informatique')),
-- ('Clavier', (SELECT id FROM Categorie WHERE Categorie = 'Informatique')),
-- ('Micro-cravate', (SELECT id FROM Categorie WHERE Categorie = 'Audio')),
-- ('Casque', (SELECT id FROM Categorie WHERE Categorie = 'Audio')),
-- ('Cable HDMI', (SELECT id FROM Categorie WHERE Categorie = 'Connectique')),
-- ('Rallonge', (SELECT id FROM Categorie WHERE Categorie = 'Connectique')),
-- ('Adaptateur MAC', (SELECT id FROM Categorie WHERE Categorie = 'Autres')),
-- ('Cle USB', (SELECT id FROM Categorie WHERE Categorie = 'Autres'));

Insert INTO `Item`(`nom`, `model`, `qr_code`, `image_url`, `etat`, `categorie_id`) VALUES
('PC Portable', 'DELL','1','<i class="fa-solid fa-laptop"></i>','moyen','1'),
('PC Portable', 'DELL','2','<i class="fa-solid fa-laptop"></i>','moyen','1'),
('Apple TV', 'MAC','3','<i class="fa-solid fa-tv"></i>','bon','1'),
('Micro cravate', 'QHOT','4','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', 'QHOT','5','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', 'QHOT','6','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', 'QHOT','7','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', 'QHOT','8','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', 'QHOT','9','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', 'QHOT','10','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', 'QHOT','11','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', 'QHOT','12','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', '','13','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', '','14','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', '','15','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', '','16','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro cravate', '','17','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro', 'SENNHEISER','18','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro', 'SENNHEISER','19','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Micro', 'SENNHEISER','20','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Table de mixage ALTO', 'LIVE 1202','21','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Caisse de basses', 'THE BOX','22','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Enceinte', 'THE BOX','23','<i class="fa-solid fa-headphones"></i>','bon','2'),
('Enceinte', 'THE BOX','24','<i class="fa-solid fa-headphones"></i>','bon','2'),
('Transmetteur ', 'SENNHEISER','25','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Transmetteur ', 'SENNHEISER','26','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Transmetteur ', 'SENNHEISER','27','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Transmetteur ', 'SENNHEISER','28','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Transmetteur ', 'SENNHEISER','29','<i class="fa-solid fa-microphone"></i>','bon','2'),
('Casque micro', 'Jabra','30','<i class="fa-solid fa-headphones"></i>','bon','2'),
('Casque micro', 'JCV','31','<i class="fa-solid fa-headphones"></i>','bon','2'),
('Multiprise','9 trou','32','<i class="fa-solid fa-plug"></i>','moyen','3'),
('Multiprise','9 trou','33','<i class="fa-solid fa-plug"></i>','moyen','3'),
('Multiprise','9 trou','34','<i class="fa-solid fa-plug"></i>','mauvais','3'),
('Multiprise','4 trou','35','<i class="fa-solid fa-plug"></i>','moyen','3'),
('Multiprise','3 trou','36','<i class="fa-solid fa-plug"></i>','bon','3'),
('Multiprise','3 trou','37','<i class="fa-solid fa-plug"></i>','bon','3'),
('Ralonge','noire','38','<i class="fa-solid fa-plug"></i>','bon','3'),
('Ralonge','blanche','39','<i class="fa-solid fa-plug"></i>','bon','3'),
('Adaptateur','QDOS USB-C to USBs','40','<i class="fa-solid fa-plug"></i>','mauvais','3'),
('Adaptateur','UGREEN USB-C to USBs','41','<i class="fa-solid fa-plug"></i>','bon','3'),
('Adaptateur','SELORE USB-C to USBs','42','<i class="fa-solid fa-plug"></i>','bon','3'),
('Adaptateur','SELORE USB to USB-C','43','<i class="fa-solid fa-plug"></i>','bon','3'),
('Adaptateur','ACEELE USB to HDMI','44','<i class="fa-solid fa-plug"></i>','bon','3'),
('Adaptateur','SYNCWIRE USB to RJ45','45','<i class="fa-solid fa-plug"></i>','bon','3'),
('Adaptateur','USB-C to USB','46','<i class="fa-solid fa-plug"></i>','bon','3'),
('Adaptateur','ThunderBolt to VGA','47','<i class="fa-solid fa-plug"></i>','bon','3'),
('Adaptateur','ThunderBolt to VGA','48','<i class="fa-solid fa-plug"></i>','bon','3'),
('Adaptateur','ThunderBolt to VGA','49','<i class="fa-solid fa-plug"></i>','bon','3'),
('Adaptateur','ThunderBolt to VGA','50','<i class="fa-solid fa-plug"></i>','bon','3'),
('Adaptateur','ThunderBolt to VGA','51','<i class="fa-solid fa-plug"></i>','bon','3'),
('Adaptateur','ThunderBolt to VGA','52','<i class="fa-solid fa-plug"></i>','bon','3'),
('Chargeur AC','ASUS','53','<i class="fa-solid fa-plug"></i>','bon','3'),
('Chargeur AC','ASUS','54','<i class="fa-solid fa-plug"></i>','bon','3'),
('Câble HDMI','','55','<i class="fa-solid fa-plug"></i>','bon','3'),
('Câble HDMI','','56','<i class="fa-solid fa-plug"></i>','bon','3'),
('Câble HDMI','','57','<i class="fa-solid fa-plug"></i>','bon','3'),
('Câble HDMI','','58','<i class="fa-solid fa-plug"></i>','bon','3'),
('Câble HDMI','','59','<i class="fa-solid fa-plug"></i>','bon','3'),
('Câble HDMI','','60','<i class="fa-solid fa-plug"></i>','bon','3'),
('Câble HDMI','','61','<i class="fa-solid fa-plug"></i>','bon','3'),
('Enceinte Portable','Fenton','62','<i class="fa-solid fa-headphones"></i>','bon','2'),
('Micro','Fenton','63','<i class="fa-solid fa-microphone"></i>','bon','2');