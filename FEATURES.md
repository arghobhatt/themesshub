# Features Documentation

Comprehensive guide to all features in The Mess Hub platform.

## 📑 Table of Contents

1. [User Management](#user-management)
2. [Mess Management](#mess-management)
3. [Meal Tracking](#meal-tracking)
4. [Finance Management](#finance-management)
5. [Dashboards](#dashboards)
6. [Security Features](#security-features)

---

## 👥 User Management

### Registration & Authentication

**Feature**: Multi-role user registration with email verification

**Available Roles**:
1. **Seeker** - Browse available messes and request to join
2. **Member** - Log meals, track balance, manage deposits
3. **Manager** - Create messes, manage members, track finances

**Registration Flow**:
```
User fills form → Email validation → Password hashing → Account created
```

**Security**:
- Email uniqueness enforced (case-insensitive)
- Passwords hashed with PHP PASSWORD_DEFAULT (Bcrypt)
- Minimum password length: 8 characters
- Stored as bcrypt with cost factor 10+

**Endpoints**:
- `GET /register` - Registration form
- `POST /register` - Create account

---

### Login & Session Management

**Session Security**:
- HttpOnly cookies (prevents XSS attacks)
- SameSite=Lax (prevents CSRF attacks)
- Secure flag (HTTPS in production)
- Session timeouts (auto-logout after inactivity)

**Session Data Stored**:
```php
$_SESSION['user_id']   // User ID
$_SESSION['email']     // Email address
$_SESSION['role']      // User role
$_SESSION['name']      // Full name
```

**Endpoints**:
- `GET /login` - Login form
- `POST /login` - Authenticate
- `POST /logout` - End session

---

### Profile Management

**Update Profile**:
- Change full name
- Change email (with uniqueness check)
- Both fields validated server-side

**Change Password**:
- Requires current password verification
- New password must match confirmation
- Minimum 8 characters
- Password updated immediately

**View Profile Information**:
- Current mess memberships
- Managed messes (for managers)
- Account creation date

**Endpoints**:
- `GET /profile` - Profile page
- `POST /profile` - Update name/email
- `POST /profile/password` - Change password

---

## 🏠 Mess Management

### Create Mess

**Input Fields**:
- **Mess Name** (max 120 chars) - Display name
- **Location** (max 150 chars) - Physical address
- **Monthly Rent** (optional) - Rent amount
- **Description** (optional) - Additional details

**Validations**:
- Both name and location required
- Rent must be numeric if provided
- Description length limited

**Features**:
- Auto-assigns creator as manager
- Creates initial membership record
- Generates unique mess ID

**Endpoints**:
- `GET /messes/create` - Create form
- `POST /messes` - Store mess

---

### Edit Mess

**Editable Fields**:
- Mess name
- Location
- Monthly rent
- Description

**Permissions**:
- Only mess managers can edit
- Middleware enforces access control

**Features**:
- Retains original data as fallback
- Success message on update
- Validation on all fields

**Endpoints**:
- `GET /messes/:id/edit` - Edit form
- `POST /messes/update` - Update mess

---

### Mess Discovery (Seeker View)

**Browse Messes**:
- View all active messes
- See mess details (name, location)
- Manager contact information
- Current member count

**Request to Join**:
- Compose optional message
- Submit join request
- Status: pending → approved/rejected

**Endpoints**:
- `GET /messes` - Browse messes
- `POST /messes/:id/request` - Request join

---

## 🍽️ Meal Tracking

### Log Daily Meals

**Input Fields**:
- **Mess Selection** - Which mess
- **Meal Date** - Date of meal
- **Meals Count** - Number of meals (decimal, 0.5-99.99)

**Features**:
- Prevents duplicate entries (same user, same mess, same date)
- Supports fractional meals (0.5 = half meal)
- Pre-fills with today's date

**Validations**:
- User must be active member of selected mess
- Date cannot be in future
- Meals count must be > 0
- Date/meals combination must be unique

**Error Handling**:
- Duplicate entry: "You've already logged meals for this date"
- Non-member: "Not a member of this mess"

**Endpoints**:
- `GET /meals/create` - Log form
- `POST /meals` - Save entry

---

### Meal History

**View Recent Meals**:
- Table of last 50 meal entries
- Sorted by date (newest first)
- Shows: Mess name, location, date, meals

**Filtering Options**:
- By mess
- By date range
- By member (manager view)

**Use Cases**:
- Verify meal logs
- Settle disputes
- Check frequency patterns

**Data Access**:
- Members see their own meals
- Managers see all mess member meals

---

## 💰 Finance Management

### Expense Tracking

**Record Expense**:
- **Mess Selection** - Which mess
- **Purchaser** - Which member paid
- **Date** - Expense date
- **Amount** - Expense amount
- **Vendor** (optional) - Store/merchant name
- **Notes** (optional) - Additional details

**Features**:
- Tracks all daily bazaar expenses
- Multi-purchaser support
- searchable by vendor/date
- Complete audit trail

**Validations**:
- Amount must be > 0
- Date cannot be in future
- Purchaser must be mess member

**Endpoints**:
- `GET /expenses/create` - Expense form
- `POST /expenses` - Record expense

---

### Deposit Management

**Record Deposit**:
- **Mess Selection** - Which mess
- **Member** - Contributing member
- **Date** - Deposit date
- **Amount** - Contribution amount
- **Method** (optional) - Payment method (cash, online, etc.)
- **Reference** (optional) - Transaction reference (UTR, ID, etc.)

**Features**:
- Tracks member contributions
- Multiple payment methods supported
- Reference numbers for tracking

**Validations**:
- Amount must be > 0
- Date cannot be in future
- Member must be mess member

**Use Cases**:
- Record cash contributions
- Track bank transfers (reference: UTR)
- Track online payments (reference: Transaction ID)

**Endpoints**:
- `GET /deposits/create` - Deposit form
- `POST /deposits` - Record deposit

---

### Automatic Meal Rate Calculation

**Formula**:
```
Meal Rate = Total Expenses / Total Meals
```

**Calculation Scope**:
- Per mess
- Per time period (configurable)
- Real-time (recalculated on demand)

**Example**:
```
Total Expenses: $1000
Total Meals: 200
Rate Per Meal: $5.00
```

**Uses**:
- Settle member obligations
- Verify fairness
- Generate reports

**Calculation Features**:
- Handles fraction meals correctly
- Supports custom date ranges
- Cached for performance

---

### Member Balance Sheet

**Balance Calculation**:
```
Balance = (Member Meals × Rate) - Member Deposits

Positive Balance = Member owes money
Negative Balance = Member receives refund
```

**Example**:
```
Member A:
- Meals: 50 × Rate $5.00 = $250
- Deposits: $200
- Balance: $250 - $200 = $50 (owes)

Member B:
- Meals: 40 × Rate $5.00 = $200
- Deposits: $250
- Balance: $200 - $250 = -$50 (receives)
```

**Reports**:
- Dashboard shows real-time balances
- Export capability (future)
- Individual settlement reports

---

## 📊 Dashboards

### Member Dashboard

**Displays**:
- **Meals Logged** - Total meals this period
- **Total Balance** - How much owed/due
- **Deposits Made** - Total contributions
- **Meal Rate** - Current rate per meal
- **Quick Links** - Log meals, make deposit

**Tables**:
1. **Mess Summary** - Per-mess breakdown
2. **Recent Deposits** - Last 10 deposits
3. **Recent Meals** - Last 20 meal entries

**Actions**:
- Log new meal entry
- Record new deposit
- View mess details

---

### Manager Dashboard

**Displays**:
- **Active Members** - Count of members
- **Total Expenses** - Sum of expenses
- **Total Meals Logged** - Sum of meals
- **Current Meal Rate** - Rate per meal
- **Pending Requests** - Join requests awaiting decision
- **Quick Links** - Create mess, record expense/deposit

**Tables**:
1. **Pending Join Requests** - Members awaiting approval
2. **Member Balances** - All members' financial status
3. **Recent Expenses** - Last 10 expenses
4. **Expense Breakdown** - By vendor/category

**Actions**:
- Approve/reject join requests
- Record expenses
- Record deposits
- View member details
- Edit mess info

---

### Seeker Dashboard

**Displays**:
- **Available Messes** - Searchable list
- **Browse Messes** - Detailed view with images
- **My Requests** - Visual status tracking of sent requests (Pending, Approved, Rejected)

**Features**:
- Search by location
- Filter by rent range
- Review manager profiles
- View member testimonials (future)

---

## 🔐 Security Features

### SQL Injection Prevention

**Implementation**: All database queries use prepared statements with bound parameters

**Example**:
```php
// UNSAFE ❌
$query = "SELECT * FROM users WHERE email = '" . $email . "'";

// SAFE ✅
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

**Coverage**:
- 100% of SELECT, INSERT, UPDATE queries
- All user input parameterized
- Type-safe binding (PDO::PARAM_INT, etc.)

---

### CSRF Token Protection

**Implementation**: Synchronizer Token Pattern

**Process**:
1. Generate unique token per session → `$csrf_token`
2. Include token in form: `<input name="csrf_token" value="<?php echo $csrf ?>">`
3. Validate on form submission
4. Reject if token missing or invalid

**Forms Protected**:
- Login form
- Registration form
- Profile updates
- Mess create/edit
- Expense/deposit records
- Join request approval

---

### Password Security

**Hashing Algorithm**: PHP PASSWORD_DEFAULT (currently Bcrypt with cost=10)

**Features**:
- One-way hashing (cannot be reversed)
- Salt automatically generated
- Cost factor future-proofed (can increase)
- Timing-safe comparison

**Example**:
```php
// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Verify password
if (password_verify($password, $hash)) {
    // Login successful
}
```

---

### Session Security

**Protections**:
1. **HttpOnly Flag** - Prevents JavaScript access
2. **SameSite Attribute** - Prevents CSRF cookie transmission
3. **Secure Flag** - HTTPS only in production
4. **Session Fixation** - Regenerate on login
5. **Timeout** - Auto-logout after inactivity

**Configuration**:
```php
session_set_cookie_params([
    'lifetime' => 0,        // Browser closes = logout
    'path' => '/',
    'secure' => $isSecure,  // HTTPS in production
    'httponly' => true,     // JS cannot access
    'samesite' => 'Lax',    // CSRF protection
]);
```

---

### Input Validation

**Server-Side Validation**:
- Email format & uniqueness
- Password length (min 8 chars)
- Numeric fields (amounts, counts)
- Date format & range validation
- String length limits

**Sanitization**:
- HTML output encoding (htmlspecialchars)
- SQL parameter binding
- Type casting

**Example**:
```php
// Validate email
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email format';
}
```

---

### Error Handling

**Production Errors**:
- Display user-friendly error messages
- Never expose system details to users
- Log all errors internally

**404 Handling**:
- Proper 404 page for undefined routes
- No route information disclosure

**Database Errors**:
- PDO exceptions caught
- Generic message to user
- Detailed logs internally

**Endpoints Protected**:
- `/dashboard` - Requires login
- `/profile` - Requires login
- `/expenses/create` - Requires manager role
- `/join-requests` - Requires manager role

---

### Middleware-Based Access Control

**RequireAuth Middleware**:
- Protects: `/dashboard`, `/profile`, `/logout`
- Redirects unauthenticated users to login

**RequireRole Middleware**:
- Protects: Manager-only routes
- Checks: User's role matches requirement
- Denies: Non-managers attempting access

**Implementation**:
```php
// Apply middleware to routes
router->middleware('require-auth')->group(function() {
    router->get('/dashboard', 'DashboardController@show');
    router->get('/profile', 'ProfileController@show');
});
```

---

## 📋 Future Features (Roadmap)

- [ ] Email notifications (join approved, balance alerts)
- [ ] Monthly reports (PDF export)
- [ ] Expense categorization
- [ ] Automatic settlement reminders
- [ ] Multi-language support
- [ ] Mobile app
- [ ] Two-factor authentication
- [ ] Payment gateway integration
- [ ] Bulk expense import
- [ ] Historical data analysis

---

**Last Updated**: April 2026  
**Version**: 1.0.0
