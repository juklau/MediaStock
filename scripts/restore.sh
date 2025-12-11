#!/bin/bash

# Script de restauration pour MediaStock
# Ce script restaure une sauvegarde complète de la base de données MySQL et des volumes Docker

# Configuration
# Le répertoire de sauvegarde peut être personnalisé via la variable d'environnement BACKUP_DIR
BACKUP_DIR="${BACKUP_DIR:-/home/mediastock/backup}"

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== MediaStock Restore Script ===${NC}"

# Vérifier qu'un chemin de sauvegarde est fourni
if [ -z "$1" ]; then
    echo -e "${RED}Usage: $0 /chemin/absolu/vers/backup${NC}"
    echo ""
    echo "Exemple:"
    echo "  $0 ${BACKUP_DIR}/mediastock_backup_20251211_172021"
    echo ""
    echo "Sauvegardes disponibles dans ${BACKUP_DIR}/:"
    ls -1d ${BACKUP_DIR}/mediastock_backup_* 2>/dev/null || echo "  Aucune sauvegarde trouvée"
    exit 1
fi

BACKUP_PATH="$1"

# Vérifier que le chemin de sauvegarde existe
if [ ! -d "${BACKUP_PATH}" ]; then
    echo -e "${RED}Erreur: Le répertoire ${BACKUP_PATH} n'existe pas${NC}"
    exit 1
fi

# Vérifier que les fichiers de sauvegarde existent
if [ ! -f "${BACKUP_PATH}/database.sql" ]; then
    echo -e "${RED}Erreur: ${BACKUP_PATH}/database.sql n'existe pas${NC}"
    exit 1
fi

echo "Répertoire de sauvegarde: ${BACKUP_PATH}"
echo ""
echo -e "${YELLOW}ATTENTION: Cette opération va écraser les données actuelles!${NC}"
read -p "Êtes-vous sûr de vouloir continuer? (oui/non): " confirmation

if [ "$confirmation" != "oui" ]; then
    echo "Restauration annulée"
    exit 0
fi

CONTAINER_NAME="mediastock-db"

# Vérifier que le conteneur MySQL est en cours d'exécution
if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
    echo -e "${RED}Erreur: Le conteneur ${CONTAINER_NAME} n'est pas en cours d'exécution${NC}"
    echo -e "${YELLOW}Lancez d'abord: docker-compose up -d${NC}"
    exit 1
fi

# Charger les variables d'environnement depuis la sauvegarde ou le répertoire courant
if [ -f "${BACKUP_PATH}/.env" ]; then
    echo -e "${YELLOW}Utilisation du fichier .env depuis la sauvegarde${NC}"
    source "${BACKUP_PATH}/.env"
elif [ -f .env ]; then
    echo -e "${YELLOW}Utilisation du fichier .env actuel${NC}"
    source .env
else
    echo -e "${RED}Erreur: Fichier .env non trouvé${NC}"
    exit 1
fi

# Restauration de la base de données
echo -e "${YELLOW}Restauration de la base de données...${NC}"
docker exec -i ${CONTAINER_NAME} mysql \
    -u ${DB_USER} \
    -p${DB_PASSWORD} \
    ${DB_NAME} < "${BACKUP_PATH}/database.sql"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Base de données restaurée${NC}"
else
    echo -e "${RED}✗ Erreur lors de la restauration de la base de données${NC}"
    exit 1
fi

# Restauration du volume mysql-data (optionnel)
if [ -f "${BACKUP_PATH}/volumes/mysql-data.tar.gz" ]; then
    echo -e "${YELLOW}Restauration du volume mysql-data disponible${NC}"
    read -p "Voulez-vous également restaurer le volume Docker? (oui/non): " restore_volume
    
    if [ "$restore_volume" = "oui" ]; then
        echo -e "${YELLOW}Arrêt du conteneur MySQL...${NC}"
        docker-compose stop mysql
        
        echo -e "${YELLOW}Restauration du volume...${NC}"
        docker run --rm \
            -v mediastock_mysql-data:/target \
            -v "${BACKUP_PATH}/volumes":/backup:ro \
            alpine \
            sh -c "rm -rf /target/* && tar xzf /backup/mysql-data.tar.gz -C /target"
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Volume mysql-data restauré${NC}"
        else
            echo -e "${RED}✗ Erreur lors de la restauration du volume${NC}"
        fi
        
        echo -e "${YELLOW}Redémarrage du conteneur MySQL...${NC}"
        docker-compose start mysql
        sleep 5
    fi
fi

# Résumé
echo ""
echo -e "${GREEN}=== Restauration terminée avec succès ===${NC}"
echo "Base de données restaurée depuis: ${BACKUP_PATH}/database.sql"
echo ""
echo -e "${YELLOW}Note: Vérifiez que l'application fonctionne correctement${NC}"
