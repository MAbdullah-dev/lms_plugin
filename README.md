# Class Management and Booking System - lms_plugin

This project is a custom-built class management and booking system developed in core PHP. Inspired by Amelia, it supports class creation, booking management, and payment integration with PayPal and Stripe.

## Installation

### 1. Clone the Repository
Clone the repository into your local server (e.g., `htdocs` if using XAMPP).

```bash
git clone https://github.com/MAbdullah-dev/lms_plugin.git

2. Database Configuration
Create a MySQL database named "lms".


Edit the database configuration in the relevant file (e.g., config/Db.php):

$this->host = 'localhost';
$this->user = 'your_db_user';
$this->pass = 'your_db_password';
$this->dbname = 'lms';


3. Run the Database Seeder
To seed the database with initial data, open the following URL in your browser:

http://localhost/lms/config/seeder.php

4. Run the Project
You can access the login page to start using the system by visiting:

http://localhost/lms/views/login.php

Need Help?
If you encounter any issues during setup, feel free to reach out via email:
* m.abdullah.web.dev@gmail.com
* ma0621450@gmail.com

Feel free to adjust as necessary!
