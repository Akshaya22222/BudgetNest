# BudgetNest --- Smart Budget Planner

BudgetNest is a PHP and MySQL-based personal finance web application for
recording income and expenses, organizing budgets, tracking savings
goals, and reviewing financial activity through dashboards and reports.

The project includes separate pages for user-facing finance tools and
administrative management. It is intended as a local web-development
project that can be run with XAMPP.

## Features

Based on the supplied project files, BudgetNest includes pages and
workflows for:

-   **User registration and login** with session-based access
-   **Dashboard** for navigating finance tools
-   **Income and expense tracking** --- add, view, and delete
    income/expense records
-   **Recurring transactions** --- add, view, edit, delete, and process
    recurring items
-   **Budget limits and comparisons** --- set limits and compare budget
    information
-   **Savings tools** --- savings records, savings targets, emergency
    funds, and savings challenges
-   **Reminders and alerts** --- set, edit, view, and delete reminders
-   **Reports and trends** --- financial reports, monthly trends, and
    expense history
-   **Financial insights** --- smart suggestions and a financial-health
    page
-   **Wallets/accounts** --- create and manage wallet balances
-   **Shared budget and bill splitting** tools
-   **Calendar view** for financial planning
-   **Data export** through an export page
-   **Profile and settings** pages, including privacy-related settings
    and theme options
-   **Admin pages** for managing users, expenses, savings, and reports
-   **UI customization** with a dark theme and interactive dashboard
    elements

Some features may require additional database tables or configuration.
The ZIP does not include a database export, so the complete database
schema could not be verified from the supplied files.

## Technology Stack

-   PHP
-   MySQL
-   HTML5
-   CSS3
-   JavaScript
-   Chart.js (used by the reports page)
-   XAMPP / Apache for local development

## Project Structure

The supplied ZIP contains a folder named `prjct_smart_budget` with PHP
pages such as:

``` text
prjct_smart_budget/
├── index.php
├── register.php
├── dashboard.php
├── config.php
├── add-income.php
├── add-expense.php
├── view-income.php
├── view-expenses.php
├── reports.php
├── monthly-trends.php
├── budget-comparison.php
├── smart-suggestions.php
├── savings-challenges.php
├── set-target.php
├── set-emergency-fund.php
├── wallets.php
├── shared-budget.php
├── split-bill.php
├── calendar-view.php
├── admin_dashboard.php
├── admin_users.php
├── admin_expenses.php
├── admin_savings.php
├── admin_reports.php
├── dark-mode.php
├── dark-theme.css
├── theme.js
└── ... other finance, settings, and management pages
```

## Requirements

-   PHP-enabled web server (XAMPP is recommended for local Windows
    development)
-   MySQL or MariaDB
-   A modern web browser

## Run Locally with XAMPP

### 1. Copy the project

Extract the ZIP file and place the `prjct_smart_budget` folder inside
XAMPP's `htdocs` directory. For example:

``` text
C:\xampp\htdocs\prjct_smart_budget
```

### 2. Start services

1.  Open **XAMPP Control Panel**.
2.  Start **Apache**.
3.  Start **MySQL**.

### 3. Create the database

The current `config.php` is configured for a local database named
`smart_budget` with host `localhost`, username `root`, and a blank
password.

1.  Open `http://localhost/phpmyadmin/`.
2.  Create a database named `smart_budget`.
3.  Import the project's SQL schema/data if you have it.

**Database setup note:** No `.sql` file was included in the uploaded
ZIP. The application will need the correct tables and columns before
database-backed features can work. Check the SQL queries in the PHP
files or add the matching schema before testing.

### 4. Open the application

Visit:

``` text
http://localhost/prjct_smart_budget/
```

The `index.php` file serves as the login page.

## Configuration

Database settings are currently stored in `config.php`. Adjust the host,
username, password, and database name to match your local MySQL
configuration.

For a public GitHub repository or deployment, avoid committing real
credentials. Use environment variables or a separate local configuration
file that is excluded from version control.

## Troubleshooting

### Database connection failed

-   Confirm that MySQL is running in XAMPP.
-   Confirm that the database `smart_budget` exists.
-   Verify the connection values in `config.php`.
-   Ensure the required tables and columns have been created.

### Page redirects to login or a page is missing

-   Open the project through `http://localhost/...`; do not double-click
    PHP files.
-   Confirm that the project folder is inside `htdocs`.
-   Check that all linked PHP files are present and that links match
    their filenames.

### Charts or external resources do not load

-   Check your internet connection if a page relies on a CDN such as
    Chart.js.
-   Review browser developer tools and Apache/PHP logs for errors.

## Security Notes

This project is for learning and local development unless it has been
reviewed and hardened for production.

-   Use secure password hashing and verify authentication and
    authorization checks.
-   Validate input and escape output.
-   Use prepared statements for database queries.
-   Add CSRF protection to state-changing forms.
-   Do not expose real database credentials or personal financial
    information.
-   Review admin access controls before deploying.
-   Back up the database before making changes.

