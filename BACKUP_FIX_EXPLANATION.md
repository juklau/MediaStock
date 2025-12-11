# Explication de la correction de l'erreur Docker

## Problème rencontré

L'erreur suivante se produit lors de la tentative de création d'une sauvegarde :

```
docker: Error response from daemon: create home/mediastock/backup/mediastock_backup_20251211_172021/volumes: "home/mediastock/backup/mediastock_backup_20251211_172021/volumes" includes invalid characters for a local volume name, only "[a-zA-Z0-9][a-zA-Z0-9_.-]" are allowed. If you intended to pass a host directory, use absolute path
```

## Cause du problème

Docker interprète les chemins de deux manières différentes :
1. **Chemins absolus** (commençant par `/`) → montés comme des répertoires hôtes
2. **Chemins relatifs** (ne commençant pas par `/`) → interprétés comme des noms de volumes Docker

Le chemin `home/mediastock/backup/...` est un chemin relatif, et Docker essaie de l'utiliser comme nom de volume. Mais les noms de volumes ne peuvent contenir que `[a-zA-Z0-9][a-zA-Z0-9_.-]`, donc le caractère `/` est invalide.

## Solution appliquée

### Avant (incorrect) :
```bash
# Chemin relatif - ERREUR!
docker run -v home/mediastock/backup:/backup alpine
```

### Après (correct) :
```bash
# Chemin absolu - OK!
docker run -v /home/mediastock/backup:/backup alpine
```

## Implémentation dans les scripts

### backup.sh
```bash
# Configuration avec chemin absolu par défaut
BACKUP_DIR="${BACKUP_DIR:-/home/mediastock/backup}"
BACKUP_PATH="${BACKUP_DIR}/mediastock_backup_${TIMESTAMP}"

# Utilisation dans les commandes Docker
docker run --rm \
    -v mediastock_mysql-data:/source:ro \
    -v "${BACKUP_PATH}/volumes":/backup \
    alpine \
    tar czf /backup/mysql-data.tar.gz -C /source .
```

### restore.sh
```bash
# Utilise la même configuration
BACKUP_DIR="${BACKUP_DIR:-/home/mediastock/backup}"

# Accepte un chemin absolu en argument
BACKUP_PATH="$1"

# Exemple d'utilisation
./scripts/restore.sh /home/mediastock/backup/mediastock_backup_20251211_172021
```

## Utilisation

### Sauvegarde standard
```bash
./scripts/backup.sh
```

### Sauvegarde avec répertoire personnalisé
```bash
export BACKUP_DIR=/mon/chemin/absolu/backup
./scripts/backup.sh
```

### Restauration
```bash
./scripts/restore.sh /home/mediastock/backup/mediastock_backup_20251211_172021
```

## Points clés

1. ✅ Toujours utiliser des chemins **absolus** (commençant par `/`) pour les montages de volumes Docker
2. ✅ Utiliser des variables d'environnement pour permettre la personnalisation
3. ✅ Inclure des messages d'erreur clairs pour aider au débogage
4. ✅ Tester la syntaxe des scripts avec `bash -n script.sh`

## Références

- [Documentation Docker sur les volumes](https://docs.docker.com/storage/volumes/)
- [Docker volume vs bind mount](https://docs.docker.com/storage/bind-mounts/)
