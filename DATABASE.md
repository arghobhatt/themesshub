# Database Schema & Architecture

Complete documentation of The Mess Hub database structure, relationships, and design decisions.

## 🏗️ Database Overview

- **Database Name**: `mess_hub`
- **Character Set**: `utf8mb4` (full UTF-8 support)
- **Collation**: `utf8mb4_unicode_ci` (Unicode Case-Insensitive)
- **Engine**: InnoDB (transactions, foreign keys)
- **Tables**: 9 core tables with 8+ relationships

## 📊 Entity Relationship Diagram

```
roles (1)
   ↓
users (N)
   ├→ mess_memberships (N)
   │   └→ messes (1)
   ├→ messes (creator) (N:1)
   ├→ expenses (N)
   ├→ meal_entries (N)
   ├→ deposits (N)
   └→ join_requests (N)
       └→ messes (1)

messes (1)
   ├→ mess_memberships (N)
   │   └→ users (1)
   ├→ expenses (N)
   ├→ meal_entries (N)
   ├→ deposits (N)
   ├→ join_requests (N)
   └→ meal_rates (1)
```

## 📑 Table Specifications

### 1. `roles` - User Role Definitions

Defines application roles and their permissions.

```sql
CREATE TABLE roles (
  id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(20) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_roles_name (name)
) ENGINE=InnoDB;
```

**Columns**:
| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `id` | TINYINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique role identifier |
| `name` | VARCHAR(20) | UNIQUE NOT NULL | Role name (seeker, member, manager) |

**Predefined Roles**:
1. **Seeker** - Browse messes, request to join
2. **Member** - Log meals, track balance
3. **Manager** - Manage mess, expenses, members

**Indexes**:
- Primary Key: `id`
- Unique: `name`

---

### 2. `users` - User Accounts & Authentication

Core user table storing authentication and profile information.

```sql
CREATE TABLE users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  role_id TINYINT UNSIGNED NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(30) NULL,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_users_email (email),
  KEY idx_users_role (role_id),
  CONSTRAINT fk_users_role
    FOREIGN KEY (role_id) REFERENCES roles (id)
) ENGINE=InnoDB;
```

**Columns**:
| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY | Unique user identifier |
| `role_id` | TINYINT UNSIGNED | FK → roles | User's role |
| `full_name` | VARCHAR(100) | NOT NULL | User's display name |
| `email` | VARCHAR(190) | UNIQUE, NOT NULL | Login email (case-insensitive) |
| `phone` | VARCHAR(30) | NULLABLE | Contact phone number |
| `password_hash` | VARCHAR(255) | NOT NULL | Bcrypt hashed password |
| `status` | ENUM | NOT NULL, DEFAULT 'active' | Account status |
| `created_at` | DATETIME | AUTO TIMESTAMP | Account creation time |
| `updated_at` | DATETIME | AUTO TIMESTAMP | Last update time |

**Indexes**:
- Primary Key: `id`
- Unique: `email` (case-insensitive lookup)
- Foreign Key: `role_id` → `roles.id`

**Security Notes**:
- Email converted to lowercase for uniqueness
- Passwords hashed with PHP `PASSWORD_DEFAULT` (currently Bcrypt)
- No plaintext passwords stored
- Status field enables soft deactivation

---

### 3. `messes` - Shared Housing Groups

Represents individual messes (shared housing units).

```sql
CREATE TABLE messes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  location VARCHAR(150) NOT NULL,
  description TEXT NULL,
  rent DECIMAL(10,2) NULL,
  image_url VARCHAR(255) NULL,
  contact_info VARCHAR(255) NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  started_on DATE NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_messes_creator (created_by),
  CONSTRAINT fk_messes_creator
    FOREIGN KEY (created_by) REFERENCES users (id)
) ENGINE=InnoDB;
```

**Columns**:
| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY | Unique mess identifier |
| `name` | VARCHAR(120) | NOT NULL | Mess name (e.g., "Home Sweet Home") |
| `location` | VARCHAR(150) | NOT NULL | Physical address |
| `description` | TEXT | NULLABLE | Additional details |
| `rent` | DECIMAL(10,2) | NULLABLE | Monthly rent amount |
| `image_url` | VARCHAR(255) | NULLABLE | Cover image for mess |
| `contact_info` | VARCHAR(255) | NULLABLE | Manager contact info |
| `created_by` | BIGINT UNSIGNED | FK → users (mgr) | Creator/manager |
| `started_on` | DATE | NULLABLE | Operational start date |
| `is_active` | TINYINT(1) | NOT NULL, DEFAULT 1 | Active/inactive flag |
| `created_at` | DATETIME | AUTO TIMESTAMP | Creation timestamp |

**Indexes**:
- Primary Key: `id`
- Foreign Key: `created_by` → `users.id`

**Relationships**:
- **One-to-Many**: mess → multiple memberships
- **One-to-Many**: mess → multiple expenses
- **One-to-Many**: mess → multiple meal entries
- **One-to-Many**: mess → multiple deposits

---

### 4. `mess_memberships` - User-Mess Associations

Junction table linking users to messes with role tracking.

```sql
CREATE TABLE mess_memberships (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mess_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  role_in_mess ENUM('member', 'manager') NOT NULL DEFAULT 'member',
  joined_on DATE NOT NULL,
  left_on DATE NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_membership (mess_id, user_id),
  KEY idx_membership_user (user_id),
  CONSTRAINT fk_membership_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id),
  CONSTRAINT fk_membership_user
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB;
```

**Columns**:
| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY | Record identifier |
| `mess_id` | BIGINT UNSIGNED | FK, UNIQUE(pair) | Associated mess |
| `user_id` | BIGINT UNSIGNED | FK, UNIQUE(pair) | Associated user |
| `role_in_mess` | ENUM('member','manager') | NOT NULL | Role within mess |
| `joined_on` | DATE | NOT NULL | Membership start date |
| `left_on` | DATE | NULLABLE | Membership end date |
| `status` | ENUM | NOT NULL, DEFAULT 'active' | Membership status |
| `created_at` | DATETIME | AUTO TIMESTAMP | Creation timestamp |

**Indexes**:
- Primary Key: `id`
- Unique Composite: `(mess_id, user_id)` - prevents duplicate memberships
- Foreign Keys: `mess_id`, `user_id`

**Key Features**:
- Unique constraint prevents user joining mess twice
- Tracks role changes (member ↔ manager)
- Soft deletion via `left_on` date
- Historical tracking of all memberships

---

### 5. `join_requests` - Membership Join Requests

Request queue for users seeking to join messes.

```sql
CREATE TABLE join_requests (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mess_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  message VARCHAR(255) NULL,
  decided_by BIGINT UNSIGNED NULL,
  decided_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_join_request (mess_id, user_id),
  KEY idx_join_status (status),
  CONSTRAINT fk_join_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id),
  CONSTRAINT fk_join_user
    FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_join_decider
    FOREIGN KEY (decided_by) REFERENCES users (id)
) ENGINE=InnoDB;
```

**Columns**:
| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY | Record identifier |
| `mess_id` | BIGINT UNSIGNED | FK, UNIQUE(pair) | Target mess |
| `user_id` | BIGINT UNSIGNED | FK, UNIQUE(pair) | Requesting user |
| `status` | ENUM | NOT NULL, DEFAULT 'pending' | Request status |
| `message` | VARCHAR(255) | NULLABLE | User's joining message |
| `decided_by` | BIGINT UNSIGNED | FK (optional) | Manager who decided |
| `decided_at` | DATETIME | NULLABLE | Decision timestamp |
| `created_at` | DATETIME | AUTO TIMESTAMP | Request timestamp |

**Indexes**:
- Primary Key: `id`
- Unique Composite: `(mess_id, user_id)`
- Status Index: `status` (fast filtering)
- Foreign Keys: `mess_id`, `user_id`, `decided_by`

**Workflow**:
1. User creates request (status='pending')
2. Manager approves/rejects (updates status, decided_by, decided_at)
3. If approved, creates corresponding `mess_memberships` record

---

### 6. `expenses` - Daily Bazaar Expenses

Records of common expenses split among mess members.

```sql
CREATE TABLE expenses (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mess_id BIGINT UNSIGNED NOT NULL,
  purchaser_id BIGINT UNSIGNED NOT NULL,
  expense_date DATE NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  vendor VARCHAR(120) NULL,
  notes VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_expense_mess_date (mess_id, expense_date),
  KEY idx_expense_user (purchaser_id),
  CONSTRAINT fk_expense_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id),
  CONSTRAINT fk_expense_user
    FOREIGN KEY (purchaser_id) REFERENCES users (id)
) ENGINE=InnoDB;
```

**Columns**:
| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY | Record identifier |
| `mess_id` | BIGINT UNSIGNED | FK | Associated mess |
| `purchaser_id` | BIGINT UNSIGNED | FK | Member who paid |
| `expense_date` | DATE | NOT NULL | Transaction date |
| `amount` | DECIMAL(12,2) | NOT NULL | Expense amount |
| `vendor` | VARCHAR(120) | NULLABLE | Store/vendor name |
| `notes` | VARCHAR(255) | NULLABLE | Additional details |
| `created_at` | DATETIME | AUTO TIMESTAMP | Record creation |

**Indexes**:
- Primary Key: `id`
- Composite: `(mess_id, expense_date)` - date range queries
- Foreign Keys: `mess_id`, `purchaser_id`

**Precision**: DECIMAL(12,2) supports amounts up to $9,999,999.99

---

### 7. `meal_entries` - Meal Usage Tracking

Day-by-day meal consumption records per user per mess.

```sql
CREATE TABLE meal_entries (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mess_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  meal_date DATE NOT NULL,
  meals_count DECIMAL(4,2) NOT NULL DEFAULT 1.00,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_meal_user_day (mess_id, user_id, meal_date),
  KEY idx_meal_mess_date (mess_id, meal_date),
  KEY idx_meal_user (user_id),
  CONSTRAINT fk_meal_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id),
  CONSTRAINT fk_meal_user
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB;
```

**Columns**:
| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY | Record identifier |
| `mess_id` | BIGINT UNSIGNED | FK, UNIQUE(triplet) | Associated mess |
| `user_id` | BIGINT UNSIGNED | FK, UNIQUE(triplet) | Member consuming |
| `meal_date` | DATE | UNIQUE(triplet) | Date of meal |
| `meals_count` | DECIMAL(4,2) | NOT NULL, DEFAULT 1.00 | # of meals (0.5-99.99) |
| `created_at` | DATETIME | AUTO TIMESTAMP | Record creation |

**Indexes**:
- Primary Key: `id`
- Unique Composite: `(mess_id, user_id, meal_date)` - prevents duplicates
- Composite: `(mess_id, meal_date)` - period queries
- Foreign Keys: `mess_id`, `user_id`

**Features**:
- Supports fractional meals (0.5 = half meal)
- Prevents duplicate entry for same day
- DECIMAL(4,2) allows values 0.00 - 99.99

---

### 8. `deposits` - Member Contributions

Records of money contributed by members toward expenses.

```sql
CREATE TABLE deposits (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mess_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  deposited_on DATE NOT NULL,
  method VARCHAR(50) NULL,
  reference VARCHAR(100) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_deposit_mess_date (mess_id, deposited_on),
  KEY idx_deposit_user (user_id),
  CONSTRAINT fk_deposit_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id),
  CONSTRAINT fk_deposit_user
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB;
```

**Columns**:
| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY | Record identifier |
| `mess_id` | BIGINT UNSIGNED | FK | Associated mess |
| `user_id` | BIGINT UNSIGNED | FK | Contributing member |
| `amount` | DECIMAL(12,2) | NOT NULL | Contribution amount |
| `deposited_on` | DATE | NOT NULL | Deposit date |
| `method` | VARCHAR(50) | NULLABLE | Payment method (cash, online, etc.) |
| `reference` | VARCHAR(100) | NULLABLE | Transaction reference (UTR, etc.) |
| `created_at` | DATETIME | AUTO TIMESTAMP | Record creation |

**Indexes**:
- Primary Key: `id`
- Composite: `(mess_id, deposited_on)` - date range queries
- Foreign Keys: `mess_id`, `user_id`

---

### 9. `meal_rates` - Calculated Meal Rates

Pre-calculated meal rates for accounting periods (historical, for reporting).

```sql
CREATE TABLE meal_rates (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mess_id BIGINT UNSIGNED NOT NULL,
  period_start DATE NOT NULL,
  period_end DATE NOT NULL,
  total_expense DECIMAL(12,2) NOT NULL,
  total_meals DECIMAL(10,2) NOT NULL,
  rate_per_meal DECIMAL(10,4) NOT NULL,
  calculated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_rate_period (mess_id, period_start, period_end),
  KEY idx_rate_period (mess_id, period_start, period_end),
  CONSTRAINT fk_rate_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id)
) ENGINE=InnoDB;
```

**Columns**:
| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY | Record identifier |
| `mess_id` | BIGINT UNSIGNED | FK, UNIQUE(pair+dates) | Associated mess |
| `period_start` | DATE | UNIQUE(triplet) | Calculation period start |
| `period_end` | DATE | UNIQUE(triplet) | Calculation period end |
| `total_expense` | DECIMAL(12,2) | NOT NULL | Sum of all expenses |
| `total_meals` | DECIMAL(10,2) | NOT NULL | Sum of all meals |
| `rate_per_meal` | DECIMAL(10,4) | NOT NULL | Calculated rate (per meal) |
| `calculated_at` | DATETIME | AUTO TIMESTAMP | Calculation time |

**Calculation Formula**:
```
rate_per_meal = total_expense / total_meals
```

**Precision**: DECIMAL(10,4) supports rates up to $9999.9999 per meal

---

## 🔗 Relationships Summary

### One-to-Many (1:N)

| Parent | Child | Foreign Key | Purpose |
|--------|-------|------------|---------|
| `roles` | `users` | `role_id` | Users belong to role |
| `messes` | `mess_memberships` | `mess_id` | Mess has members |
| `messes` | `expenses` | `mess_id` | Mess has expenses |
| `messes` | `meal_entries` | `mess_id` | Mess has meal logs |
| `messes` | `deposits` | `mess_id` | Mess has deposits |
| `messes` | `join_requests` | `mess_id` | Mess has requests |
| `messes` | `meal_rates` | `mess_id` | Mess has rate history |
| `users` | `mess_memberships` | `user_id` | User in messes |
| `users` | `expenses` | `purchaser_id` | User makes expenses |
| `users` | `meal_entries` | `user_id` | User logs meals |
| `users` | `deposits` | `user_id` | User makes deposits |
| `users` | `join_requests` | `user_id` | User requests join |
| `users` | `messes` | `created_by` | User manages mess |

### Many-to-Many (N:N)

**Via `mess_memberships`**:
- Users ↔ Messes (with role tracking)

---

## 🔍 Query Performance

### Optimized Indexes

```sql
-- Fast user lookups by email
UNIQUE KEY uniq_users_email (email)

-- Fast expense date range queries
KEY idx_expense_mess_date (mess_id, expense_date)

-- Fast meal history queries
KEY idx_meal_mess_date (mess_id, meal_date)
UNIQUE KEY uniq_meal_user_day (mess_id, user_id, meal_date)

-- Fast filtering by join request status
KEY idx_join_status (status)
```

### Example Query Plans

**Get all expenses for a mess in a date range:**
```sql
SELECT * FROM expenses 
WHERE mess_id = 1 AND expense_date BETWEEN '2026-01-01' AND '2026-03-31'
ORDER BY expense_date DESC;
-- Uses: idx_expense_mess_date (mess_id, expense_date)
```

**Calculate meal rate for period:**
```sql
SELECT 
  SUM(amount) as total_expense,
  SUM(meals_count) as total_meals,
  SUM(amount) / SUM(meals_count) as rate
FROM (
  SELECT amount FROM expenses WHERE mess_id = ? 
    AND expense_date BETWEEN ? AND ?
) AS e,
(
  SELECT meals_count FROM meal_entries WHERE mess_id = ?
    AND meal_date BETWEEN ? AND ?
) AS m;
-- Uses both date indexes
```

---

## 🔐 Data Integrity

### Constraints & Validations

- **Primary Keys**: Ensure unique records
- **Foreign Keys**: Maintain referential integrity
- **Unique Constraints**: Prevent duplicates (email, memberships)
- **CHECK Constraints** (implicit): ENUM defines valid values
- **DEFAULT Values**: Consistent initialization

### Cascading

Current setup uses restrictive foreign keys:
- Deleting user hard-fails if referenced
- Data preservation approach (soft delete instead)

---

## 📈 Scaling Considerations

### For Growth

**Current Limits**:
- Supports ~9 billion records per table (BIGINT UNSIGNED)
- Supports decimal precision to $9,999,999.99

**Optimization for 100K+ users**:
1. Add partitioning on `expenses` and `meal_entries` by `mess_id`
2. Archive old data (>1 year) to separate tables
3. Create materialized views for meal rates
4. Implement read replicas for reporting queries

---

## 📋 Backup & Recovery

### Full Database Backup

```bash
mysqldump -u root -p mess_hub > backup.sql
```

### Restore from Backup

```bash
mysql -u root -p mess_hub < backup.sql
```

### Table-specific Operations

```sql
-- Repair corruption
REPAIR TABLE expenses;

-- Optimize storage
OPTIMIZE TABLE expenses;

-- Update statistics
ANALYZE TABLE expenses;
```

---

**Last Updated**: April 2026  
**Version**: 1.0.0
