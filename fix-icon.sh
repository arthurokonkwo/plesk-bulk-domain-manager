#!/bin/bash
# Fix icon issue in Plesk Bulk Domain Manager extension

# Define colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Fixing Plesk Bulk Domain Manager extension icon...${NC}"

# Define paths
PLESK_EXT_PATH="/usr/local/psa/admin/plib/modules/bulk-domain-manager"
ICON_DIR="$PLESK_EXT_PATH/htdocs/images"

# Check if the extension is installed
if [ ! -d "$PLESK_EXT_PATH" ]; then
    echo -e "${RED}Error: Extension not installed at $PLESK_EXT_PATH${NC}"
    echo -e "${YELLOW}Please make sure the extension is installed before running this script.${NC}"
    exit 1
fi

# Create images directory if it doesn't exist
if [ ! -d "$ICON_DIR" ]; then
    echo -e "${YELLOW}Creating images directory...${NC}"
    mkdir -p "$ICON_DIR"
fi

# Check if ImageMagick is installed
if ! command -v convert &> /dev/null; then
    echo -e "${YELLOW}ImageMagick not found. Installing...${NC}"
    if command -v apt-get &> /dev/null; then
        apt-get update && apt-get install -y imagemagick
    elif command -v yum &> /dev/null; then
        yum install -y ImageMagick
    else
        echo -e "${RED}Error: Cannot install ImageMagick. Please install it manually.${NC}"
        exit 1
    fi
fi

# Create icon files in appropriate sizes
echo -e "${YELLOW}Creating icon files in required sizes...${NC}"

# Base64 encoded data of a simple bulk management icon (blue/red blocks)
cat > "$ICON_DIR/icon_base64.txt" << 'EOF'
iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAACXBIWXMAAAsTAAALEwEAmpwYAAAFHGlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDIgNzkuMTYwOTI0LCAyMDE3LzA3LzEzLTAxOjA2OjM5ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxOCAoV2luZG93cykiIHhtcDpDcmVhdGVEYXRlPSIyMDI1LTA2LTI0VDEwOjMwOjAyKzAxOjAwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAyNS0wNi0yNFQxMDozMDoxNyswMTowMCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAyNS0wNi0yNFQxMDozMDoxNyswMTowMCIgZGM6Zm9ybWF0PSJpbWFnZS9wbmciIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDowMzlmOTg2Yi0zOTZh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
EOF

# Convert base64 to image
base64 -d "$ICON_DIR/icon_base64.txt" > "$ICON_DIR/icon.png"

# Create icon files in all required sizes
convert "$ICON_DIR/icon.png" -resize 16x16 "$ICON_DIR/icon-16.png"
convert "$ICON_DIR/icon.png" -resize 32x32 "$ICON_DIR/icon-32.png"
convert "$ICON_DIR/icon.png" -resize 64x64 "$ICON_DIR/icon-64.png"

# Remove temporary file
rm "$ICON_DIR/icon_base64.txt"

# Set proper permissions
echo -e "${YELLOW}Setting correct file permissions...${NC}"
chmod 644 "$ICON_DIR"/*.png
chown -R psaadm:psaadm "$ICON_DIR"

# Update meta.xml if needed
echo -e "${YELLOW}Checking meta.xml file...${NC}"
META_XML="$PLESK_EXT_PATH/meta.xml"

if [ -f "$META_XML" ]; then
    # Check if icon tags are present
    if ! grep -q "<icon>" "$META_XML"; then
        echo -e "${YELLOW}Adding icon tags to meta.xml...${NC}"
        
        # Create a backup
        cp "$META_XML" "$META_XML.bak"
        
        # Add icon tags before </module>
        sed -i 's|</module>|    <icon>htdocs/images/icon.png</icon>\n    <icon32>htdocs/images/icon-32.png</icon32>\n    <icon64>htdocs/images/icon-64.png</icon64>\n</module>|' "$META_XML"
    else
        echo -e "${GREEN}Icon tags already present in meta.xml${NC}"
    fi
else
    echo -e "${RED}Error: meta.xml not found at $META_XML${NC}"
    exit 1
fi

# Clear Plesk cache
echo -e "${YELLOW}Clearing Plesk cache...${NC}"
plesk repair cache

echo -e "${GREEN}Icon fix completed successfully!${NC}"
echo -e "${YELLOW}Please restart your browser to see the changes.${NC}"
