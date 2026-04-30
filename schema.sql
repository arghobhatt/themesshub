
DROP DATABASE IF EXISTS mess_hub;
CREATE DATABASE mess_hub
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mess_hub;

CREATE TABLE roles (
  id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(20) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_roles_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  role_id TINYINT UNSIGNED NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(30) NULL,
  meal_plan ENUM('standard', 'vegetarian', 'custom') NOT NULL DEFAULT 'standard',
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_users_email (email),
  KEY idx_users_role (role_id),
  CONSTRAINT fk_users_role
    FOREIGN KEY (role_id) REFERENCES roles (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE messes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  location VARCHAR(150) NOT NULL,
  description TEXT NULL,
  rent DECIMAL(10,2) NULL,
  image VARCHAR(255) NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  started_on DATE NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_messes_creator (created_by),
  CONSTRAINT fk_messes_creator
    FOREIGN KEY (created_by) REFERENCES users (id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY idx_membership_mess_status (mess_id, status),
  CONSTRAINT fk_membership_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_membership_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    FOREIGN KEY (mess_id) REFERENCES messes (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_join_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_join_decider
    FOREIGN KEY (decided_by) REFERENCES users (id)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE expenses (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mess_id BIGINT UNSIGNED NOT NULL,
  purchaser_id BIGINT UNSIGNED NOT NULL,
  expense_date DATE NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  vendor VARCHAR(120) NULL,
  notes VARCHAR(255) NULL,
  category VARCHAR(50) NULL,
  payment_method VARCHAR(50) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_expense_mess_date (mess_id, expense_date),
  KEY idx_expense_user (purchaser_id),
  KEY idx_expense_category (category),
  CONSTRAINT fk_expense_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_expense_user
    FOREIGN KEY (purchaser_id) REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE meal_entries (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mess_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  meal_date DATE NOT NULL,
  meals_count DECIMAL(4,2) NOT NULL DEFAULT 1.00,
  has_breakfast TINYINT(1) NOT NULL DEFAULT 0,
  has_lunch TINYINT(1) NOT NULL DEFAULT 0,
  has_dinner TINYINT(1) NOT NULL DEFAULT 0,
  guest_meals DECIMAL(4,2) NOT NULL DEFAULT 0.00,
  notes VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_meal_user_day (mess_id, user_id, meal_date),
  KEY idx_meal_mess_date (mess_id, meal_date),
  KEY idx_meal_user (user_id),
  CONSTRAINT fk_meal_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_meal_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE deposits (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mess_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  deposited_on DATE NOT NULL,
  method VARCHAR(50) NULL,
  reference VARCHAR(100) NULL,
  payment_method VARCHAR(50) NULL,
  transaction_id VARCHAR(100) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_deposit_mess_date (mess_id, deposited_on),
  KEY idx_deposit_user (user_id),
  CONSTRAINT fk_deposit_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_deposit_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE attendance (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  mess_id BIGINT UNSIGNED NOT NULL,
  attendance_date DATE NOT NULL,
  is_present TINYINT(1) NOT NULL DEFAULT 0,
  notes VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_attendance (mess_id, user_id, attendance_date),
  KEY idx_attendance_mess_date (mess_id, attendance_date),
  KEY idx_attendance_user (user_id),
  CONSTRAINT fk_attendance_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_attendance_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE weekly_menu (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mess_id BIGINT UNSIGNED NOT NULL,
  day_of_week TINYINT UNSIGNED NOT NULL,
  breakfast TEXT NULL,
  lunch TEXT NULL,
  dinner TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_weekly_menu (mess_id, day_of_week),
  KEY idx_weekly_menu_mess_day (mess_id, day_of_week),
  CONSTRAINT fk_weekly_menu_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT chk_weekly_menu_day
    CHECK (day_of_week BETWEEN 0 AND 6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notices (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mess_id BIGINT UNSIGNED NOT NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  title VARCHAR(150) NOT NULL,
  content TEXT NOT NULL,
  priority ENUM('normal', 'high') NOT NULL DEFAULT 'normal',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_notices_mess_created (mess_id, created_at),
  KEY idx_notices_priority (priority),
  CONSTRAINT fk_notices_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_notices_creator
    FOREIGN KEY (created_by) REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE feedback (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  mess_id BIGINT UNSIGNED NOT NULL,
  rating TINYINT UNSIGNED NOT NULL,
  category VARCHAR(50) NOT NULL,
  comment TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_feedback_mess_created (mess_id, created_at),
  KEY idx_feedback_user (user_id),
  KEY idx_feedback_category (category),
  CONSTRAINT fk_feedback_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_feedback_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT chk_feedback_rating
    CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notice_comments (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  notice_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_comments_notice_created (notice_id, created_at),
  KEY idx_comments_user (user_id),
  CONSTRAINT fk_comments_notice
    FOREIGN KEY (notice_id) REFERENCES notices (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_comments_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE complaints (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  mess_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  status ENUM('open', 'investigating', 'resolved', 'closed') NOT NULL DEFAULT 'open',
  priority ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_complaints_mess_status (mess_id, status),
  KEY idx_complaints_user (user_id),
  KEY idx_complaints_priority (priority),
  CONSTRAINT fk_complaints_user
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_complaints_mess
    FOREIGN KEY (mess_id) REFERENCES messes (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles (name) VALUES
  ('seeker'),
  ('member'),
  ('manager');
