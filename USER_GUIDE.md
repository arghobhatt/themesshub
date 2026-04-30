# User Guide

Step-by-step guide to using The Mess Hub platform for different user roles.

## 📑 Table of Contents

1. [Getting Started](#getting-started)
2. [For Seekers](#for-seekers)
3. [For Members](#for-members)
4. [For Managers](#for-managers)
5. [Common Tasks](#common-tasks)
6. [Troubleshooting](#troubleshooting)

---

## 🚀 Getting Started

### Creating Your Account

**Step 1: Access Registration Page**
- Navigate to http://localhost:8001
- Click "Register" button in navigation

**Step 2: Select Your Role**
- **Seeker** - Looking for a mess to join
- **Member** - Already part of a mess, need to log in
- **Manager** - Running a mess

**Step 3: Fill In Your Details**
- **Name**: Your full name (min 1 char, max 100 chars)
- **Email**: Valid email address (will be your login username)
- **Password**: Min 8 characters for security
- **Confirm Password**: Must match password field

**Step 4: Submit**
- Click "Register" button
- Account created immediately
- You'll be asked to log in

### Logging In

**Step 1: Go to Login Page**
- Navigate to http://localhost:8001/login
- Or click "Login" if redirected from registration

**Step 2: Enter Credentials**
- **Email**: Your registered email address
- **Password**: Your chosen password

**Step 3: Submit**
- Click "Login" button
- Session starts, redirected to dashboard

### Logging Out

- Click your name in top-right navbar
- Click "Logout" button
- Session ends, redirected to login

---

## 🔍 For Seekers

Seekers browse available messes and request to join them.

### Step 1: View Available Messes

**After Login**:
1. You're redirected to **Seeker Dashboard**
2. View list of all active messes
3. See: Mess name, location, manager info

### Step 2: Request to Join

**For Each Mess You're Interested In**:

1. View mess details
2. Click "Request to Join" button
3. (Optional) Add a custom message
   - "Hi, I'm interested in joining..."
   - "I'm a graduate student looking for housing..."
4. Click "Send Request"

**Status After Submission**:
- Status shows as "Pending"
- Wait for manager approval
- You'll be notified when approved

### Step 3: Monitor Your Requests

**View All Request Statuses**:
1. Go to your **Seeker Dashboard**
2. Scroll to the "Your Join Requests" table
3. View the status of your applications visually labeled as Pending, Approved, or Rejected
4. Approved requests: You are now a member!

### Step 4: After Approval

Once a manager approves your request:
1. You're automatically added as a member
2. Your role changes from "Seeker" to "Member"
3. New options unlock in dashboard
4. Can now log meals and deposits

---

## 👤 For Members

Members log meals, track their balance, and manage deposits.

### Dashboard Overview

**After Login as Member**:

Your dashboard shows:

1. **Summary Cards**:
   - Meals Logged: Count of meals you've entered
   - Total Balance: How much you owe/are owed
   - Deposits Made: Total money you've contributed
   - Meal Rate: Current rate per meal

2. **Mess Summary Table**:
   - For each mess you're in
   - Your balance in that mess specifically
   - Quick links to mess details

3. **Action Buttons**:
   - "Log Meal"
   - "Make Deposit"
   - "View Profile"

---

### Task 1: Log Daily Meals

**When to Log**: After eating at the mess

**Steps**:
1. Click "Log Meal" button on dashboard
2. **Select Mess**: Choose which mess (if in multiple)
3. **Select Date**: Pick the date you ate
4. **Enter Meals**: Number of meals
   - 1.0 = full meal
   - 0.5 = half meal
   - 1.5 = meal + breakfast/snack
5. Click "Save Entry"

**Rules**:
- Can't log same meal twice for same day
- Can't log future dates
- Meals can be fractional

**Example Workflow**:
- Monday dinner: Log 1.0 meal
- Tuesday breakfast + lunch: Log 2.0 meals
- Wednesday half day (breakfast only): Log 0.5 meal

**View Meal History**:
- Recent meals shown on dashboard
- See past 20 entries
- Verify your logs are correct

---

### Task 2: Make a Deposit

**When to Deposit**: Contribute money toward expenses

**Deposit Options**:
1. From dashboard, click "Make Deposit"
2. **Select Mess**: Which mess is this for
3. **Amount**: How much you're depositing
4. **Date**: When you're paying
5. **Method** (optional):
   - Cash
   - Bank Transfer
   - Online Payment
6. **Reference** (optional):
   - UTR (for bank transfers)
   - Transaction ID
   - Receipt number
7. Click "Record Deposit"

**Example**:
```
Depositing $200 cash to Mess "Home Sweet Home" on April 28
Amount: $200
Date: 2026-04-28
Method: Cash
Reference: (leave blank)
```

**View Deposit History**:
- Recent deposits shown on dashboard
- See all past deposits
- Review transaction details

---

### Task 3: Check Your Balance

**Balance Calculation**:
```
Balance = (Your Meals × Meal Rate) - Your Total Deposits

If Balance > 0: You owe money
If Balance < 0: You're owed refund
If Balance = 0: Settled up
```

**Where to See**:
1. **Dashboard**: "Total Balance" card shows live balance
2. **Per-Mess**: Mess Summary table shows balance per mess
3. **Profile**: Full balance sheet

**Understanding Your Balance**:

Example 1: Member owes
```
Meals: 50 × Rate $5.00 = $250
Deposits: $200
Balance: $50 (you owe)
```

Example 2: Member is owed
```
Meals: 30 × Rate $5.00 = $150
Deposits: $200
Balance: -$50 (you're owed)
```

---

### Task 4: Update Your Profile

**What You Can Change**:
1. Full name
2. Email address
3. Password

**Steps**:
1. Click your name in navigation → "Profile"
2. Click "Update Profile" section
3. Edit fields:
   - Name: New display name
   - Email: New email (must be unique)
4. Click "Save Profile"

---

### Task 5: Change Your Password

**Steps**:
1. Go to Profile page
2. Click "Change Password" section
3. Enter:
   - **Current Password**: Your existing password
   - **New Password**: New password (min 8 chars)
   - **Confirm Password**: Must match
4. Click "Update Password"

**Important**:
- Remember your new password
- For security, use strong passwords
- Mix of letters, numbers, symbols recommended

---

## 👨‍💼 For Managers

Managers create messes, record finances, and approve members.

### Dashboard Overview

**After Login as Manager**:

Your dashboard shows:

1. **Summary Cards**:
   - Active Members: Count of mess members
   - Total Expenses: Sum of all expenses
   - Total Meals Logged: Sum of member meals
   - Current Meal Rate: Per-meal cost

2. **Pending Requests Table**:
   - Join requests awaiting your decision
   - Seeker info and message
   - Approve/Reject buttons

3. **Member Balances Table**:
   - All members and their financial status
   - How much each member owes/is owed

4. **Action Buttons**:
   - "Create Mess"
   - "Record Expense"
   - "Record Deposit"
   - "View Requests"

---

### Task 1: Create a Mess

**When**: Setting up a new shared housing unit

**Steps**:
1. From dashboard, click "Create Mess"
2. Fill in details:
   - **Mess Name**: E.g., "Home Sweet Home"
   - **Location**: Physical address
   - **Monthly Rent** (optional): How much rent costs
   - **Description** (optional): Additional details
3. Click "Create Mess"

**After Creation**:
- You're automatically the manager
- You can now invite members
- You can record expenses

**Example**:
```
Name: Harmony House
Location: 123 Main Street, Downtown
Rent: $5000/month
Description: 3-bedroom shared home with common areas
```

---

### Task 2: Review Join Requests

**Pending Members Workflow**:

**Step 1: See Requests**
- "Pending Join Requests" shown on dashboard
- See: Seeker name, message, request date

**Step 2: Review Request**
- Click on request to see details
- Read seeker's message
- Check their profile if available

**Step 3: Approve or Reject**

**To Approve**:
1. Click "Approve" button
2. Member is added to your mess
3. Member assigned as "member" role
4. Member can now log meals/deposits

**To Reject**:
1. Click "Reject" button
2. Request is marked rejected
3. Seeker will see rejection status
4. They can request again later

---

### Task 3: Record Expenses

**When**: Track shared expenses (groceries, rent, utilities, etc.)

**Steps**:
1. From dashboard, click "Record Expense"
2. **Select Mess**: Which mess is this for
3. **Purchased By**: Which member paid
4. **Expense Date**: When was it purchased
5. **Amount**: How much was spent
6. **Vendor** (optional): Store/merchant name
   - "Big Bazaar"
   - "Local Market"
   - "Utilities Company"
7. **Notes** (optional): Details
   - "Weekly groceries"
   - "Electricity bill"
8. Click "Add Expense"

**Example Entries**:
```
Entry 1:
- Mess: Home Sweet Home
- Purchased By: John (manager)
- Date: 2026-04-28
- Amount: $150
- Vendor: ABC Grocery Store
- Notes: Weekly groceries

Entry 2:
- Mess: Home Sweet Home
- Purchased By: Sarah
- Date: 2026-04-28
- Amount: $80
- Vendor: Online Market
- Notes: Fruits and vegetables
```

**After Recording**:
- Expense added to mess finances
- Counts toward meal rate calculation
- Appears in expense history

---

### Task 4: Record Member Deposits

**When**: Members pay their contribution

**Steps**:
1. From dashboard, click "Record Deposit"
2. **Select Mess**: Which mess
3. **Member**: Which member is paying
4. **Amount**: Payment amount
5. **Deposit Date**: When paid
6. **Method** (optional):
   - "Cash" - Physical currency
   - "Bank Transfer" - Online transfer
   - "Check" - Cheque payment
7. **Reference** (optional):
   - Bank: UTR/Reference number
   - Online: Transaction ID
   - Check: Check number
8. Click "Record Deposit"

**Example**:
```
Member: John
Amount: $250
Date: 2026-04-28
Method: Bank Transfer
Reference: UTR2610123456789
```

**Tracking Deposits**:
- All deposits recorded in history
- Used to calculate member balances
- Important for final settlements

---

### Task 5: Monitor Meal Rate

**What is Meal Rate?**:
- Cost per meal = Total Expenses ÷ Total Meals
- Automatically calculated
- Updated as expenses/meals change

**Example**:
```
Total Expenses: $1000
Total Meals: 200
Meal Rate: $1000 ÷ 200 = $5.00 per meal
```

**Why It Matters**:
- Determines member obligations
- Fair basis for splitting costs
- Updated after each expense
- Shown on dashboard

**Monitoring**:
- Dashboard shows current rate
- Track changes over time
- Alert members of high rates

---

### Task 6: Manage Member Balances

**View All Balances**:
- "Member Balances" table on dashboard
- Shows each member's status:
  - Amount owed (positive number)
  - Amount due to them (negative number)
  - Settled (zero)

**Settlement Process**:
1. Review all member balances
2. Identify settled members (balance = 0)
3. For unsettled members:
   - Remind of deposits needed
   - Coordinate final payment
   - Deduct from balance once paid
4. Final settlement: All balances = 0

---

## 🔄 Common Tasks

### Task: Check Meal Rate

**Why**: Understand current cost per meal

**Steps**:
1. Go to Dashboard
2. Find "Current Meal Rate" card
3. Rate per meal shown
4. Updates automatically with new expenses/meals

---

### Task: Download Balance Report

**Currently**: Visible on dashboard and tables  
**Future**: PDF export available

**What's Shown**:
- Member name
- Meals logged
- Total owed (meals × rate)
- Deposits made
- Final balance (owed - deposits)

---

### Task: Settle Between Members

**When**: Two members need to settle (e.g., Member A owes Member B)

**How**:
1. Calculate amount owed
2. Member A pays Member B directly (outside system)
3. Member A deposits money to mess (system recognizes)
4. Balance updates automatically

**Example**:
```
Initial:
- Member A: Balance +$100 (owes)
- Member B: Balance -$100 (owed)

After Member A deposits $100:
- Member A: Balance $0 (settled)
- Member B: Balance $0 (settled)
```

---

### Task: View Transaction History

**What**:
- All expenses and meals
- All deposits and payments
- Complete audit trail

**Where**:
- Dashboard shows recent (last 10-20)
- Full history available in detailed views
- Export (future feature)

---

### Task: Edit Mess Information

**For Managers Only**:

**Steps**:
1. Dashboard or mess details page
2. Click "Edit Mess" button
3. Update:
   - Name
   - Location
   - Rent amount
   - Description
4. Click "Update Mess"

---

### Task: Invite Members

**Methods**:
1. **Direct**: Manage join requests → Approve seekers
2. **Share Link** (future): Send invite to seekers
3. **Email** (future): Direct email invitations

**Current Flow**:
1. Seeker registers as "Seeker"
2. Seeker requests to join your mess
3. You approve request
4. Seeker becomes "Member"

---

## 🐛 Troubleshooting

### "Invalid Password or Email"

**Problem**: Can't log in

**Solutions**:
1. Check email is spelled correctly
2. Verify Caps Lock is off
3. Reset password:
   - Contact manager/administrator
   - Request password reset
4. Check email is your registered email (from registration)

---

### "You've Already Logged Meals For This Date"

**Problem**: Can't log meal for a day

**Reason**: You already have an entry for that date/mess

**Solutions**:
1. Check dashboard meal history
2. If entry is wrong, contact manager
3. If duplicate, it will be removed
4. Try different date

---

### "You Are Not a Member of This Mess"

**Problem**: Can't log meals or deposit to a mess

**Reason**: Your membership request hasn't been approved

**Solutions**:
1. Verify request was sent
2. Check status on "My Requests"
3. Wait for manager approval
4. Contact mess manager if delayed

---

### "Not Authorized to View This"

**Problem**: Access denied error

**Reason**: You don't have permission (role restriction)

**Solutions**:
1. Only members see "Log Meal"
2. Only managers see "Record Expense"
3. If you should have access:
   - Verify your role
   - Contact administrator
   - Check if membership is active

---

### "Email Already Exists"

**Problem**: Can't register with that email

**Reason**: Email is already registered

**Solutions**:
1. Use different email for new account
2. If it's your email:
   - Try logging in instead
   - Request password reset
3. Check email spelling

---

### Meal Rate Seems Incorrect

**Problem**: Balance calculation looks wrong

**Debug Steps**:
1. Check: Meals logged (count)
2. Check: Total deposits made
3. Verify: Calculation = (Meals × Rate) - Deposits
4. Example: (50 × $5) - $200 = $50
5. If still wrong, contact manager

---

### Can't See Other Members' Information

**Problem**: Privacy concern

**Reason**: By design, members see limited info about each other

**Information Visible**:
- Names (in balance sheet)
- Roles (member/manager)
- Balances (for transparency)

**Information Hidden**:
- Phone numbers
- Email addresses (except manager)
- Personal details

---

### Session Keeps Timing Out

**Problem**: Get logged out frequently

**Causes**:
- Browser privacy settings
- Network interruption
- Server session timeout
- Cookies disabled

**Solutions**:
1. Enable cookies in browser
2. Check internet connection
3. Log in again
4. Contact administrator if persists

---

### Page Won't Load / Styling Broken

**Problem**: CSS not loading, ugly layout

**Causes**:
- CSS file not loading (Tailwind CDN)
- Browser cache issue
- Network issue

**Solutions**:
1. Hard refresh: Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
2. Clear browser cache
3. Try different browser
4. Check internet connection
5. Contact administrator

---

### Error: "CSRF Token Missing"

**Problem**: Form submission fails

**Reason**: Security token not found

**Solutions**:
1. The page generates this automatically
2. Try refreshing and resubmitting
3. Clear cookies and log in again
4. Try different browser
5. Contact administrator

---

## 📞 Getting Help

### Issues or Questions?

1. **Within the App**:
   - Hover over fields for help text
   - Error messages explain what went wrong

2. **Beyond the App**:
   - Contact your mess manager
   - Contact application administrator
   - Email support team

3. **Report a Bug**:
   - Document steps to reproduce
   - Screenshot if possible
   - Contact support with details

---

## 🎓 Tips & Best Practices

### For Better Finances

1. **Log meals immediately** - Don't forget entries
2. **Keep receipts** - Reference expense details
3. **Make deposits promptly** - Don't fall behind
4. **Monitor your balance** - Check dashboard weekly
5. **Communicate** - Tell manager of special expenses

### For Smooth Operations (Managers)

1. **Set meal rate period** - Weekly, monthly, or custom
2. **Review balances regularly** - Weekly or monthly
3. **Approve requests promptly** - Encourage new members
4. **Document expenses** - Keep vendor details
5. **Final settlement** - Settle all at end of period

### General Best Practices

1. **Use descriptions** - Mark what expenses are for
2. **Consistent dates** - Log meals on actual date
3. **Regular deposits** - Don't accumulate debt
4. **Track carefully** - Precision matters
5. **Communicate clearly** - Messages in join requests help

---

**Last Updated**: April 2026  
**Version**: 1.0.0  
**Questions?** Contact your mess administrator
