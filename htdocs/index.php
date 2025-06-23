<?php
/**
 * Bulk Domain Manager - Main Entry Point
 * This file serves as the entry point for the extension
 */

// Initialize Plesk module context
require_once 'pm/Context.php';
pm_Context::init('bulk-domain-manager');

// Include the main controller
require_once pm_Context::getPlibDir() . 'controllers/IndexController.php';

// Create and run the controller
$controller = new IndexController();
$controller->run();