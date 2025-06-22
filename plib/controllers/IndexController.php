<?php
/**
 * Bulk Domain Manager - Main Controller
 * Handles the main interface and bulk operations
 */

class IndexController extends pm_Controller_Action
{
    public function init()
    {
        parent::init();
        
        // Check if user has required permissions
        if (!pm_Session::getClient()->hasPermission('domain-administration')) {
            throw new pm_Exception('Insufficient permissions to access Bulk Domain Manager');
        }
        
        $this->view->headTitle('Bulk Domain Manager');
        
        // Add CSS and JavaScript
        $this->view->headLink()->appendStylesheet(pm_Context::getBaseUrl() . 'css/bulk-manager.css');
        $this->view->headScript()->appendFile(pm_Context::getBaseUrl() . 'js/bulk-manager.js');
    }
    
    public function indexAction()
    {
        $this->view->pageTitle = 'Bulk Domain Manager';
        
        // Initialize form and result variables
        $this->view->urls = '';
        $this->view->selectedAction = '';
        $this->view->actionType = '';
        $this->view->results = array();
        $this->view->hasResults = false;
        
        // Get available service plans for subscription operations
        $this->view->servicePlans = $this->_getServicePlans();
        
        // Get available clients for subscriber changes
        $this->view->clients = $this->_getClients();
        
        if ($this->getRequest()->isPost()) {
            $this->_processBulkOperation();
        }
    }
    
    /**
     * Process bulk operations submitted via POST
     */
    private function _processBulkOperation()
    {
        try {
            // Get form data
            $urls = trim($this->getRequest()->getParam('urls', ''));
            $actionType = $this->getRequest()->getParam('action_type', '');
            $selectedAction = $this->getRequest()->getParam('selected_action', '');
            $newPlanId = $this->getRequest()->getParam('new_plan_id', '');
            $newSubscriberId = $this->getRequest()->getParam('new_subscriber_id', '');
            
            // Validate input
            if (empty($urls)) {
                throw new pm_Exception('Please enter at least one URL/domain');
            }
            
            if (empty($actionType) || empty($selectedAction)) {
                throw new pm_Exception('Please select an action type and specific action');
            }
            
            // Parse URLs
            $urlList = $this->_parseUrls($urls);
            
            if (empty($urlList)) {
                throw new pm_Exception('No valid URLs/domains found');
            }
            
            // Initialize bulk manager
            $bulkManager = new Modules_BulkDomainManager_BulkManager();
            
            // Execute bulk operation
            $results = $bulkManager->executeBulkOperation(
                $urlList,
                $actionType,
                $selectedAction,
                array(
                    'newPlanId' => $newPlanId,
                    'newSubscriberId' => $newSubscriberId
                )
            );
            
            // Pass results to view
            $this->view->results = $results;
            $this->view->hasResults = true;
            $this->view->urls = $urls;
            $this->view->actionType = $actionType;
            $this->view->selectedAction = $selectedAction;
            
            // Success message
            $successCount = count(array_filter($results, function($r) { return $r['status'] === 'success'; }));
            $this->_status->addMessage('info', "Bulk operation completed. {$successCount} operations succeeded.");
            
        } catch (Exception $e) {
            $this->_status->addMessage('error', 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Parse URLs from textarea input
     */
    private function _parseUrls($urlsText)
    {
        $lines = explode("\n", $urlsText);
        $urls = array();
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                // Clean up URL - remove protocols, www, trailing slashes
                $cleanUrl = preg_replace('/^https?:\/\//', '', $line);
                $cleanUrl = preg_replace('/^www\./', '', $cleanUrl);
                $cleanUrl = rtrim($cleanUrl, '/');
                
                if (!empty($cleanUrl)) {
                    $urls[] = $cleanUrl;
                }
            }
        }
        
        return array_unique($urls);
    }
    
    /**
     * Get available service plans
     */
    private function _getServicePlans()
    {
        $plans = array();
        try {
            $servicePlans = pm_ServicePlan::getAll();
            foreach ($servicePlans as $plan) {
                $plans[$plan->getId()] = $plan->getName();
            }
        } catch (Exception $e) {
            // Handle error gracefully
        }
        return $plans;
    }
    
    /**
     * Get available clients
     */
    private function _getClients()
    {
        $clients = array();
        try {
            $clientList = pm_Client::getAll();
            foreach ($clientList as $client) {
                $clients[$client->getId()] = $client->getProperty('pname') . ' (' . $client->getProperty('login') . ')';
            }
        } catch (Exception $e) {
            // Handle error gracefully
        }
        return $clients;
    }
}