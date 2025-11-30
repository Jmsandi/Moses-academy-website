# 🎉 Backend Converted to PHP for cPanel!

## ✅ What Was Changed

Your Bayan Children's Foundation CMS has been successfully converted from Node.js to PHP for seamless cPanel deployment!

### Old (Node.js) ❌
- Required Node.js installation
- Used SQLite database
- Needed terminal/command line
- Complex deployment process
- Not supported by most shared hosting

### New (PHP) ✅
- Works on any cPanel hosting
- Uses MySQL database
- Web-based installer
- Simple upload and run
- Supported everywhere!

## 📁 New File Structure

```
Your Project/
├── api/                    ← PHP API endpoints
│   ├── login.php
│   ├── verify.php
│   ├── stories.php
│   └── impact.php
├── config/                 ← Configuration
│   ├── config.php
│   └── database.php
├── includes/               ← Helper functions
│   └── auth.php
├── setup/                  ← Web installer
│   ├── install.php
│   ├── install_process.php
│   └── database.sql
├── uploads/                ← Image storage
├── .htaccess              ← Apache configuration
├── admin-login.html       ← Admin login
├── admin-dashboard.html   ← Dashboard
└── All other HTML files
```

## 🚀 How to Deploy to cPanel

### Step 1: Upload Files
1. Login to cPanel
2. Open File Manager
3. Go to `public_html`
4. Upload all files (or upload ZIP and extract)

### Step 2: Create Database
1. In cPanel → **MySQL® Databases**
2. Create database: `bayan_cms`
3. Create user with strong password
4. Add user to database with ALL privileges

### Step 3: Run Installer
1. Visit: `https://yourdomain.com/setup/install.php`
2. Enter database info
3. Create admin credentials
4. Click Install

### Step 4: Secure
1. **DELETE** the `/setup` folder
2. Login at: `https://yourdomain.com/admin-login.html`

## 📊 Database (MySQL)

Your data is stored in MySQL with these tables:

| Table | Purpose |
|-------|---------|
| `admin_users` | Admin login credentials |
| `stories` | Success stories |
| `impact_updates` | Impact achievements |

## 🔧 Configuration

Edit `config/config.php` to change settings:

```php
// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'bayan_cms');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// Admin (will be hashed)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'your_password');

// Security
define('JWT_SECRET', 'random-secret-key');

// Upload limits
define('MAX_FILE_SIZE', 5242880); // 5MB
```

## 🎯 API Endpoints

All API endpoints work via clean URLs:

- `POST /api/admin/login` - Admin login
- `GET /api/admin/verify` - Verify token
- `GET /api/stories` - Get all stories
- `POST /api/stories` - Create story (auth required)
- `PUT /api/stories?id=X` - Update story (auth required)
- `DELETE /api/stories?id=X` - Delete story (auth required)
- Same for `/api/impact`

## ✨ Features

- ✅ **JWT Authentication** - Secure token-based auth
- ✅ **Image Upload** - Support for JPG, PNG, GIF (max 5MB)
- ✅ **Publish/Draft** - Control content visibility
- ✅ **REST API** - Clean, RESTful endpoints
- ✅ **Security** - Password hashing, SQL injection protection
- ✅ **Easy Deployment** - Just upload and run installer!

## 🌐 After Installation

### Public URLs
- Homepage: `https://yourdomain.com/index.html`
- Stories: `https://yourdomain.com/blog.html`
- Impact: `https://yourdomain.com/feature.html`

### Admin URLs
- Login: `https://yourdomain.com/admin-login.html`
- Dashboard: `https://yourdomain.com/admin-dashboard.html`

## 🔒 Security Checklist

After installation:
- [ ] Delete `/setup` folder
- [ ] Change default admin password
- [ ] Update `JWT_SECRET` in config
- [ ] Set proper folder permissions (755 for folders, 644 for files)
- [ ] Enable SSL certificate (free with Let's Encrypt)
- [ ] Setup regular database backups

## 📱 Test Your Installation

1. **Test website:**
   - Visit homepage
   - Check all pages load
   - Verify images display

2. **Test admin:**
   - Login to admin panel
   - Add a test story
   - Upload an image
   - Verify it appears on blog.html

3. **Test API:**
   - Check browser console for errors
   - Verify dynamic content loads
   - Test edit and delete functions

## 🆘 Common Issues

### Issue: "Database connection failed"
**Fix:** Check database credentials in `config/config.php`

### Issue: "500 Internal Server Error"
**Fix:** Check `.htaccess` file exists and PHP version is 7.4+

### Issue: "Images won't upload"
**Fix:** Set `uploads/` folder permissions to 755

### Issue: "Can't login"
**Fix:** Verify admin credentials in database or re-run installer

## 📞 Need Help?

**Documentation:**
- [README.md](README.md) - Overview
- [CPANEL-DEPLOYMENT.md](CPANEL-DEPLOYMENT.md) - Full deployment guide
- [START-HERE.md](START-HERE.md) - Quick start

**Support:**
- Email: info@bayanfoundation.org

## 🎊 You're Ready!

Your PHP-based CMS is ready for cPanel deployment!

**Next Steps:**
1. Read [CPANEL-DEPLOYMENT.md](CPANEL-DEPLOYMENT.md) for detailed instructions
2. Upload to your cPanel hosting
3. Run the web installer
4. Start managing your content!

---

## 📋 Deployment Checklist

- [ ] Files uploaded to cPanel
- [ ] MySQL database created
- [ ] Database user created with privileges
- [ ] Installer run successfully
- [ ] Setup folder deleted
- [ ] Admin login works
- [ ] Test story added
- [ ] Image upload tested
- [ ] Content appears on website
- [ ] SSL certificate enabled
- [ ] Permissions set correctly
- [ ] Backups configured

---

**🎉 Congratulations! Your site is now powered by PHP and ready for cPanel hosting!**

*Bayan Children's Foundation - Empowering Communities Through Education and Support*

