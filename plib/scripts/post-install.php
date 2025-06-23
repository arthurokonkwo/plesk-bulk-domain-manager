<?php
/**
 * Bulk Domain Manager - Post Installation Script
 * Handles any setup tasks needed after extension installation
 */

try {
    // Log installation
    pm_Log::info('Bulk Domain Manager extension installed successfully');
    
    // Create any necessary database tables or configurations
    // (None needed for this extension as it uses existing Plesk APIs)
    
    // Set default permissions if needed
    // This extension relies on existing domain-administration permissions
    
    echo "Bulk Domain Manager extension installed successfully.\n";
    echo "The extension is now available in the Plesk interface.\n";
    echo "Make sure users have 'domain-administration' permissions to access it.\n";
    
} catch (Exception $e) {
    pm_Log::err('Failed to install Bulk Domain Manager: ' . $e->getMessage());
    throw $e;
}