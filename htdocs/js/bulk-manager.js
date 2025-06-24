/**
 * Bulk Domain Manager JavaScript
 * Interactive functionality for the bulk management interface
 */

(function($) {
    'use strict';
    
    var BulkManager = {
        
        init: function() {
            this.bindEvents();
            this.initializeForm();
        },
        
        bindEvents: function() {
            // Action type selection
            $('input[name="action_type"]').on('change', this.handleActionTypeChange);
            
            // Subscription action selection
            $('#subscription-action-select').on('change', this.handleSubscriptionActionChange);
            
            // URL validation
            $('#urls').on('input', this.validateUrls);
            
            // Form submission
            $('#bulkManagerForm').on('submit', this.handleFormSubmit);
            
            // Export CSV
            $('#export-csv').on('click', this.exportResultsToCSV);
            
            // Real-time URL count
            $('#urls').on('input', this.updateUrlCount);
        },
        
        initializeForm: function() {
            // Show appropriate action section based on selected type
            var selectedType = $('input[name="action_type"]:checked').val();
            if (selectedType) {
                this.showActionSection(selectedType);
            }
            
            // Show plan/subscriber selection if needed
            var selectedAction = $('#subscription-action-select').val();
            if (selectedAction) {
                this.handleSubscriptionActionChange();
            }
            
            // Initial URL validation
            this.validateUrls();
        },
        
        handleActionTypeChange: function() {
            var selectedType = $(this).val();
            BulkManager.showActionSection(selectedType);
        },
        
        showActionSection: function(actionType) {
            $('#domain-actions, #subscription-actions').hide();
            
            if (actionType === 'domain') {
                $('#domain-actions').show();
                $('#subscription-action-select').val('');
                $('#plan-selection, #subscriber-selection').hide();
            } else if (actionType === 'subscription') {
                $('#subscription-actions').show();
                $('#domain-action-select').val('');
            }
        },
        
        handleSubscriptionActionChange: function() {
            var selectedAction = $('#subscription-action-select').val();
            
            $('#plan-selection, #subscriber-selection').hide();
            
            if (selectedAction === 'change_plan') {
                $('#plan-selection').show();
            } else if (selectedAction === 'change_subscriber') {
                $('#subscriber-selection').show();
            }
        },
        
        validateUrls: function() {
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
                if (BulkManager.isValidUrl(url)) {
                    validUrls.push(url);
                } else {
                    invalidUrls.push(url);
                }
            });
            
            if (invalidUrls.length === 0) {
                feedback.removeClass('invalid').addClass('valid');
                feedback.html('<strong>✓ Valid:</strong> ' + validUrls.length + ' URL(s) found');
                feedback.show();
            } else {
                feedback.removeClass('valid').addClass('invalid');
                feedback.html('<strong>⚠ Issues found:</strong> ' + invalidUrls.length + ' invalid URL(s). Valid: ' + validUrls.length);
                feedback.show();
            }
        },
        
        isValidUrl: function(url) {
            // Remove protocols and www
            var cleanUrl = url.replace(/^https?:\/\//, '').replace(/^www\./, '');
            
            // Basic domain validation regex - allow multi-level domains
            var domainRegex = /^([a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]?\.)+[a-zA-Z]{2,}$/;

            return domainRegex.test(cleanUrl.split('/')[0]);
        },
        
        updateUrlCount: function() {
            var urlText = $('#urls').val().trim();
            var count = 0;
            
            if (urlText) {
                count = urlText.split('\n').filter(function(line) {
                    return line.trim() !== '';
                }).length;
            }
            
            // Update placeholder text with count
            if (count > 0) {
                $('#urls').attr('title', count + ' URL(s) entered');
            }
        },
        
        handleFormSubmit: function(e) {
            var form = $(this);
            var submitBtn = $('#submit-btn');
            var processingIndicator = $('#processing-indicator');
            
            // Validate form
            if (!BulkManager.validateForm()) {
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
            submitBtn.hide();
            processingIndicator.show();
            
            // The form will submit normally, this is just UI feedback
        },
        
        validateForm: function() {
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
        },
        
        exportResultsToCSV: function() {
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
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        BulkManager.init();
    });
    
})(jQuery);