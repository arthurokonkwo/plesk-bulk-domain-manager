(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Initialize the form on page load
        initializeForm();
        
        // Bind event handlers
        bindEvents();
    });
    
    function bindEvents() {
        // Action type selection
        $('input[name="action_type"]').on('change', function() {
            var selectedType = $(this).val();
            showActionSection(selectedType);
        });
        
        // Subscription action selection
        $('#subscription-action-select').on('change', function() {
            handleSubscriptionActionChange();
        });
        
        // URL validation
        $('#urls').on('input', validateUrls);
        
        // Form submission
        $('#bulkManagerForm').on('submit', function(e) {
            // Validate form
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
            
            // Check for destructive operations
            var selectedAction = $('select[name="selected_action"]').val();
            if (selectedAction === 'remove') {
                if (!confirm('WARNING: This will permanently delete the selected domains/subscriptions. This action cannot be undone. Are you sure you want to continue?')) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Show processing indicator
            $('#submit-btn').hide();
            $('#processing-indicator').show();
        });
        
        // Export CSV
        $('#export-csv').on('click', exportResultsToCSV);
    }
    
    function initializeForm() {
        // Show appropriate action section based on selected type
        var selectedType = $('input[name="action_type"]:checked').val();
        if (selectedType) {
            showActionSection(selectedType);
        }
        
        // Show plan/subscriber selection if needed
        var selectedAction = $('#subscription-action-select').val();
        if (selectedAction) {
            handleSubscriptionActionChange();
        }
        
        // Initial URL validation
        validateUrls();
    }
    
    function showActionSection(actionType) {
        $('#domain-actions, #subscription-actions').hide();
        
        if (actionType === 'domain') {
            $('#domain-actions').show();
            $('#subscription-action-select').val('');
            $('#plan-selection, #subscriber-selection').hide();
        } else if (actionType === 'subscription') {
            $('#subscription-actions').show();
            $('#domain-action-select').val('');
        }
    }
    
    function handleSubscriptionActionChange() {
        var selectedAction = $('#subscription-action-select').val();
        
        $('#plan-selection, #subscriber-selection').hide();
        
        if (selectedAction === 'change_plan') {
            $('#plan-selection').show();
        } else if (selectedAction === 'change_subscriber') {
            $('#subscriber-selection').show();
        }
    }
    
    function validateUrls() {
        var urlText = $('#urls').val().trim();
        var feedback = $('#url-validation-feedback');
        
        if (!urlText) {
            feedback.hide();
            return;
        }
        
        var urls = urlText.split('\n').filter(function(line) {
            return line.trim() !== '';
        });
        
        var validUrls = [];
        var invalidUrls = [];
        
        urls.forEach(function(url) {
            url = url.trim();
            if (isValidUrl(url)) {
                validUrls.push(url);
            } else {
                invalidUrls.push(url);
            }
        });
        
        if (invalidUrls.length === 0) {
            feedback.removeClass('invalid').addClass('valid');
            feedback.html('<strong>? Valid:</strong> ' + validUrls.length + ' URL(s) found');
            feedback.show();
        } else {
            feedback.removeClass('valid').addClass('invalid');
            feedback.html('<strong>? Issues found:</strong> ' + invalidUrls.length + ' invalid URL(s). Valid: ' + validUrls.length);
            feedback.show();
        }
    }
    
    function isValidUrl(url) {
        // Remove protocols and www
        var cleanUrl = url.replace(/^https?:\/\//, '').replace(/^www\./, '');
        
        // Basic domain validation regex - allow multi-level domains
        var domainRegex = /^([a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]?\.)+[a-zA-Z]{2,}$/;

        return domainRegex.test(cleanUrl.split('/')[0]);
    }
    
    function validateForm() {
        var errors = [];
        
        // Check URLs
        var urls = $('#urls').val().trim();
        if (!urls) {
            errors.push('Please enter at least one URL/domain');
        }
        
        // Check action type
        var actionType = $('input[name="action_type"]:checked').val();
        if (!actionType) {
            errors.push('Please select an action type (Domain or Subscription)');
        }
        
        // Check selected action
        var selectedAction = $('select[name="selected_action"]').val();
        if (!selectedAction) {
            errors.push('Please select a specific action to perform');
        }
        
        // Check plan selection if needed
        if (selectedAction === 'change_plan') {
            var planId = $('#new_plan_id').val();
            if (!planId) {
                errors.push('Please select a service plan for the plan change');
            }
        }
        
        // Check subscriber selection if needed
        if (selectedAction === 'change_subscriber') {
            var subscriberId = $('#new_subscriber_id').val();
            if (!subscriberId) {
                errors.push('Please select a subscriber for the ownership transfer');
            }
        }
        
        if (errors.length > 0) {
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
            return false;
        }
        
        return true;
    }
    
    function exportResultsToCSV() {
        var csv = [];
        var headers = ['Domain/URL', 'Action', 'Status', 'Message', 'Timestamp'];
        csv.push(headers.join(','));
        
        $('.results-table tbody tr').each(function() {
            var row = [];
            $(this).find('td').each(function(index) {
                var text = $(this).text().trim();
                // Escape quotes and wrap in quotes if contains comma
                if (text.includes(',') || text.includes('"')) {
                    text = '"' + text.replace(/"/g, '""') + '"';
                }
                row.push(text);
            });
            csv.push(row.join(','));
        });
        
        // Download CSV
        var csvContent = csv.join('\n');
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'bulk-operation-results-' + new Date().toISOString().split('T')[0] + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
})(jQuery);