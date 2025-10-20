CREATE TABLE `Item`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` TEXT NOT NULL,
    `model` TEXT NOT NULL,
    `qr_code` TEXT NOT NULL,
    `état` ENUM('') NOT NULL,
    `categorie_id` BIGINT NOT NULL,
    `admin_id` BIGINT NOT NULL,
    `statut` ENUM('') NOT NULL
);
ALTER TABLE
    `Item` ADD INDEX `item_admin_id_index`(`admin_id`);
ALTER TABLE
    `Item` ADD INDEX `item_categorie_id_index`(`categorie_id`);
CREATE TABLE `Pret`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `item_id` BIGINT NOT NULL,
    `emprenteur_id` BIGINT NOT NULL,
    `date_sortie` DATE NOT NULL,
    `date_retour_prévue` DATE NULL,
    `date_retour_effective` DATE NOT NULL,
    `note_debut` TEXT NOT NULL,
    `note_fin` TEXT NOT NULL
);
ALTER TABLE
    `Pret` ADD INDEX `pret_item_id_emprenteur_id_index`(`item_id`, `emprenteur_id`);
ALTER TABLE
    `Pret` ADD INDEX `pret_item_id_index`(`item_id`);
ALTER TABLE
    `Pret` ADD INDEX `pret_emprenteur_id_index`(`emprenteur_id`);
CREATE TABLE `Administrateur`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `login` TEXT NOT NULL,
    `mot_de_passe_hash` TEXT NOT NULL
);
CREATE TABLE `emprunteur`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `emprunteur_nom` TEXT NOT NULL,
    `emprunteur_prenom` TEXT NOT NULL,
    `role_id` BIGINT NOT NULL,
    `formation_id` BIGINT NOT NULL
);
CREATE TABLE `categorie`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `categorie` TEXT NOT NULL
);
CREATE TABLE `Formation`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `formation` TEXT NULL
);
CREATE TABLE `Role`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `role` ENUM('') NOT NULL
);
CREATE TABLE `sous_categorie`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `sous_categorie` TEXT NOT NULL,
    `categorie_id` BIGINT NOT NULL
);
ALTER TABLE
    `sous_categorie` ADD INDEX `sous_categorie_categorie_id_index`(`categorie_id`);
ALTER TABLE
    `Item` ADD CONSTRAINT `item_admin_id_foreign` FOREIGN KEY(`admin_id`) REFERENCES `Administrateur`(`id`);
ALTER TABLE
    `emprunteur` ADD CONSTRAINT `emprunteur_formation_id_foreign` FOREIGN KEY(`formation_id`) REFERENCES `Formation`(`id`);
ALTER TABLE
    `Item` ADD CONSTRAINT `item_categorie_id_foreign` FOREIGN KEY(`categorie_id`) REFERENCES `categorie`(`id`);
ALTER TABLE
    `Pret` ADD CONSTRAINT `pret_emprenteur_id_foreign` FOREIGN KEY(`emprenteur_id`) REFERENCES `emprunteur`(`id`);
ALTER TABLE
    `Pret` ADD CONSTRAINT `pret_item_id_foreign` FOREIGN KEY(`item_id`) REFERENCES `Item`(`id`);
ALTER TABLE
    `sous_categorie` ADD CONSTRAINT `sous_categorie_categorie_id_foreign` FOREIGN KEY(`categorie_id`) REFERENCES `categorie`(`id`);
ALTER TABLE
    `emprunteur` ADD CONSTRAINT `emprunteur_role_id_foreign` FOREIGN KEY(`role_id`) REFERENCES `Role`(`id`);
    