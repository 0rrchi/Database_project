# Database_project
A database-backed Teaching Assistant (TA) Recruitment &amp; Management System built for East West University using PHP, MySQL, HTML5, and CSS3. Features Role-Based Access Control (RBAC), eligibility verification, and full CRUD functionality.

                                                                          East West University - Teaching Assistant (TA) Management System

A database-driven web application designed for East West University (EWU) to automate, organize, and streamline the Teaching Assistant (TA) recruitment, application, and selection process.
Built as the final project for CSE302: Database Systems.

 Project Overview & Purpose 
 
In standard academic environments, managing Teaching Assistant recruitment manually via emails or physical forms creates disorganization, security risks, and tracking issues.
This system solves that by providing a centralized web platform supporting three distinct user roles (Admin, Faculty, and Student) , each governed by strict Role-Based Access Control (RBAC) and database integrity constraints.
Note on Administration: Unlike students and faculty who register or activate accounts via the web portal, admin user accounts are pre-seeded directly into the MySQL database during system setup. Administrators must log in using these pre-configured database credentials to begin managing system data.
 Key Features
 
 1. Administrator Module
Pre-Seeded Access: Admin credentials (ID and Password) are inserted directly into the database during initial setup, bypassing public user registration.
System Management (CRUD): Full Create, Read, Update, and Delete capabilities for Departments, Courses, Faculty, and Students.
Faculty-Course Assignment: Assigns courses to specific faculty members for specific terms/semesters.
Student Course Records: Enters completed course records and grades for students (used for TA prerequisite verification).
System Overview: Live metric dashboard displaying total registered students, faculty, courses, departments, and pending TA applications.

 2. Faculty Module
Recruitment Control: Toggle recruitment status (RecruitingTA = TRUE / FALSE) to open or close applications.
Application Review: View student applicants along with their CGPA, department, and uploaded documents (CV and Cover Letter).
Automated Selection: Accepting a TA automatically:
Sets application status to Accepted.
Closes the faculty's recruitment status.
Rejects the student's other pending applications across the system.
Profile Management: Update personal contact email and password.

 3. Student Module
Account Activation: Secure account activation using Student ID and institutional email.
Prerequisite Verification: Dynamic eligibility checking ensuring students can only apply if:
Their CGPA ≥ 3.50.
They have successfully completed all courses taught by the faculty member.
Application Tracker: Submit PDF resumes/cover letters and monitor application status (Pending, Accepted, Rejected) in real-time.
 
 Tools & Technologies Used
Frontend: HTML5, CSS3 (Flexbox & CSS Grid)
Backend: PHP (PHP 8.x) with MySQLi Prepared Statements
Database: MySQL (Relational Schema with Foreign Keys, Cascades, & Unique Constraints)
Environment: XAMPP (Apache Web Server & MySQL Database)


                                                                            How the System Works (Complete User Flow)
1. Account Creation & Activation Flow

[Admin creates Student/Faculty Profile]
                 │
                 ▼
[Student/Faculty goes to activate_student.php / activate_faculty.php]
                 │
                 ▼
[Enters ID & Institutional Email] ──(Verifies Match)──► [Sets Password & IsActivated = 1]
                                                                 │
                                                                 ▼
                                                        [Redirects to login.php]




2. Login & Security Verification Flow (RBAC)

[User enters ID & Password on login.php]
                 │
                 ▼
[Checks Admin ➔ Student ➔ Faculty Tables]
                 │
                 ▼
[Verifies Credentials & Session Variables: $_SESSION['user_id'] & $_SESSION['user_type']]
                 │
                 ▼
[Redirects to Role Dashboard (admin/dashboard.php, faculty/dashboard.php, or student/dashboard.php)]


Security Enforcement: Every protected page enforces $required_role. If a student or faculty attempts to manually alter the URL to access an admin page, includes/session.php intercepts the request, blocks execution, and safely redirects them back.
3. Application & Selection Flow
[Faculty opens recruitment flag]
                 │
                 ▼
[Student applies via student/apply_ta.php]
   ├── Checks CGPA >= 3.50
   ├── Verifies completion of all courses taught by faculty
   └── Submits CV and Cover Letter (PDF)
                 │
                 ▼
[Faculty reviews in faculty/view_applications.php]
                 │
                 ├──► [If Rejected] ──► Status updated to 'Rejected'
                 │
                 └──► [If Accepted (Database Transaction)]
                        ├── 1. Application Status set to 'Accepted'
                        ├── 2. Faculty RecruitingTA flag set to FALSE
                        └── 3. Student's other pending applications set to 'Rejected'



                                                                                  📂 Project Structure

ewu_ta_management/
├── admin/                 # Administrator dashboards and CRUD scripts
│   ├── dashboard.php
│   ├── manage_departments.php
│   ├── manage_courses.php
│   ├── manage_faculty.php
│   └── manage_students.php
├── faculty/               # Faculty management & review scripts
│   ├── dashboard.php
│   ├── my_courses.php
│   ├── edit_profile.php
│   └── view_applications.php
├── student/               # Student portal & application scripts
│   ├── dashboard.php
│   ├── profile.php
│   ├── edit_profile.php
│   ├── apply_ta.php
│   └── my_applications.php
├── includes/              # DB connection & RBAC session handlers
│   ├── db_conn.php
│   └── session.php
├── css/                   # Global stylesheets
│   └── style.css
├── uploads/               # PDF upload storage (Resumes & Cover Letters)
├── schema.sql             # MySQL Database Script
├── login.php              # Central authentication portal
├── logout.php             # Session destruction handler
├── activate_student.php   # Student account activation
└── activate_faculty.php   # Faculty account activation


                                                                           Installation & Setup Instructions
Clone the Repository:
Bash
git clone https://github.com/your-username/ewu-ta-management-system.git


Database Setup:
Open phpMyAdmin or your MySQL client.
Create a new database named ewu_ta_management.
Import the schema.sql file provided in the project root.
Database Configuration:
Ensure includes/db_conn.php matches your local database credentials:
PHP
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ewu_ta_management";


Run the Project:
Move the project folder into your web server directory (htdocs for XAMPP).
Open your browser and navigate to:
Plaintext
http://localhost/ewu_ta_management/login.php



