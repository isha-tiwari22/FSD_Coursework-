# Lost & Found Management System

A complete dynamic web application for managing lost and found items, built with PHP and MySQL.

## Features
- **User Authentication**: Secure Login and Registration system.
- **CRUD Operations**: Create, Read, Update, and Delete lost/found items.
- **Advanced Search**: 
  - Live search filtering (AJAX / Fetch API) without page reload.
  - Autocomplete references.
  - Filter by status (Lost, Found, Claimed).
- **Security**:
  - SQL Injection prevention using Prepared Statements (PDO).
  - XSS prevention using output escaping.
  - CSRF protection on forms.
  - Password hashing (bcrypt).
- **Modern UI**: Fully responsive, dark-themed design with "Glassmorphism" aesthetics using CSS3 variables.

## Setup Instructions

1.  **Database Setup**:
    -   Create a MySQL database named `lost_found_db`.
    -   Import the `config/schema.sql` file into your database using phpMyAdmin or the mysql command line.
    -   *Alternative*: Visit `public/setup_db.php` in your browser (after configuring db connection) to attempt auto-creation.

2.  **Configuration**:
    -   Open `config/db.php`.
    -   Update the `$username` and `$password` variables with your MySQL credentials.
    -   Default is set to `root` with no password.

3.  **Running the Project**:
    -   Deploy the project folder to your web server (e.g., Apache/HTDOCS).
    -   Ensure the document root points to the `public/` directory for best security, or navigate to `project_folder/public/index.php`.

## Login Credentials (Test User)
A test user is created by the schema script:
- **Username**: `testuser`
- **Password**: `password123`

## Directory Structure
```
project_root/
│── config/        # Database connection and schema
│── public/        # Publicly accessible files (the site root)
│   ├── assets/    # CSS and JS
│   ├── index.php  # Dashboard
│   ├── ...        # Other pages
│── includes/      # Reusable PHP components (Header, Footer, Functions)
```

## Known Issues
-   Email sending is not configured (registration simulates success).
-   Image upload for items is not implemented in this version (optional feature).
