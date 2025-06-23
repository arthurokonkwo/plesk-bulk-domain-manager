<?php
/**
 * Bulk Domain Manager - Main Controller
 * Handles the main interface and bulk operations
 */

class IndexController extends pm_Controller_Action
{
    protected $_accessLevel = 'admin';
    
    public function init()
    {
        parent::init();
        
        // Set the page title
        $this->view->pageTitle = 'Bulk Domain Manager';
        
        // Add CSS and JavaScript
        $this->view->headLink()->appendStylesheet(pm_Context::getBaseUrl() . 'css/bulk-manager.css');
        $this->view->headScript()->appendFile(pm_Context::getBaseUrl() . 'js/bulk-manager.js');
    }
    
    public function indexAction()
    {
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
     * Get available service plans - Fixed for compatibility
     */
    private function _getServicePlans()
    {
        $plans = array();
        try {
            // Try different approaches for different Plesk versions
            if (class_exists('pm_ServicePlan')) {
                $servicePlans = pm_ServicePlan::getAll();
                foreach ($servicePlans as $plan) {
                    $plans[$plan->getId()] = $plan->getName();
                }
            } else {
                // Fallback: Use API to get service plans
                try {
                    $request = '<packet><service-plan><get><filter/><dataset><gen_info/></dataset></get></service-plan></packet>';
                    $response = pm_ApiRpc::getService()->call($request);
                    
                    if (isset($response->{'service-plan'}->get->result)) {
                        $results = $response->{'service-plan'}->get->result;
                        if (!is_array($results)) {
                            $results = array($results);
                        }
                        
                        foreach ($results as $result) {
                            if (isset($result->data->gen_info)) {
                                $plans[$result->id] = $result->data->gen_info->name;
                            }
                        }
                    }
                } catch (Exception $e) {
                    // If API fails, provide default message
                    $plans[0] = 'Service plans unavailable - using API method';
                }
            }
        } catch (Exception $e) {
            // Handle error gracefully - provide basic functionality
            $plans[0] = 'Default Plan';
        }
        return $plans;
    }
    
    /**
     * Get available clients - Fixed for compatibility
     */
    private function _getClients()
    {
        $clients = array();
        try {
            if (class_exists('pm_Client')) {
                $clientList = pm_Client::getAll();
                foreach ($clientList as $client) {
                    $clients[$client->getId()] = $client->getProperty('pname') . ' (' . $client->getProperty('login') . ')';
                }
            } else {
                // Fallback: Use API to get clients
                try {
                    $request = '<packet><customer><get><filter/><dataset><gen_info/></dataset></get></customer></packet>';
                    $response = pm_ApiRpc::getService()->call($request);
                    
                    if (isset($response->customer->get->result)) {
                        $results = $response->customer->get->result;
                        if (!is_array($results)) {
                            $results = array($results);
                        }
                        
                        foreach ($results as $result) {
                            if (isset($result->data->gen_info)) {
                                $clients[$result->id] = $result->data->gen_info->pname . ' (' . $result->data->gen_info->login . ')';
                            }
                        }
                    }
                } catch (Exception $e) {
                    $clients[0] = 'Clients unavailable - using API method';
                }
            }
        } catch (Exception $e) {
            // Handle error gracefully
            $clients[0] = 'Admin User';
        }
        return $clients;
    }
}