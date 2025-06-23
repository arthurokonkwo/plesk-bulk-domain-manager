<?php
/**
 * Bulk Domain Manager - Entry Point
 * Following the official Plesk extension pattern from the documentation
 */

pm_Context::init('bulk-domain-manager');

$application = new pm_Application();
$application->run();