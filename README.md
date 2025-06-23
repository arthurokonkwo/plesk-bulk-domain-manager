# Plesk Bulk Domain Manager

A powerful Plesk extension for managing multiple domains and subscriptions through bulk operations. This extension allows administrators to efficiently perform actions on multiple domains and subscriptions simultaneously, saving time and reducing manual work.

## 🚀 Features

### Domain Operations
- **Activate** - Enable multiple domains at once
- **Suspend** - Temporarily disable domains
- **Deactivate** - Disable domains without suspension
- **Remove** - Permanently delete domains (with confirmation)

### Subscription Operations  
- **Activate** - Enable multiple subscriptions
- **Suspend** - Temporarily disable subscriptions
- **Remove** - Permanently delete subscriptions (with confirmation)
- **Change Plan** - Bulk migrate subscriptions to a different service plan
- **Change Subscriber** - Transfer ownership of multiple subscriptions

### Additional Features
- **URL Validation** - Real-time validation of entered domains/URLs
- **Progress Tracking** - Visual feedback during bulk operations
- **Results Export** - Export operation results to CSV format
- **Audit Logging** - Complete logging of all operations for compliance
- **Responsive Design** - Works on desktop, tablet, and mobile devices
- **Security** - Permission-based access control and CSRF protection

## 📋 Requirements

- **Plesk Version**: 18.0.0 or higher
- **PHP Version**: 7.4 or higher
- **Permissions**: Domain Administration and Subscription Administration
- **Browser**: Modern browsers with JavaScript enabled

## 🛠 Installation

### Method 1: Manual Installation

1. **Download** the extension files from this repository
2. **Create ZIP package** with the following structure:
   ```
   bulk-domain-manager.zip
   ├── meta.xml
   ├── htdocs/
   ├── plib/
   └── locale/
   ```
3. **Upload** via Plesk Panel:
   - Go to **Extensions** > **My Extensions**
   - Click **"Add Extension"**
   - Upload the ZIP file
   - Follow installation prompts

### Method 2: Direct Installation

1. **Clone** this repository to your Plesk extensions directory:
   ```bash
   cd /usr/local/psa/admin/plib/modules/
   git clone https://github.com/arthurokonkwo/plesk-bulk-domain-manager.git bulk-domain-manager
   ```

2. **Set permissions**:
   ```bash
   chown -R psaadm:psaadm bulk-domain-manager
   chmod -R 755 bulk-domain-manager
   ```

3. **Register** the extension in Plesk

## 📖 Usage

### Getting Started

1. **Access the Extension**:
   - Log into Plesk Panel as Administrator
   - Navigate to **Extensions** > **My Extensions**
   - Click on **"Bulk Domain Manager"**

2. **Enter URLs/Domains**:
   - Paste or type one URL/domain per line in the text area
   - URLs can include or exclude protocols (http/https) and www
   - Example input:
     ```
     example.com
     www.another-site.com
     https://third-site.com
     ```

3. **Select Action Type**:
   - Choose **"Domain Actions"** for domain-specific operations
   - Choose **"Subscription Actions"** for subscription management

4. **Choose Specific Action**:
   - Select the desired action from the dropdown menu
   - For plan changes: Select the target service plan
   - For ownership transfers: Select the new subscriber

5. **Execute Operation**:
   - Review your selections
   - Click **"Execute Bulk Operation"**
   - Confirm destructive operations when prompted

### Best Practices

- **Test First**: Try operations on a few domains before bulk processing
- **Backup**: Ensure you have backups before destructive operations
- **Validation**: Review the URL validation feedback before proceeding
- **Monitoring**: Check the results table for any failed operations
- **Logging**: Export results for record-keeping and audit purposes

## 🔒 Security Features

- **Permission Checks**: Only users with domain administration rights can access
- **Input Validation**: All URLs and parameters are validated and sanitized
- **CSRF Protection**: Forms include CSRF tokens to prevent cross-site attacks
- **Confirmation Dialogs**: Destructive operations require explicit confirmation
- **Audit Trail**: All operations are logged with timestamps and details

## 🐛 Troubleshooting

### Common Issues

**Extension not appearing in Plesk:**
- Check file permissions (should be readable by psaadm)
- Verify meta.xml syntax
- Check Plesk error logs: `/var/log/plesk/panel.log`

**"Insufficient permissions" error:**
- Ensure user has "domain-administration" permission
- Check subscription limitations
- Verify user role allows bulk operations

**Operations failing:**
- Verify domains exist in Plesk
- Check domain/subscription status
- Review operation logs in Plesk

**JavaScript not working:**
- Ensure browser has JavaScript enabled
- Check browser console for errors
- Verify jQuery is loaded by Plesk

### Debug Mode

Enable debug logging in Plesk for detailed troubleshooting:

```bash
# Enable debug logging
echo "log_level = debug" >> /usr/local/psa/admin/conf/panel.ini

# View logs
tail -f /var/log/plesk/panel.log | grep bulk-domain-manager
```

## 🔧 Development

### Project Structure

```
plesk-bulk-domain-manager/
├── meta.xml                          # Extension manifest
├── htdocs/                           # Web interface
│   ├── css/bulk-manager.css          # Styling
│   ├── js/bulk-manager.js            # JavaScript functionality
│   └── images/                       # Icons and images
├── plib/                             # PHP backend
│   ├── controllers/IndexController.php
│   ├── library/BulkManager.php       # Core logic
│   ├── views/scripts/index/index.phtml
│   └── scripts/post-install.php
├── locale/                           # Translations
│   └── en-US.php
└── README.md                         # This file
```

### Contributing

1. **Fork** the repository
2. **Create** a feature branch: `git checkout -b feature/new-feature`
3. **Commit** your changes: `git commit -am 'Add new feature'`
4. **Push** to the branch: `git push origin feature/new-feature`
5. **Submit** a pull request

### Testing

Before submitting changes:

1. **Test** on a development Plesk instance
2. **Verify** all operations work correctly
3. **Check** error handling and edge cases
4. **Validate** HTML/CSS/JavaScript syntax
5. **Test** responsive design on different devices

## 📝 Changelog

### Version 1.0.0 (Initial Release)
- ✅ Domain bulk operations (activate, suspend, deactivate, remove)
- ✅ Subscription bulk operations (activate, suspend, remove)
- ✅ Subscription plan changes
- ✅ Subscription ownership transfers
- ✅ Real-time URL validation
- ✅ CSV export functionality
- ✅ Responsive web interface
- ✅ Comprehensive error handling
- ✅ Audit logging
- ✅ Security features

## 🤝 Support

- **Issues**: [GitHub Issues](https://github.com/arthurokonkwo/plesk-bulk-domain-manager/issues)
- **Documentation**: This README and inline code comments
- **Community**: Plesk Developer Community Forums

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Arthur Okonkwo**
- GitHub: [@arthurokonkwo](https://github.com/arthurokonkwo)
- Website: [arthurokonkwo.com] (https://arthurokonkwo.com)
- Email: arthurokonkwo007@gmail.com

## 🙏 Acknowledgments

- Plesk Development Team for the comprehensive API
- Open source community for tools and libraries
- Beta testers and early adopters for feedback

---

**⚠️ Important**: This extension performs powerful operations that can affect multiple domains and subscriptions simultaneously. Always test thoroughly in a development environment before using in production. Ensure you have proper backups before performing any destructive operations.