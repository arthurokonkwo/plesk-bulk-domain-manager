<?php
/**
 * Bulk Domain Manager - Entry Point
 * This file serves as the main entry point for the Plesk extension
 */

// Properly initialize the Plesk module context
pm_Context::init('bulk-domain-manager');

// Set up autoloading for our classes
$loader = new pm_Loader();
$loader->registerNamespace('Modules_BulkDomainManager', pm_Context::getPlibDir());

// Initialize and dispatch the request using Plesk's standard pattern
$application = new pm_Application();
$application->bootstrap();
$application->run();