# Setup & Installation Guide

Complete step-by-step guide to set up and configure The Mess Hub locally or on a production server.

## 📋 Prerequisites

Verify you have the following installed:

### System Requirements

- **PHP**: 8.0 or higher
  ```bash
  php --version
  ```
- **MySQL**: 5.7 or higher
  ```bash
  mysql --version
  ```
- **Web Server**: Apache with `mod_rewrite` or Nginx (optional for development)

### PHP Extensions

Ensure these extensions are enabled:

- `pdo` - Database abstraction
- `pdo_mysql` - MySQL driver for PDO
- `session` - Session handling
- `filter` - Input filtering

Check enabled extensions:

```bash
php -m | grep -E 'pdo|session|filter'
```

## 🚀 Installation Steps

### Step 1: Obtain the Project

**Option A: Clone from repository**

```bash
git clone https://github.com/arghobhatt/themesshub.git themesshub
cd themesshub
```

**Option B: Extract from archive**

```bash
unzip themesshub.zip
cd themesshub
```

### Step 2: Create Environment Configuration

**Copy the example environment file:**

```bash
cp .env.example .env
```

**Edit `.env` with your database credentials:**

```bash
nano .env
```

**Content of `.env`:**

```env
# Database Connection
DB_HOST=127.0.0.1
DB_NAME=mess_hub
DB_USER=root
DB_PASS=your_password
DB_CHARSET=utf8mb4
```

**Note**: For production, use environment-specific values and never commit `.env` to version control.

### Step 3: Create Database

**Option A: Using MySQL CLI**

```bash
mysql -u root -p < schema.sql
```

**Option B: Using MySQL GUI (phpMyAdmin)**

1. Open phpMyAdmin
2. Click "SQL" tab
3. Open `schema.sql` file
4. Execute

**Verify database creation:**

```bash
mysql -u root -p -e "SHOW DATABASES LIKE 'mess_hub';"
```

### Step 4: Verify File Permissions

```bash
# Ensure web server can read project files
chmod 755 .
chmod -R 755 app/
chmod -R 755 config/
chmod -R 755 core/

# If you need write permissions for logs (future feature)
chmod 755 storage/ 2>/dev/null || true
```

### Step 5: Start Development Server

**Built-in PHP server (for development):**

```bash
cd /path/to/themesshub
php -S localhost:8000
```

Access: http://localhost:8000

**Note**: The built-in server is suitable only for development and testing, not production.

### Step 6: Verify Installation

1. **Navigate to home page**
   - URL: http://localhost:8000
   - Expected: The Mess Hub homepage displays

2. **Test registration**
   - Click "Register"
   - Create a test account
   - Verify email is accepted

3. **Test login**
   - Log in with test credentials
   - Verify dashboard displays

4. **Check database**
   ```bash
   mysql -u root -p mess_hub -e "SELECT COUNT(*) as users FROM users;"
   ```

## 🐍 Production Deployment

### Pre-Deployment Checklist

- [ ] Database backups configured
- [ ] `.env` file contains production credentials
- [ ] `DB_HOST` is correct (not localhost if on different server)
- [ ] SSL/HTTPS configured
- [ ] PHP display_errors disabled
- [ ] Error logging configured
- [ ] Web server properly configured

### Web Server Configuration

**Apache (.htaccess for URL rewriting)**

Already included at project root or place in `public/`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
</IfModule>
```

**Nginx Configuration**

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/themesshub;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Environment Setup

**Production `.env` example:**

```env
DB_HOST=db.example.com
DB_NAME=mess_hub_prod
DB_USER=messdb_user
DB_PASS=strong_secure_password
DB_CHARSET=utf8mb4
```

### Database Setup on Remote Server

```bash
# Transfer schema file
scp schema.sql user@server:/tmp/

# Execute on remote server
ssh user@server mysql -u root -p < /tmp/schema.sql
```

## 🔧 Configuration Details

### Database Configuration (`config/database.php`)

The configuration file automatically loads `.env`:

```php
$host    = env('DB_HOST', '127.0.0.1');
$dbname  = env('DB_NAME', 'mess_hub');
$user    = env('DB_USER', 'root');
$pass    = env('DB_PASS', '');
$charset = env('DB_CHARSET', 'utf8mb4');
```

### Session Configuration (`index.php`)

Sessions are automatically configured with:

- HttpOnly cookies (prevents XSS attacks)
- SameSite=Lax (prevents CSRF attacks)
- Secure flag (HTTPS only in production)

### Database Connection String

```php
$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
```

## 🐛 Troubleshooting

### Database Connection Error

**Error**: `SQLSTATE[HY000]: General error: 1030 Got error...`

**Solution**:

1. Verify database credentials in `.env`
2. Check MySQL is running: `mysql -u root -p -e "SELECT 1;"`
3. Verify database exists: `mysql -u root -p -e "SHOW DATABASES;"`

### PDO Extension Not Loaded

**Error**: `Call to undefined class PDO`

**Solution**:

```bash
# Ubuntu/Debian
sudo apt-get install php-pdo php-mysql

# macOS (Homebrew)
brew install php php-mysql

# Verify
php -m | grep pdo
```

### Permission Denied Errors

**Error**: `Warning: fopen(/path/to/file): failed to open stream`

**Solution**:

```bash
sudo chown -R www-data:www-data /var/www/themesshub
chmod -R 755 /var/www/themesshub
```

### File Not Found (404 on All Routes)

**Cause**: URL rewriting not working

**Apache solution**:

1. Enable mod_rewrite: `sudo a2enmod rewrite`
2. Restart Apache: `sudo systemctl restart apache2`

**Nginx solution**:

- Check nginx config includes `try_files` rule
- Reload nginx: `sudo systemctl reload nginx`

### Session Not Persisting

**Cause**: Session directory not writable

**Solution**:

```bash
sudo chown -R www-data:www-data /var/lib/php/sessions
chmod -R 755 /var/lib/php/sessions
```

## 📊 Database Initialization

### Reset Database

To reset the database to initial state:

```bash
# Drop and recreate
mysql -u root -p -e "DROP DATABASE IF EXISTS mess_hub;"
mysql -u root -p < schema.sql
```

### Seed with Test Data

```sql
-- Insert test user
INSERT INTO users (role_id, full_name, email, password_hash, status)
VALUES (2, 'Test Member', 'test@example.com', PASSWORD('password'), 'active');

-- Insert test mess
INSERT INTO messes (name, location, created_by, is_active)
VALUES ('Test Mess', '123 Main St', 1, 1);

-- Add membership
INSERT INTO mess_memberships (mess_id, user_id, role_in_mess, joined_on)
VALUES (1, 1, 'member', CURDATE());
```

## 🔐 Security Hardening

### PHP Configuration (`php.ini`)

For production, review these settings:

```ini
# Disable dangerous functions
disable_functions = exec, passthru, shell_exec, system
expose_php = Off
display_errors = Off
error_reporting = E_ALL
log_errors = On
error_log = /var/log/php-errors.log

# Session security
session.use_strict_mode = On
session.use_only_cookies = On
session.cookie_httponly = On
session.cookie_secure = On
session.cookie_samesite = Lax
```

### MySQL User Privileges

Create restricted database user:

```sql
CREATE USER 'messdb_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON mess_hub.* TO 'messdb_user'@'localhost';
FLUSH PRIVILEGES;
```

### HTTPS/SSL Setup

For production (using Let's Encrypt with Certbot):

```bash
sudo certbot certonly --webroot -w /var/www/yourdomain -d yourdomain.com
```

## 📈 Performance Optimization

### Database Indexes

The schema includes optimized indexes:

- User roles lookup
- Mess by creator
- Membership lookups
- Expense and deposit date-range queries

### Query Optimization

All queries use:

- Prepared statements (prevents SQL injection)
- Bound parameters (1 ms overhead, major security benefit)
- Indexed columns for WHERE clauses
- LIMIT clauses for large result sets

### Caching Considerations

For future optimization:

- Cache meal rates (recalculate daily)
- Cache member balances (recalculate on deposit/expense)
- Use Redis for session storage in high-traffic scenarios

## 🚨 Monitoring & Maintenance

### Regular Backups

```bash
# Daily backup
mysqldump -u root -p mess_hub > ~/backups/mess_hub_$(date +%Y%m%d).sql

# Schedule with cron
0 2 * * * mysqldump -u root -pPASSWORD mess_hub > ~/backups/mess_hub_$(date +\%Y\%m\%d).sql
```

### Log Monitoring

```bash
# Check PHP error log
tail -f /var/log/php-errors.log

# Check MySQL queries (slow query log)
tail -f /var/log/mysql/slow.log
```

### Database Optimization

Monthly maintenance:

```sql
-- Repair tables
REPAIR TABLE users, messes, expenses, meal_entries, deposits;

-- Optimize tables
OPTIMIZE TABLE users, messes, expenses, meal_entries, deposits;

-- Update statistics
ANALYZE TABLE users, messes, expenses, meal_entries, deposits;
```

## ✅ Verification Checklist

After setup, verify:

- [ ] Application loads without errors
- [ ] Can register new user
- [ ] Can login with credentials
- [ ] Dashboard displays correctly
- [ ] Database populated with test data
- [ ] All 3 roles available (Seeker, Member, Manager)
- [ ] CSS and assets loading (Tailwind styling visible)
- [ ] Forms have CSRF token fields
- [ ] Session persists across pages
- [ ] Logout clears session

## 📞 Support

For setup issues:

1. Check error logs: `php -S localhost:8000` shows errors in console
2. Verify all prerequisites installed
3. Ensure database connectivity
4. Review troubleshooting section above

---

**Last Updated**: April 2026  
**Version**: 1.0.0
