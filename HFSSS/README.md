# Scholarship Application System


## Features

- ✅ Modern, responsive application form
- ✅ SQLite database for data storage
- ✅ Admin panel with statistics
- ✅ PDF admit card generation
- ✅ Email validation and duplicate prevention
- ✅ Secure admin authentication

## Files Structure

```
BBTS/
├── form.html              - Student application form
├── submit_form.php        - Form submission handler
├── download_admit_card.php - PDF generation
├── admin-panel.php        - Admin dashboard
├── database.html          - Database schema viewer
├── config.php             - Database configuration
├── scholarship.db         - SQLite database (auto-created)
└── fpdf/                  - PDF library folder
    └── fpdf.php           - FPDF library
```

## Installation Steps

### 1. Install FPDF Library

Download FPDF from: http://www.fpdf.org/

1. Download FPDF 1.85 (or latest version)
2. Extract the files
3. Copy `fpdf.php` to the `fpdf/` folder in this directory

**OR** use Composer:
```bash
composer require setasign/fpdf
```

### 2. Set Up Web Server

#### Option A: Using XAMPP/WAMP
1. Copy the `BBTS` folder to `htdocs/` (XAMPP) or `www/` (WAMP)
2. Start Apache server
3. Access: `http://localhost/BBTS/form.html`

#### Option B: Using PHP Built-in Server
```bash
cd "s:\growtez\1.2 Nucleon Scholarship\BBTS"
php -S localhost:8000
```
Then access: `http://localhost:8000/form.html`

### 3. Configure Permissions

Ensure the web server has write permissions for:
- `scholarship.db` (will be auto-created)
- The BBTS directory

On Windows, this is usually automatic.
On Linux/Mac:
```bash
chmod 755 BBTS/
chmod 666 BBTS/scholarship.db
```

## Usage

### For Students

1. Visit `form.html`
2. Fill out all required fields
3. Submit the form
4. Download the PDF admit card after successful submission

### For Administrators

1. Visit `admin-panel.php`
2. Login with credentials:
   - **Username:** admin
   - **Password:** admin123
   - ⚠️ **IMPORTANT:** Change these in `admin-panel.php` line 7-8

3. View statistics and all applications
4. Download admit cards for any student

## Database

- **Type:** SQLite
- **File:** `scholarship.db`
- **Location:** Same directory as PHP files
- **View Schema:** Open `database.html` in browser

### Database Schema

**Table: applications**
- `id` - Primary key (auto-increment)
- `application_id` - Unique ID (e.g., NCI20240001)
- `name` - Student name
- `class` - Current class
- `school` - School name
- `address` - Full address
- `contact` - Primary contact (10 digits)
- `alt_contact` - Alternative contact
- `email` - Email address (unique)
- `achievements` - Science/Math achievements
- `declaration` - Declaration acceptance
- `submission_date` - Auto-timestamp
- `status` - Application status

## Security Recommendations

1. **Change Admin Password**
   - Edit `admin-panel.php`
   - Update `$ADMIN_PASSWORD` variable
   - Consider using password hashing (bcrypt)

2. **Add .htaccess Protection**
   Create `.htaccess` in BBTS folder:
   ```apache
   # Protect database file
   <Files "scholarship.db">
       Order allow,deny
       Deny from all
   </Files>
   ```

3. **Enable HTTPS**
   - Use SSL certificate for production
   - Redirect HTTP to HTTPS

4. **Backup Database**
   - Regularly backup `scholarship.db`
   - Store backups securely

## Customization

### Change Colors
Edit the CSS in `form.html` and `admin-panel.php`:
- Primary color: `#667eea`
- Secondary color: `#764ba2`

### Modify Form Fields
1. Edit `form.html` - Add/remove fields
2. Update `submit_form.php` - Handle new fields
3. Update `config.php` - Modify database schema
4. Update `download_admit_card.php` - Include in PDF

### Email Notifications
Add email functionality in `submit_form.php`:
```php
// After successful submission
mail($email, "Application Submitted", "Your application ID: $applicationId");
```

## Troubleshooting

### Database Not Created
- Check folder write permissions
- Ensure PHP PDO SQLite extension is enabled

### PDF Not Generating
- Verify FPDF library is installed in `fpdf/fpdf.php`
- Check PHP error logs

### Form Not Submitting
- Check browser console for JavaScript errors
- Verify `submit_form.php` path is correct
- Ensure PHP is running

## Requirements

- PHP 7.0 or higher
- PDO SQLite extension
- Apache/Nginx web server (or PHP built-in server)
- FPDF library

## Support

For issues or questions:
- Email: info@nucleoncoaching.com
- Check PHP error logs
- Review browser console for JavaScript errors

## License

Proprietary - Nucleon Coaching Institute, Durgapur

---

**Version:** 1.0  
**Last Updated:** October 2024
