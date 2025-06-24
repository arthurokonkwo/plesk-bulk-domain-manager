#!/bin/bash
# Installation/Upgrade script for Plesk Bulk Domain Manager Extension

# Define colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to display messages
function log {
    echo -e "${YELLOW}$1${NC}"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run as root${NC}"
    exit 1
fi

# Set variables
PLESK_PATH="/usr/local/psa"
EXTENSION_NAME="bulk-domain-manager"
EXTENSION_PATH="$PLESK_PATH/admin/plib/modules/$EXTENSION_NAME"
REPO_URL="https://github.com/arthurokonkwo/plesk-bulk-domain-manager.git"

# Check if Plesk is installed
if [ ! -d "$PLESK_PATH" ]; then
    echo -e "${RED}Plesk not found at $PLESK_PATH${NC}"
    exit 1
fi

# Check if git is installed
if ! command -v git &> /dev/null; then
    log "Git not found, installing..."
    if command -v apt-get &> /dev/null; then
        apt-get update && apt-get install -y git
    elif command -v yum &> /dev/null; then
        yum install -y git
    else
        echo -e "${RED}Cannot install git. Please install it manually.${NC}"
        exit 1
    fi
fi

# Backup current installation if exists
if [ -d "$EXTENSION_PATH" ]; then
    log "Backing up current installation..."
    BACKUP_DIR="$EXTENSION_PATH.bak.$(date +%Y%m%d%H%M%S)"
    mv "$EXTENSION_PATH" "$BACKUP_DIR"
    log "Backup created at $BACKUP_DIR"
fi

# Clone or pull repository
log "Installing latest version from GitHub..."
mkdir -p $(dirname "$EXTENSION_PATH")
git clone "$REPO_URL" "$EXTENSION_PATH"

# Set permissions
log "Setting permissions..."
chown -R psaadm:psaadm "$EXTENSION_PATH"
chmod -R 755 "$EXTENSION_PATH"
chmod 644 $EXTENSION_PATH/htdocs/js/*.js
chmod 644 $EXTENSION_PATH/htdocs/css/*.css
chmod 644 $EXTENSION_PATH/htdocs/images/*.png
chmod 644 $EXTENSION_PATH/meta.xml

# Clear Plesk cache
log "Clearing Plesk cache..."
plesk repair cache

# Restart Plesk services
log "Restarting Plesk services..."
plesk sbin httpsd restart

log "Installation/upgrade completed successfully!"
log "You can now access the extension at Extensions > My Extensions > Bulk Domain Manager"
