# Bulk Domain Manager

A powerful Plesk extension for managing multiple domains and subscriptions through bulk operations.

## Features

### Domain Operations
- **Activate** multiple domains simultaneously
- **Suspend** domains in bulk
- **Deactivate** multiple domains at once
- **Remove** domains with confirmation (destructive operation)

### Subscription Operations  
- **Activate** multiple subscriptions
- **Suspend** subscription services in bulk
- **Remove** subscriptions with confirmation
- **Change Service Plan** for multiple subscriptions
- **Transfer Ownership** to different subscribers

### Additional Capabilities
- **Real-time URL Validation** - Validates domain formats as you type
- **Progress Tracking** - Visual feedback during bulk operations
- **Results Export** - Export operation results to CSV format
- **Audit Logging** - Complete logging of all operations for compliance
- **Security Features** - Permission-based access control and CSRF protection
- **Responsive Design** - Works seamlessly on desktop, tablet, and mobile devices

## Requirements

- Plesk 18.0.0 or higher
- PHP 7.4 or higher
- Domain Administration permissions
- Subscription Administration permissions

## Usage

1. Enter URLs/domains (one per line) in the text area
2. Select action type (Domain or Subscription operations)
3. Choose specific action from the dropdown
4. For plan changes: select target service plan
5. For ownership transfers: select new subscriber
6. Click "Execute Bulk Operation"

## Safety Features

- Confirmation dialogs for destructive operations
- Real-time validation of domain formats
- Detailed operation results with success/failure status
- Export functionality for audit trails
- Comprehensive error handling and logging

This extension is perfect for hosting providers and administrators who need to manage large numbers of domains and subscriptions efficiently.