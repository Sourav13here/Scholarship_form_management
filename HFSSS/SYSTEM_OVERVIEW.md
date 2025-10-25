# Nucleon Scholarship Application System - Complete Overview

## 🎯 System Purpose

A complete web-based scholarship application management system for **Nucleon Coaching Institute, Durgapur** in collaboration with **Holyflower Senior Secondary School Teok**, designed for Class X students focusing on Science and Math subjects.

---

## 📋 System Components

### 1. **Student-Facing Components**

#### `form.html` - Application Form
- Modern, responsive design with gradient UI
- All required fields from your template:
  - Name, Class, School Name
  - Address (paragraph)
  - Contact Number, Alternative Number
  - Email ID
  - Achievements in Science and Math
  - Declaration checkbox
- Real-time validation
- AJAX form submission
- Instant admit card download link after submission

#### `download_admit_card.php` - PDF Generator
- Generates professional PDF admit cards
- Includes all applicant information
- Unique application ID (format: NCI20240001)
- Branded header with institute details
- Important instructions section
- Downloadable by both students and admin

---

### 2. **Admin Components**

#### `admin-panel.php` - Admin Dashboard
- Secure login system (Username: admin, Password: admin123)
- Real-time statistics:
  - Total applications count
  - Today's applications count
- Complete applications table with:
  - Application ID
  - Student details
  - Submission date/time
  - Download PDF action button
- Responsive design for mobile/desktop

#### `database.html` - Database Schema Viewer
- Visual representation of database structure
- Field descriptions and constraints
- Quick navigation links
- Educational resource for understanding data storage

---

### 3. **Backend Components**

#### `config.php` - Database Configuration
- SQLite database setup
- Connection management
- Auto-initialization of database tables
- Database path configuration

#### `submit_form.php` - Form Handler
- Processes form submissions
- Validates all input data:
  - Email format validation
  - 10-digit phone number validation
  - Required field checks
  - Duplicate email prevention
- Generates unique application IDs
- Returns JSON responses
- Stores data in SQLite database

---

### 4. **Database**

#### `scholarship.db` - SQLite Database
**Table: applications**

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INTEGER | PRIMARY KEY, AUTO INCREMENT | Internal ID |
| application_id | TEXT | UNIQUE, NOT NULL | Display ID (NCI20240001) |
| name | TEXT | NOT NULL | Student name |
| class | TEXT | NOT NULL | Current class |
| school | TEXT | NOT NULL | School name |
| address | TEXT | NOT NULL | Full address |
| contact | TEXT | NOT NULL | Primary contact (10 digits) |
| alt_contact | TEXT | OPTIONAL | Alternative contact |
| email | TEXT | NOT NULL | Email address |
| achievements | TEXT | OPTIONAL | Science/Math achievements |
| declaration | INTEGER | NOT NULL | Declaration (1=agreed) |
| submission_date | DATETIME | AUTO | Submission timestamp |
| status | TEXT | DEFAULT 'pending' | Application status |

---

### 5. **Support Files**

#### `index.html` - Landing Page
- System overview
- Quick navigation to all components
- Setup status indicators
- Feature highlights
- Documentation links

#### `README.md` - Complete Documentation
- Detailed installation instructions
- Feature list
- Configuration guide
- Troubleshooting section
- Security recommendations

#### `SETUP.txt` - Quick Setup Guide
- Step-by-step installation
- Multiple setup options
- Troubleshooting tips
- Deployment instructions

#### `test_system.php` - System Tester
- Automated system verification
- Checks PHP version
- Tests database connectivity
- Verifies file permissions
- Validates FPDF installation
- Provides actionable feedback

#### `install_fpdf.php` - FPDF Installer
- Automatic FPDF download
- Extraction and installation
- Cleanup of temporary files
- Error handling with fallback instructions

#### `composer.json` - Dependency Manager
- PHP version requirement
- FPDF library dependency
- Autoload configuration
- Installation scripts

#### `.htaccess` - Security Configuration
- Database file protection
- Directory listing prevention
- Security headers
- PHP configuration

#### `.gitignore` - Version Control
- Excludes database files
- Ignores vendor dependencies
- Filters temporary files

---

## 🔄 System Workflow

### Student Application Process

1. **Student visits** `form.html`
2. **Fills out** all required information
3. **Submits** form via AJAX
4. **Backend** (`submit_form.php`) validates data
5. **Database** stores application with unique ID
6. **Student receives** success message with application ID
7. **Student downloads** PDF admit card immediately

### Admin Management Process

1. **Admin visits** `admin-panel.php`
2. **Logs in** with credentials
3. **Views** dashboard with statistics
4. **Browses** all applications in table
5. **Downloads** any student's admit card
6. **Monitors** application trends

---

## 🚀 Quick Start

### Option 1: Fastest Setup
```bash
cd "s:\growtez\1.2 Nucleon Scholarship\BBTS"
php test_system.php          # Test the system
php install_fpdf.php         # Install FPDF
php -S localhost:8000        # Start server
```
Then open: `http://localhost:8000/index.html`

### Option 2: Using Composer
```bash
cd "s:\growtez\1.2 Nucleon Scholarship\BBTS"
composer install             # Install dependencies
php -S localhost:8000        # Start server
```

### Option 3: Using XAMPP/WAMP
1. Copy BBTS folder to `htdocs/` or `www/`
2. Start Apache
3. Visit `http://localhost/BBTS/`

---

## 🔒 Security Features

1. **Input Validation**
   - Email format validation
   - Phone number pattern matching
   - Required field enforcement
   - SQL injection prevention (PDO prepared statements)

2. **Database Protection**
   - `.htaccess` rules block direct access
   - SQLite file permissions
   - Prepared statements for all queries

3. **Admin Authentication**
   - Session-based login
   - Password protection
   - Logout functionality

4. **XSS Prevention**
   - HTML entity encoding
   - Content Security Policy headers
   - Input sanitization

---

## 📊 Features Checklist

- ✅ Modern, responsive form design
- ✅ All template fields implemented
- ✅ SQLite database storage
- ✅ Unique application ID generation
- ✅ PDF admit card generation
- ✅ Student download functionality
- ✅ Admin panel with statistics
- ✅ Admin PDF download
- ✅ Email validation
- ✅ Duplicate prevention
- ✅ Mobile-friendly design
- ✅ Secure authentication
- ✅ Real-time statistics
- ✅ Professional PDF layout
- ✅ Easy deployment

---

## 🛠️ Customization Guide

### Change Colors
Edit CSS in `form.html` and `admin-panel.php`:
```css
/* Primary gradient */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Add Form Fields
1. Add HTML input in `form.html`
2. Update validation in `submit_form.php`
3. Modify database schema in `config.php`
4. Include in PDF template in `download_admit_card.php`

### Modify Admin Credentials
Edit `admin-panel.php` lines 7-8:
```php
$ADMIN_USERNAME = 'your_username';
$ADMIN_PASSWORD = 'your_secure_password';
```

### Change Application ID Format
Edit `submit_form.php` line 40:
```php
$applicationId = 'NCI' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
```

---

## 📱 Browser Compatibility

- ✅ Chrome/Edge (Latest)
- ✅ Firefox (Latest)
- ✅ Safari (Latest)
- ✅ Mobile browsers (iOS/Android)

---

## 🔧 System Requirements

- **PHP:** 7.0 or higher
- **Extensions:** PDO, PDO_SQLite
- **Web Server:** Apache/Nginx/PHP built-in
- **FPDF:** Version 1.85 or higher
- **Storage:** Minimal (SQLite database)

---

## 📞 Support & Troubleshooting

### Common Issues

**Issue:** FPDF not installed
**Solution:** Run `php install_fpdf.php` or download from fpdf.org

**Issue:** Database not created
**Solution:** Check folder write permissions

**Issue:** Form not submitting
**Solution:** Check browser console, verify PHP is running

**Issue:** PDF not downloading
**Solution:** Ensure FPDF is installed correctly

### Getting Help

1. Check `README.md` for detailed documentation
2. Run `test_system.php` for diagnostics
3. Review PHP error logs
4. Check browser console for JavaScript errors

---

## 📈 Future Enhancements (Optional)

- Email notifications to students
- SMS integration for admit cards
- Excel export of applications
- Advanced search/filter in admin panel
- Application status tracking
- Payment integration
- Multi-language support
- Bulk PDF download

---

## 📄 License

Proprietary - Nucleon Coaching Institute, Durgapur

---

## 👨‍💻 Technical Stack

- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Backend:** PHP 7+
- **Database:** SQLite 3
- **PDF Generation:** FPDF 1.85
- **Authentication:** PHP Sessions
- **Validation:** Client-side + Server-side

---

## 🎓 About

**Developed for:** Nucleon Coaching Institute, Durgapur  
**Collaboration:** Holyflower Senior Secondary School Teok  
**Target:** Class X Students (Science & Math Focus)  
**Version:** 1.0  
**Last Updated:** October 2024

---

**System Status:** ✅ Production Ready

All components are fully functional and ready for deployment to a web server.
