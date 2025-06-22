<?php
/**
 * Bulk Domain Manager - Core Business Logic
 * Handles all bulk operations for domains and subscriptions
 */

class Modules_BulkDomainManager_BulkManager
{
    private $_logger;
    
    public function __construct()
    {
        // Initialize logger for audit trail
        $this->_logger = pm_Log::getLogger('bulk-domain-manager');
    }
    
    /**
     * Execute bulk operation on provided URLs
     * 
     * @param array $urls List of URLs/domains to process
     * @param string $actionType 'domain' or 'subscription'
     * @param string $selectedAction Specific action to perform
     * @param array $options Additional options (plan ID, subscriber ID, etc.)
     * @return array Results of operations
     */
    public function executeBulkOperation($urls, $actionType, $selectedAction, $options = array())
    {
        $results = array();
        
        $this->_logger->info("Starting bulk operation: {$actionType}:{$selectedAction} on " . count($urls) . " URLs");
        
        foreach ($urls as $url) {
            try {
                $result = $this->_processUrl($url, $actionType, $selectedAction, $options);
                $results[] = $result;
                
                // Log individual result
                $this->_logger->info("URL: {$url} - Action: {$actionType}:{$selectedAction} - Status: {$result['status']}");
                
            } catch (Exception $e) {
                $errorResult = array(
                    'url' => $url,
                    'action' => "{$actionType}:{$selectedAction}",
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'timestamp' => date('Y-m-d H:i:s')
                );
                $results[] = $errorResult;
                
                $this->_logger->error("URL: {$url} - Error: " . $e->getMessage());
            }
        }
        
        $this->_logger->info("Bulk operation completed. Total: " . count($results));
        
        return $results;
    }
    
    /**
     * Process individual URL with specified action
     */
    private function _processUrl($url, $actionType, $selectedAction, $options)
    {
        $timestamp = date('Y-m-d H:i:s');
        
        // Find domain/subscription
        $domain = $this->_findDomain($url);
        if (!$domain) {
            throw new pm_Exception("Domain not found: {$url}");
        }
        
        if ($actionType === 'domain') {
            return $this->_processDomainAction($domain, $selectedAction, $timestamp);
        } else {
            return $this->_processSubscriptionAction($domain, $selectedAction, $options, $timestamp);
        }
    }
    
    /**
     * Process domain-specific actions
     */
    private function _processDomainAction($domain, $action, $timestamp)
    {
        $domainName = $domain->getName();
        
        switch ($action) {
            case 'activate':
                if ($domain->getStatus() === 'active') {
                    $message = "Domain already active";
                } else {
                    $domain->setStatus('active');
                    $message = "Domain activated successfully";
                }
                break;
                
            case 'suspend':
                if ($domain->getStatus() === 'suspended') {
                    $message = "Domain already suspended";
                } else {
                    $domain->setStatus('suspended');
                    $message = "Domain suspended successfully";
                }
                break;
                
            case 'deactivate':
                if ($domain->getStatus() === 'disabled') {
                    $message = "Domain already deactivated";
                } else {
                    $domain->setStatus('disabled');
                    $message = "Domain deactivated successfully";
                }
                break;
                
            case 'remove':
                // Note: This is a destructive operation - should have additional confirmation
                $domain->delete();
                $message = "Domain removed successfully";
                break;
                
            default:
                throw new pm_Exception("Unknown domain action: {$action}");
        }
        
        return array(
            'url' => $domainName,
            'action' => "domain:{$action}",
            'status' => 'success',
            'message' => $message,
            'timestamp' => $timestamp
        );
    }
    
    /**
     * Process subscription-specific actions
     */
    private function _processSubscriptionAction($domain, $action, $options, $timestamp)
    {
        $subscription = $domain->getSubscription();
        if (!$subscription) {
            throw new pm_Exception("No subscription found for domain: " . $domain->getName());
        }
        
        $domainName = $domain->getName();
        
        switch ($action) {
            case 'activate':
                if ($subscription->getStatus() === 'active') {
                    $message = "Subscription already active";
                } else {
                    $subscription->setStatus('active');
                    $message = "Subscription activated successfully";
                }
                break;
                
            case 'suspend':
                if ($subscription->getStatus() === 'suspended') {
                    $message = "Subscription already suspended";
                } else {
                    $subscription->setStatus('suspended');
                    $message = "Subscription suspended successfully";
                }
                break;
                
            case 'remove':
                // Note: This is a destructive operation
                $subscription->delete();
                $message = "Subscription removed successfully";
                break;
                
            case 'change_plan':
                if (empty($options['newPlanId'])) {
                    throw new pm_Exception("New plan ID is required for plan change");
                }
                
                $newPlan = new pm_ServicePlan($options['newPlanId']);
                $subscription->setServicePlan($newPlan);
                $message = "Subscription plan changed to: " . $newPlan->getName();
                break;
                
            case 'change_subscriber':
                if (empty($options['newSubscriberId'])) {
                    throw new pm_Exception("New subscriber ID is required for subscriber change");
                }
                
                $newClient = new pm_Client($options['newSubscriberId']);
                $subscription->setClient($newClient);
                $message = "Subscription transferred to: " . $newClient->getProperty('pname');
                break;
                
            default:
                throw new pm_Exception("Unknown subscription action: {$action}");
        }
        
        return array(
            'url' => $domainName,
            'action' => "subscription:{$action}",
            'status' => 'success',
            'message' => $message,
            'timestamp' => $timestamp
        );
    }
    
    /**
     * Find domain by URL/domain name
     */
    private function _findDomain($url)
    {
        try {
            // Try exact match first
            return pm_Domain::getByName($url);
        } catch (pm_Exception $e) {
            // If not found, try with www prefix
            try {
                return pm_Domain::getByName('www.' . $url);
            } catch (pm_Exception $e2) {
                // Try without www if URL had it
                if (strpos($url, 'www.') === 0) {
                    try {
                        return pm_Domain::getByName(substr($url, 4));
                    } catch (pm_Exception $e3) {
                        return null;
                    }
                }
                return null;
            }
        }
    }
    
    /**
     * Validate if operation is safe to perform
     */
    public function validateOperation($urls, $actionType, $selectedAction)
    {
        $warnings = array();
        $errors = array();
        
        // Check for destructive operations
        if (in_array($selectedAction, array('remove'))) {
            $warnings[] = "This operation will permanently delete data. Make sure you have backups.";
        }
        
        // Validate URLs exist
        $notFound = array();
        foreach ($urls as $url) {
            if (!$this->_findDomain($url)) {
                $notFound[] = $url;
            }
        }
        
        if (!empty($notFound)) {
            $errors[] = "The following domains were not found: " . implode(', ', $notFound);
        }
        
        return array(
            'warnings' => $warnings,
            'errors' => $errors,
            'canProceed' => empty($errors)
        );
    }
}