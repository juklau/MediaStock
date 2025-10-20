CREATE TABLE `Item`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(50) NOT NULL,
    `model` VARCHAR(50) NULL,
    `qr_code` VARCHAR(255) NOT NULL,
    `image_url` VARCHAR(255) NOT NULL,
    `état` ENUM('') NOT NULL,
    `categorie_id` BIGINT NOT NULL
);
ALTER TABLE `Item` ADD INDEX `item_categorie_id_index`(`categorie_id`);

CREATE TABLE `Pret`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `item_id` BIGINT NOT NULL,
    `emprenteur_id` BIGINT NOT NULL,
    `date_sortie` DATE NOT NULL,
    `date_retour_prévue` DATE NOT NULL,
    `date_retour_effective` DATE NULL,
    `note_debut` VARCHAR(200) NOT NULL,
    `note_fin` VARCHAR(200) NOT NULL,
    `preteur_id` BIGINT NOT NULL
);
ALTER TABLE `Pret` ADD INDEX `pret_item_id_emprenteur_id_preteur_id_index`(
        `item_id`,
        `emprenteur_id`,
        `preteur_id`
    );
ALTER TABLE `Pret` ADD INDEX `pret_item_id_index`(`item_id`);
ALTER TABLE `Pret` ADD INDEX `pret_emprenteur_id_index`(`emprenteur_id`);
ALTER TABLE `Pret` ADD INDEX `pret_preteur_id_index`(`preteur_id`);
CREATE TABLE `Administrateur`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `login` VARCHAR(50) NOT NULL,
    `mot_de_passe_hash` VARCHAR(100) NOT NULL
);
CREATE TABLE `emprunteur`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `emprunteur_nom` VARCHAR(50) NOT NULL,
    `emprunteur_prenom` VARCHAR(50) NOT NULL,
    `role` ENUM('') NOT NULL,
    `formation_id` BIGINT NOT NULL
);
ALTER TABLE `emprunteur` ADD INDEX `emprunteur_formation_id_index`(`formation_id`);
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
    `categorie_id` BIGINT NOT NULL
);
ALTER TABLE `sous_categorie` ADD INDEX `sous_categorie_categorie_id_index`(`categorie_id`);
ALTER TABLE `sous_categorie` ADD INDEX `sous_categorie_categorie_id_index`(`categorie_id`);
ALTER TABLE `Pret` ADD CONSTRAINT `pret_preteur_id_foreign` FOREIGN KEY(`preteur_id`) REFERENCES `Administrateur`(`id`);
ALTER TABLE `emprunteur` ADD CONSTRAINT `emprunteur_formation_id_foreign` FOREIGN KEY(`formation_id`) REFERENCES `Formation`(`id`);
ALTER TABLE `Item` ADD CONSTRAINT `item_categorie_id_foreign` FOREIGN KEY(`categorie_id`) REFERENCES `categorie`(`id`);
ALTER TABLE `Pret` ADD CONSTRAINT `pret_emprenteur_id_foreign` FOREIGN KEY(`emprenteur_id`) REFERENCES `emprunteur`(`id`);
ALTER TABLE `Pret` ADD CONSTRAINT `pret_item_id_foreign` FOREIGN KEY(`item_id`) REFERENCES `Item`(`id`);
ALTER TABLE `sous_categorie` ADD CONSTRAINT `sous_categorie_categorie_id_foreign` FOREIGN KEY(`categorie_id`) REFERENCES `categorie`(`id`);
    