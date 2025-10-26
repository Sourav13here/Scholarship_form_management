# 🎓 Scholarship Application System

[![PHP Version](https://img.shields.io/badge/PHP-7.0%2B-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-success)](https://github.com)

## 📋 Overview

This system provides a complete solution for managing scholarship applications for Class X students focusing on Science and Math subjects. It includes a student application portal, admin dashboard, PDF admit card generation, and Excel export functionality.

## ✨ Features

### 🎯 Student Portal
- **Modern Application Form** - Responsive design with real-time validation
- **Instant PDF Admit Cards** - Download immediately after submission
- **Unique Application IDs** - Auto-generated (format: NCI20240001)
- **Email Validation** - Prevents duplicate submissions
- **Mobile Friendly** - Works on all devices

### 👨‍💼 Admin Dashboard
- **Secure Login System** - Session-based authentication
- **Real-time Statistics** - Total and daily application counts
- **Student List Management** - View all applications in a table
- **Excel Export** - Professional formatted spreadsheet export
- **PDF Downloads** - Download admit cards for any student
- **Responsive Design** - Works on desktop and mobile

### 📊 Data Management
- **SQLite Database** - Lightweight, no external database required
- **Automatic Backups** - Easy to backup single file
- **Secure Storage** - Protected with .htaccess rules
- **Data Validation** - Server-side and client-side validation

### 📄 PDF Generation
- **Professional Admit Cards** - Branded with institute details
- **FPDF Library** - Reliable PDF generation
- **Downloadable** - By both students and admin
- **Formatted Layout** - Clean and professional design

## 🚀 Quick Start

### Prerequisites
- PHP 7.0 or higher
- PDO SQLite extension (usually enabled by default)
- Web server (Apache/Nginx) or PHP built-in server
- FPDF library

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/nucleon-scholarship-system.git
   cd nucleon-scholarship-system
   ```

2. **Install FPDF Library**
   ```bash
   cd HFSSS
   php install_fpdf.php
   ```
   Or download manually from [fpdf.org](http://www.fpdf.org/)

3. **Start the server**
   ```bash
   php -S localhost:8000
   ```
   Or double-click `START_SERVER.bat` (Windows)

4. **Access the system**
   - Homepage: `http://localhost:8000/index.html`
   - Student Form: `http://localhost:8000/form.html`
   - Admin Panel: `http://localhost:8000/admin-panel.php`

### Default Admin Credentials
```
Username: admin
Password: admin123
```
⚠️ **IMPORTANT:** Change these credentials in `admin-panel.php` (lines 6-7) before deployment!

## 📁 Project Structure

```
nucleon-scholarship-system/
├── BBTS/
│   ├── form.html                  # Student application form
│   ├── admin-panel.php            # Admin dashboard
│   ├── submit_form.php            # Form submission handler
│   ├── download_admit_card.php    # PDF generation
│   ├── export_excel.php           # Excel export
│   ├── config.php                 # Database configuration
│   ├── fpdf/                      # PDF library
│   │   └── fpdf.php
│   ├── index.html                 # System homepage
│   ├── database.html              # Database schema viewer
│   ├── README.md                  # Documentation
│   ├── SETUP.txt                  # Setup guide
│   ├── .htaccess                  # Security configuration
│   └── ... (other files)
├── README.md                      # This file
└── .gitignore                     # Git ignore rules
```

## 🎨 Screenshots

### Student Application Form
Modern, responsive form with real-time validation and instant PDF download.

### Admin Dashboard
Professional dashboard with statistics, student list, and Excel export.

### PDF Admit Card
Branded admit card with all student details and important instructions.

## 💻 Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Backend:** PHP 7+
- **Database:** SQLite 3
- **PDF Generation:** FPDF 1.86
- **Authentication:** PHP Sessions
- **Styling:** Custom CSS with gradient design

## 📊 Database Schema

### Table: `applications`

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

## 🔧 Configuration

### Change Admin Password
Edit `BBTS/admin-panel.php` lines 6-7:
```php
$ADMIN_USERNAME = 'your_username';
$ADMIN_PASSWORD = 'your_secure_password';
```

### Customize Colors
Edit CSS in `admin-panel.php` and `form.html`:
```css
/* Primary gradient */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Modify Application ID Format
Edit `submit_form.php` line 40:
```php
$applicationId = 'NCI' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
```

## 🔒 Security Features

- ✅ Session-based authentication
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ XSS protection (htmlspecialchars)
- ✅ Email validation
- ✅ Phone number validation
- ✅ Duplicate email prevention
- ✅ Database file protection (.htaccess)
- ✅ Secure file permissions

## 📱 Browser Compatibility

- ✅ Chrome/Edge (Latest)
- ✅ Firefox (Latest)
- ✅ Safari (Latest)
- ✅ Mobile browsers (iOS/Android)

## 🚀 Deployment

### Production Server Setup

1. Upload all files to web server
2. Install FPDF library
3. Set file permissions:
   - Files: `644`
   - Directories: `755`
   - Database: `666` (when created)
4. Configure `.htaccess` for security
5. Enable HTTPS
6. Change admin credentials
7. Test all functionality

### Recommended Hosting
- Shared hosting with PHP 7.0+
- VPS with Apache/Nginx
- Cloud platforms (AWS, DigitalOcean, etc.)

## 📖 Documentation

- **[SETUP.txt](BBTS/SETUP.txt)** - Detailed setup instructions
- **[QUICK_START.txt](BBTS/QUICK_START.txt)** - Quick start guide
- **[SYSTEM_OVERVIEW.md](BBTS/SYSTEM_OVERVIEW.md)** - Technical overview
- **[EXCEL_EXPORT_GUIDE.txt](BBTS/EXCEL_EXPORT_GUIDE.txt)** - Excel export guide
- **[GITHUB_SETUP_GUIDE.txt](GITHUB_SETUP_GUIDE.txt)** - GitHub setup guide

## 🧪 Testing

Run the system health checker:
```bash
php test_system.php
```
Or double-click `TEST_SYSTEM.bat` (Windows)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Authors

- **Nucleon Coaching Institute** - *Initial work*

## 🙏 Acknowledgments

- Holyflower Senior Secondary School Teok for collaboration
- FPDF library for PDF generation
- All contributors and testers

## 📞 Support

For issues or questions:
- Open an issue on GitHub
- Email: info@nucleoncoaching.com
- Check documentation files

## 🎯 Roadmap

### Future Enhancements
- [ ] Email notifications to students
- [ ] SMS integration for admit cards
- [ ] Advanced search and filter in admin panel
- [ ] Application status tracking
- [ ] Payment integration
- [ ] Multi-language support
- [ ] Bulk PDF download
- [ ] Analytics dashboard

## 📊 Stats

![GitHub repo size](https://img.shields.io/github/repo-size/YOUR_USERNAME/nucleon-scholarship-system)
![GitHub stars](https://img.shields.io/github/stars/YOUR_USERNAME/nucleon-scholarship-system?style=social)
![GitHub forks](https://img.shields.io/github/forks/YOUR_USERNAME/nucleon-scholarship-system?style=social)

---

**Made with ❤️ for Nucleon Coaching Institute, Durgapur**

**Version:** 1.0 | **Status:** Production Ready | **Last Updated:** October 2024
