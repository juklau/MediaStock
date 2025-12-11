#!/bin/bash

# Test script pour vérifier que les chemins absolus sont utilisés correctement
# Ce script teste que backup.sh utilise bien des chemins absolus

echo "=== Test des chemins dans backup.sh ==="

# Vérifier que BACKUP_DIR utilise un chemin absolu ou une variable d'environnement avec valeur par défaut
if grep -q 'BACKUP_DIR=".*:-/.*"' scripts/backup.sh; then
    echo "✓ BACKUP_DIR utilise un chemin absolu avec variable d'environnement configurable"
else
    echo "✗ ERREUR: BACKUP_DIR n'utilise pas un chemin absolu configurable"
    exit 1
fi

# Vérifier qu'il n'y a pas de chemins relatifs non-quotés dans les volumes
if grep 'home/mediastock/backup' scripts/backup.sh | grep -v 'BACKUP_DIR' | grep -v '#' | grep -v '"' > /dev/null 2>&1; then
    echo "✗ ERREUR: Chemin relatif non-quoté trouvé dans backup.sh"
    exit 1
else
    echo "✓ Aucun chemin relatif non-quoté trouvé"
fi

# Vérifier que les commandes docker run utilisent bien la variable avec des guillemets
if grep -q '"${BACKUP_PATH}/volumes"' scripts/backup.sh; then
    echo "✓ Les chemins dans docker run sont correctement quotés avec des variables"
else
    echo "✗ AVERTISSEMENT: Vérifier que les chemins sont bien quotés"
fi

echo ""
echo "=== Test des chemins dans restore.sh ==="

# Vérifier que restore.sh accepte un argument de chemin absolu
if grep -q 'BACKUP_PATH="\$1"' scripts/restore.sh; then
    echo "✓ restore.sh accepte un chemin en argument"
else
    echo "✗ ERREUR: restore.sh ne récupère pas correctement l'argument"
    exit 1
fi

# Vérifier que BACKUP_DIR est défini dans restore.sh
if grep -q 'BACKUP_DIR=".*:-/.*"' scripts/restore.sh; then
    echo "✓ restore.sh utilise la même configuration que backup.sh"
else
    echo "✗ ERREUR: restore.sh devrait utiliser la même variable BACKUP_DIR"
    exit 1
fi

# Vérifier l'exemple dans le message d'aide utilise la variable
if grep -q '\${BACKUP_DIR}/mediastock_backup_' scripts/restore.sh; then
    echo "✓ L'exemple dans restore.sh utilise la variable BACKUP_DIR"
else
    echo "✗ ERREUR: L'exemple ne devrait pas utiliser de chemin hardcodé"
    exit 1
fi

echo ""
echo "=== Tous les tests sont passés avec succès! ==="
echo ""
echo "La correction résout le problème suivant:"
echo "  Erreur d'origine: 'home/mediastock/backup/...' (chemin relatif)"
echo "  Solution: '/home/mediastock/backup/...' (chemin absolu)"
echo ""
echo "Les scripts utilisent maintenant des chemins absolus pour éviter l'erreur:"
echo "  'includes invalid characters for a local volume name'"
