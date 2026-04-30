# The Mess Hub

A comprehensive shared housing and finance management platform designed to simplify meal tracking, expense management, and financial settlements for residential messes and shared accommodations.

## 🎯 Overview

The Mess Hub is a modern web application built with **PHP**, **MySQL**, and **Tailwind CSS** that helps manage shared housing arrangements. It provides tools for meal logging, expense tracking, deposit management, and automatic financial calculations to ensure fair and transparent settlements among members.

### Key Statistics
- **Database**: MySQL with 9 core tables, 8+ relationships
- **Architecture**: MVC pattern with middleware support
- **Security**: Prepared statements, CSRF protection, session-based auth
- **Frontend**: Responsive design with Tailwind CSS
- **Performance**: Indexed queries for fast data retrieval

## ✨ Features

### 👥 User Management
- **Three-tier role system**: Seeker, Member, Manager
- Registration and secure authentication
- Profile management with password management
- Session-based authorization

### 🏠 Mess Management
- Create and manage multiple messes
- Invite members and manage join requests
- Track mess details (name, location, rent)

### 🍽️ Meal Tracking
- Log daily meal entries per member per mess
- Prevent duplicate entries for same date
- Track meal counts (supports fractional meals)
- Recent meal history view

### 💰 Finance Management
- **Expense Tracking**: Record daily bazaar expenses
- **Deposit Management**: Track member contributions
- **Automatic Calculations**: Real-time meal rate computation
- **Balance Sheets**: Member-wise financial summaries
- **Transaction History**: Complete audit trail

### 📊 Dashboards
- **Member Dashboard**: Personal balance, meal history, deposits
- **Manager Dashboard**: Expense overview, pending requests, member summaries
- **Seeker Dashboard**: Browse available messes, view profiles, track join request status

### 🔐 Security
- Prepared SQL statements (SQL injection protection)
- CSRF tokens on all forms
- Password hashing with PHP's `PASSWORD_DEFAULT`
- HttpOnly and SameSite cookies
- Middleware-based access control

## 🚀 Quick Start

### Prerequisites
- PHP 8.0+
- MySQL 5.7+
- Composer (optional, for future dependencies)

### Installation
1. **Clone or extract the project**
   ```bash
   cd bhattWEB
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials
   ```

3. **Create database**
   ```bash
   mysql -u root -p < schema.sql
   ```

4. **Start development server**
   ```bash
   php -S localhost:8001
   ```

5. **Access the application**
   - Open http://localhost:8001 in your browser
   - Default roles available: Seeker, Member, Manager

For detailed setup instructions, see [SETUP.md](SETUP.md).

## 📚 Documentation

- **[SETUP.md](SETUP.md)** - Detailed installation and configuration guide
- **[DATABASE.md](DATABASE.md)** - Complete database schema and relationships
- **[FEATURES.md](FEATURES.md)** - In-depth feature documentation
- **[USER_GUIDE.md](USER_GUIDE.md)** - User guide and workflows

## 🏗️ Project Structure

```
bhattWEB/
├── app/
│   ├── Controllers/       # Application logic (8 controllers)
│   ├── Models/           # Database models (7 models)
│   ├── Services/         # Business logic services
│   └── Views/            # HTML templates organized by module
├── core/
│   ├── Controller.php    # Base controller class
│   ├── Router.php        # URL routing engine
│   └── Middleware/       # Authentication & authorization
├── config/
│   └── database.php      # Database configuration
├── schema.sql            # Database initialization script
├── index.php             # Application entry point
└── .env                  # Environment configuration
```

## 🔌 API Endpoints

### Authentication
- `GET /` - Home page
- `GET /register` - Registration form
- `POST /register` - Create account
- `GET /login` - Login form
- `POST /login` - Authenticate
- `POST /logout` - End session

### Dashboard & Profile
- `GET /dashboard` - Role-based dashboard
- `GET /profile` - User profile
- `POST /profile` - Update profile
- `POST /profile/password` - Change password

### Mess Management
- `GET /messes/create` - Create mess form
- `POST /messes` - Store new mess
- `GET /messes/:id/edit` - Edit mess form
- `POST /messes/update` - Update mess

### Meal Tracking
- `GET /meals/create` - Log meal form
- `POST /meals` - Save meal entry

### Finances
- `GET /expenses/create` - Add expense form
- `POST /expenses` - Record expense
- `GET /deposits/create` - Add deposit form
- `POST /deposits` - Record deposit

### Member Management
- `GET /join-requests` - View pending requests
- `POST /join-requests/approve` - Approve request
- `POST /join-requests/reject` - Reject request

## 💾 Database

The system uses **MySQL 5.7+** with these core tables:
- `roles` - User role definitions
- `users` - User accounts and authentication
- `messes` - Shared housing groups
- `mess_memberships` - User-mess relationships
- `join_requests` - Membership requests
- `expenses` - Daily bazaar expenses
- `meal_entries` - Meal tracking logs
- `deposits` - Member contributions
- `meal_rates` - Calculated rates per period

See [DATABASE.md](DATABASE.md) for complete schema documentation.

## 🔒 Security Features

- **SQL Injection Prevention**: Parameterized queries throughout
- **CSRF Protection**: Tokens on all state-changing operations
- **Password Security**: Bcrypt hashing with PHP's PASSWORD_DEFAULT
- **Session Security**: HttpOnly cookies, SameSite protection
- **Access Control**: Role-based middleware for protected routes
- **Input Validation**: Server-side validation on all forms
- **Error Handling**: Proper 404 error pages

## 🛠️ Tech Stack

- **Backend**: PHP 8.0+ (vanilla core, no frameworks)
- **Database**: MySQL 5.7+ (InnoDB)
- **Frontend**: Tailwind CSS 3, HTML5
- **Architecture**: MVC with middleware pattern
- **Fonts**: Space Grotesk (Google Fonts)

## 📋 System Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.0 or higher |
| MySQL | 5.7 or higher |
| Web Server | Apache/Nginx |
| Browser | Modern (Chrome, Firefox, Safari, Edge) |

## 📖 Usage Examples

### For Members
1. Register as a Seeker
2. Browse available messes
3. Request to join a mess
4. Once approved, log daily meals
5. Monitor balance and deposit history
6. Download balance reports

### For Managers
1. Create a mess
2. Invite and manage members
3. Record daily expenses
4. Track member deposits
5. View real-time meal rates
6. Manage pending join requests

## 🤝 Contributing

When contributing to this project:
1. Follow the existing MVC structure
2. Use prepared statements for all database queries
3. Add CSRF tokens to all forms
4. Ensure code is properly typed (PHP 8.0+)
5. Test in multiple browsers

## 📝 License

This project is proprietary. All rights reserved.

## 📧 Support

For issues, questions, or suggestions, please contact the development team.

---

**Last Updated**: April 2026  
**Version**: 1.0.0  
**Status**: Production Ready
