# MediaStock Tests

Ce dossier contient des scripts de test pour vérifier le bon fonctionnement de la couche d'accès aux données MediaStock.

## Comment exécuter les tests

1. Assurez-vous que la base de données est correctement configurée et accessible
2. Naviguez vers ce dossier dans votre navigateur web
3. Exécutez les tests individuels en accédant à chaque fichier de test, par exemple:
   - `test_administrateur.php` - Tests des opérations d'administrateur
   - `test_item.php` - Tests des opérations d'articles
   - `test_pret.php` - Tests des opérations de prêt
   - etc.
4. Ou exécutez tous les tests en accédant à `test_all.php`

## Structure des tests

Chaque fichier de test est organisé de la manière suivante:
1. Inclusion de l'autoloader
2. Création d'une instance du modèle à tester
3. Exécution de différentes opérations CRUD et méthodes spécifiques
4. Affichage des résultats avec des explications détaillées

## Notes importantes

- Ces tests sont conçus pour être exécutés dans un environnement de développement uniquement
- Certains tests peuvent modifier les données dans la base de données
- Les tests sont abondamment commentés pour expliquer chaque opération