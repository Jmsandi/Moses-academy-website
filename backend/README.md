# Bayan Children's Foundation - Website & CMS

A complete Content Management System for the Bayan Children's Foundation website with PHP backend for cPanel hosting.

## 🌟 Features

- **Complete Website** for Bayan Children's Foundation
- **Admin Dashboard** to manage content
- **Stories Management** - Add and update success stories
- **Impact Updates** - Showcase your achievements
- **Image Upload System** - Upload photos for content
- **Secure Authentication** - JWT-based admin login
- **cPanel Ready** - Works on any shared hosting
- **No Installation Required** - Just PHP and MySQL

## 🚀 Quick Start

### For cPanel Hosting:

1. **Upload Files** to your cPanel hosting (via File Manager or FTP)
2. **Create MySQL Database** in cPanel
3. **Run Installation** at `https://yourdomain.com/setup/install.php`
4. **Delete setup folder** after installation
5. **Login** at `https://yourdomain.com/admin-login.html`

**Full cPanel deployment guide:** [CPANEL-DEPLOYMENT.md](CPANEL-DEPLOYMENT.md)

## 📁 What's Included

### Frontend Pages
- `index.html` - Homepage
- `about.html` - About page
- `blog.html` - Stories page (dynamic)
- `feature.html` - Impact page (dynamic)
- `course.html` - Programs
- `team.html` - Team page
- `contact.html` - Contact page
- `donate.html` - Donation page
- And more...

### Admin System
- `admin-login.html` - Admin login
- `admin-dashboard.html` - Content management dashboard
- Full CRUD operations for stories and impact updates
- Image upload and management
- Publish/unpublish functionality

### Backend (PHP)
- `api/login.php` - Authentication
- `api/stories.php` - Stories management
- `api/impact.php` - Impact updates management
- `config/` - Configuration files
- `includes/` - Helper functions

## 💻 Technical Requirements

- **PHP:** 7.4 or higher
- **MySQL:** 5.7 or higher (or MariaDB)
- **Apache:** with mod_rewrite enabled
- **PHP Extensions:** PDO, PDO_MySQL, JSON, GD

All standard on cPanel hosting!

## 📚 Documentation

- **[CPANEL-DEPLOYMENT.md](CPANEL-DEPLOYMENT.md)** - Complete cPanel deployment guide
- **[START-HERE.md](START-HERE.md)** - Quick start guide
- **[ADMIN-SYSTEM-OVERVIEW.md](ADMIN-SYSTEM-OVERVIEW.md)** - System overview

## 🔒 Security Features

- ✅ Password encryption (bcrypt)
- ✅ JWT authentication
- ✅ SQL injection protection
- ✅ XSS protection
- ✅ File upload validation
- ✅ Secure sessions

## 🎯 How It Works

1. **Admin logs in** via secure login page
2. **Creates content** (stories/impact updates)
3. **Uploads images** (optional)
4. **Publishes content**
5. **Visitors see updates** immediately on website

```
Admin Dashboard → Create Content → Save to Database → Website Updates
```

## 📊 Database

Uses MySQL with 3 tables:
- `admin_users` - Admin credentials
- `stories` - Success stories
- `impact_updates` - Impact achievements

## 🌐 Live Demo

After installation:
- **Website:** `https://yourdomain.com/`
- **Admin Login:** `https://yourdomain.com/admin-login.html`

## 🛠️ Installation

### Method 1: Web Installer (Recommended)
1. Upload all files to cPanel
2. Visit `https://yourdomain.com/setup/install.php`
3. Follow the wizard
4. Delete `/setup` folder

### Method 2: Manual Installation
1. Create MySQL database in cPanel
2. Import `setup/database.sql` via phpMyAdmin
3. Edit `config/config.php` with database details
4. Create admin user manually in database

## 📱 Pages Overview

### Public Pages (Visitors)
- Homepage with hero section
- About the foundation
- Programs and services
- Success stories (dynamic from admin)
- Impact updates (dynamic from admin)
- Team members
- Contact form
- Donation page

### Admin Pages (Staff Only)
- Secure login
- Dashboard with statistics
- Stories management
- Impact updates management
- Image library

## 🎨 Customization

### Change Admin Credentials
Edit `config/config.php`:
```php
define('ADMIN_USERNAME', 'your-username');
define('ADMIN_PASSWORD', 'your-password');
```

### Upload Limits
Edit `config/config.php`:
```php
define('MAX_FILE_SIZE', 5242880); // 5MB
```

### Allowed Image Types
```php
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
```

## 🔄 Updates & Maintenance

### Backup Your Data
Regular backups recommended:
- Database (via phpMyAdmin or cPanel backup)
- `/uploads` folder (contains all images)
- `config/config.php` (your settings)

### Update Content
Login to admin dashboard and manage content anytime!

## 🆘 Troubleshooting

### Can't login?
- Check username/password in `config/config.php`
- Verify database connection
- Clear browser cache

### Images not uploading?
- Check `/uploads` folder permissions (755)
- Verify file size (max 5MB)
- Check allowed file types

### Database connection error?
- Verify database credentials in `config/config.php`
- Check database exists
- Ensure user has privileges

**Full troubleshooting:** See [CPANEL-DEPLOYMENT.md](CPANEL-DEPLOYMENT.md)

## 📞 Support

- **Email:** info@bayanfoundation.org
- **Documentation:** See docs folder
- **Issues:** Check troubleshooting guide

## 📄 License

© 2025 Bayan Children's Foundation. All rights reserved.

## 🙏 Credits

**Built with:**
- PHP & MySQL
- Bootstrap 5
- Font Awesome
- Owl Carousel
- WOW.js

**Made by:** Jmsandi

---

## ✨ Features at a Glance

| Feature | Description |
|---------|-------------|
| 📝 Stories | Add and manage success stories |
| 📊 Impact | Track and display your impact |
| 🖼️ Images | Upload and manage images |
| 🔐 Secure | JWT authentication & encryption |
| 📱 Responsive | Works on all devices |
| ⚡ Fast | Optimized for performance |
| 🎨 Modern | Beautiful, professional design |
| 🔄 Dynamic | Real-time content updates |

---

## 🚀 Getting Started

1. Read [CPANEL-DEPLOYMENT.md](CPANEL-DEPLOYMENT.md)
2. Upload to your cPanel hosting
3. Run the installer
4. Start managing content!

**Your foundation deserves a great online presence. Let's build it together!** 🎉

---

*Bayan Children's Foundation - Empowering Communities Through Education and Support*

