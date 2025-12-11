#!/bin/bash

# Script de sauvegarde pour MediaStock
# Ce script crée une sauvegarde complète de la base de données MySQL et des volumes Docker

# Configuration
BACKUP_DIR="/home/mediastock/backup"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_PATH="${BACKUP_DIR}/mediastock_backup_${TIMESTAMP}"
CONTAINER_NAME="mediastock-db"

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== MediaStock Backup Script ===${NC}"
echo "Timestamp: ${TIMESTAMP}"

# Créer le répertoire de sauvegarde s'il n'existe pas
echo -e "${YELLOW}Création du répertoire de sauvegarde...${NC}"
mkdir -p "${BACKUP_PATH}"

if [ ! -d "${BACKUP_PATH}" ]; then
    echo -e "${RED}Erreur: Impossible de créer le répertoire ${BACKUP_PATH}${NC}"
    echo -e "${YELLOW}Astuce: Vérifiez que le chemin absolu ${BACKUP_DIR} existe et que vous avez les permissions nécessaires.${NC}"
    exit 1
fi

# Vérifier que le conteneur MySQL est en cours d'exécution
if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
    echo -e "${RED}Erreur: Le conteneur ${CONTAINER_NAME} n'est pas en cours d'exécution${NC}"
    echo -e "${YELLOW}Lancez d'abord: docker-compose up -d${NC}"
    exit 1
fi

# Charger les variables d'environnement
if [ -f .env ]; then
    source .env
else
    echo -e "${RED}Erreur: Fichier .env non trouvé${NC}"
    exit 1
fi

# Sauvegarde de la base de données
echo -e "${YELLOW}Sauvegarde de la base de données...${NC}"
docker exec ${CONTAINER_NAME} mysqldump \
    -u ${DB_USER} \
    -p${DB_PASSWORD} \
    ${DB_NAME} > "${BACKUP_PATH}/database.sql"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Base de données sauvegardée: ${BACKUP_PATH}/database.sql${NC}"
else
    echo -e "${RED}✗ Erreur lors de la sauvegarde de la base de données${NC}"
    exit 1
fi

# Sauvegarde des volumes Docker
echo -e "${YELLOW}Sauvegarde des volumes Docker...${NC}"
mkdir -p "${BACKUP_PATH}/volumes"

# Sauvegarder le volume mysql-data
docker run --rm \
    -v mediastock_mysql-data:/source:ro \
    -v "${BACKUP_PATH}/volumes":/backup \
    alpine \
    tar czf /backup/mysql-data.tar.gz -C /source .

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Volume mysql-data sauvegardé${NC}"
else
    echo -e "${RED}✗ Erreur lors de la sauvegarde du volume mysql-data${NC}"
    exit 1
fi

# Sauvegarde des fichiers de configuration et du code source
echo -e "${YELLOW}Sauvegarde des fichiers de configuration...${NC}"
cp -r config "${BACKUP_PATH}/"
cp -r sql "${BACKUP_PATH}/"
cp docker-compose.yml "${BACKUP_PATH}/"
cp .env "${BACKUP_PATH}/"

echo -e "${GREEN}✓ Fichiers de configuration sauvegardés${NC}"

# Résumé
echo ""
echo -e "${GREEN}=== Sauvegarde terminée avec succès ===${NC}"
echo "Emplacement: ${BACKUP_PATH}"
echo "Contenu:"
echo "  - database.sql (dump MySQL)"
echo "  - volumes/mysql-data.tar.gz (volume Docker)"
echo "  - config/ (fichiers de configuration)"
echo "  - sql/ (scripts SQL)"
echo "  - docker-compose.yml"
echo "  - .env"
echo ""
echo -e "${YELLOW}Note: Pour restaurer, utilisez le script restore.sh${NC}"
