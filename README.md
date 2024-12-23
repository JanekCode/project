Project Documentation: Log Management System
1. Project Description
The project is designed to accept log entries from users and store them in a MySQL database. Log entries can be submitted via a web interface or an API. The system supports various log severity levels (according to Syslog standards) and stores additional information about the project to which the log entry relates. Authentication mechanisms include checking API tokens or CSRF tokens for web applications.

2. Features
Adding Logs:

Logs can be added by users via both the web application and external APIs.
Each log contains information about the project, severity level, and message content.
Authentication and Authorization:

The application uses CSRF tokens for web applications and API tokens for external applications.
Access to the project for which a log is being added is checked.
Log Validation:

Logs must contain at least 10 characters in the message.
The severity level of the log must be one of the following: Emergency, Alert, Critical, Error, Warning, Notice, Informational, Debug.
Database:

Logs are stored in the logs table, and project access is verified via the projects table.

3. Database Structure
Overview:
users Table:

-Stores information about users including their username, password hash, API token, and role.
-Primary Key: id

projects Table:

-Stores project-related information such as the project name and the user ID that owns the project.
-Primary Key: id
-Foreign Key: user_id references users(id)

logs Table:

-Stores the logs generated by users, with details such as severity, log message, associated project, and user ID.
-Primary Key: id
-Foreign Keys: project_id references projects(id), user_id references users(id)

4. Installation
To set up and run the project on your machine, follow the steps below:

4.1 Requirements
PHP 7.4 or higher
MySQL or MariaDB
Web server (e.g., Apache, Nginx)
PHP PDO with MySQL support
Optional: Nginx/Apache with PHP support

4.2 Database Setup
Create the database in MySQL:

CREATE DATABASE log_manager;

projects.sql, logs.sql, users.sql have all dumping data for table `projects` and create project etc...

5. Usage

Adding Logs (via API)
-To add a log via the API, send a POST request to the appropriate endpoint with the data in JSON format.

-Use a bearer token, which is updated with each login via login.php. (You can only add logs to projects that you own).

http://127.0.0.1/api/api.php 

body
json
{
    "project_id": 1,
    "severity": "Critical",
    "message": "An error occurred in the application"
}

example answer
json
{"success":"Log added successfully"}

5.2 Adding Logs (via Web Interface)
-User logs in to the application.
-Goes to the log submission form.
-Fills in the form by selecting the project, severity level, and entering the message.
-The log is saved in the database, and the user is shown an appropriate response.

6. Security Mechanisms
User can use panel if he accept in database (role must be admin)
CSRF Tokens: Ensures that requests originate from the legitimate application.
API Tokens: Allows authentication for external requests.
Data Validation: All incoming data (e.g., severity level, message content) is validated before being saved to the database.

7. Conclusion
This log management system provides a mechanism for adding, storing, and authenticating logs using PHP and MySQL. It also supports integration with external applications via API tokens and handles CSRF for web applications.