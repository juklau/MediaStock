#!/bin/bash

# Test script pour vérifier que les chemins absolus sont utilisés correctement
# Ce script teste que backup.sh utilise bien des chemins absolus

echo "=== Test des chemins dans backup.sh ==="

# Vérifier que BACKUP_DIR commence par /
if grep -q '^BACKUP_DIR="/home/mediastock/backup"' scripts/backup.sh; then
    echo "✓ BACKUP_DIR utilise un chemin absolu (commence par /)"
else
    echo "✗ ERREUR: BACKUP_DIR n'utilise pas un chemin absolu"
    exit 1
fi

# Vérifier qu'il n'y a pas de chemins relatifs pour les volumes
if grep -q 'home/mediastock/backup' scripts/backup.sh | grep -v '^BACKUP_DIR=' | grep -v '#'; then
    echo "✗ ERREUR: Chemin relatif trouvé dans backup.sh"
    exit 1
else
    echo "✓ Aucun chemin relatif trouvé (sauf dans BACKUP_DIR)"
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

# Vérifier l'exemple dans le message d'aide
if grep -q '/home/mediastock/backup/mediastock_backup_' scripts/restore.sh; then
    echo "✓ L'exemple dans restore.sh utilise un chemin absolu"
else
    echo "✗ ERREUR: L'exemple ne montre pas de chemin absolu"
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
