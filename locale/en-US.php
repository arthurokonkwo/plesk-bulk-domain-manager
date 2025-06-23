<?php
/**
 * Bulk Domain Manager - English (US) Language File
 */

$messages = array(
    // General
    'bulk_domain_manager' => 'Bulk Domain Manager',
    'description' => 'Manage multiple domains and subscriptions in bulk operations',
    
    // Form Labels
    'urls_domains' => 'URLs/Domains',
    'urls_help_text' => 'Enter one URL or domain per line. You can include or exclude protocols (http/https) and www.',
    'action_type' => 'Action Type',
    'domain_actions' => 'Domain Actions',
    'subscription_actions' => 'Subscription Actions',
    'select_domain_action' => 'Select Domain Action...',
    'select_subscription_action' => 'Select Subscription Action...',
    
    // Actions
    'activate' => 'Activate',
    'suspend' => 'Suspend',
    'deactivate' => 'Deactivate',
    'remove' => 'Remove',
    'change_plan' => 'Change Plan',
    'change_subscriber' => 'Change Subscriber',
    
    // Additional Options
    'new_service_plan' => 'New Service Plan',
    'select_plan' => 'Select Plan...',
    'new_subscriber' => 'New Subscriber',
    'select_subscriber' => 'Select Subscriber...',
    
    // Buttons
    'execute_bulk_operation' => 'Execute Bulk Operation',
    'export_results_csv' => 'Export Results to CSV',
    'processing_wait' => 'Processing... Please wait',
    
    // Results
    'operation_results' => 'Operation Results',
    'total_operations' => 'Total Operations',
    'successful' => 'Successful',
    'failed' => 'Failed',
    'domain_url' => 'Domain/URL',
    'action' => 'Action',
    'status' => 'Status',
    'message' => 'Message',
    'timestamp' => 'Timestamp',
    
    // Status Messages
    'success' => 'Success',
    'error' => 'Error',
    'domain_already_active' => 'Domain already active',
    'domain_activated_successfully' => 'Domain activated successfully',
    'domain_already_suspended' => 'Domain already suspended',
    'domain_suspended_successfully' => 'Domain suspended successfully',
    'domain_already_deactivated' => 'Domain already deactivated',
    'domain_deactivated_successfully' => 'Domain deactivated successfully',
    'domain_removed_successfully' => 'Domain removed successfully',
    
    'subscription_already_active' => 'Subscription already active',
    'subscription_activated_successfully' => 'Subscription activated successfully',
    'subscription_already_suspended' => 'Subscription already suspended',
    'subscription_suspended_successfully' => 'Subscription suspended successfully',
    'subscription_removed_successfully' => 'Subscription removed successfully',
    'subscription_plan_changed' => 'Subscription plan changed to: %s',
    'subscription_transferred' => 'Subscription transferred to: %s',
    
    // Error Messages
    'insufficient_permissions' => 'Insufficient permissions to access Bulk Domain Manager',
    'enter_at_least_one_url' => 'Please enter at least one URL/domain',
    'select_action_type' => 'Please select an action type and specific action',
    'no_valid_urls_found' => 'No valid URLs/domains found',
    'domain_not_found' => 'Domain not found: %s',
    'no_subscription_found' => 'No subscription found for domain: %s',
    'new_plan_id_required' => 'New plan ID is required for plan change',
    'new_subscriber_id_required' => 'New subscriber ID is required for subscriber change',
    'unknown_domain_action' => 'Unknown domain action: %s',
    'unknown_subscription_action' => 'Unknown subscription action: %s',
    
    // Warnings
    'destructive_operation_warning' => 'This operation will permanently delete data. Make sure you have backups.',
    'domains_not_found' => 'The following domains were not found: %s',
    'confirm_removal' => 'WARNING: This will permanently delete the selected domains/subscriptions. This action cannot be undone. Are you sure you want to continue?',
    
    // Validation
    'valid_urls_found' => 'Valid: %d URL(s) found',
    'invalid_urls_found' => 'Issues found: %d invalid URL(s). Valid: %d',
    'urls_entered' => '%d URL(s) entered',
    
    // Success Messages
    'bulk_operation_completed' => 'Bulk operation completed. %d operations succeeded.',
    
    // Form Validation
    'fix_following_errors' => 'Please fix the following errors:',
    'select_action_type_error' => 'Please select an action type (Domain or Subscription)',
    'select_specific_action_error' => 'Please select a specific action to perform',
    'select_service_plan_error' => 'Please select a service plan for the plan change',
    'select_subscriber_error' => 'Please select a subscriber for the ownership transfer',
);