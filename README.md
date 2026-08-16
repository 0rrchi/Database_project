# East West University Teaching Assistant Recruitment and Management System

A web-based **Teaching Assistant (TA) Recruitment and Management System** developed for East West University. The system is designed to streamline the TA recruitment process by allowing students to apply for TA opportunities, faculty members to recruit and manage TAs, and administrators to manage the overall system.

---

## 📌 Project Overview

The **EWU TA Management System** provides a centralized platform for managing the Teaching Assistant recruitment process.

The system supports multiple types of users and provides role-based access to different parts of the application.

### Main Users

* **Admin**
* **Student**
* **Faculty**
* **Committee Member**

The home page describes the system as a platform where students can apply for Teaching Assistant positions, faculty members can recruit TAs, and administrators can manage the recruitment process.

---

## ✨ Features

### 👨‍🎓 Student

Students can:

* Activate their university account
* Log in using their University ID and password
* Access the student dashboard
* Apply for TA positions
* Submit a resume
* Submit a cover letter
* Track their TA applications
* Have their eligibility determined based on completed courses

## Student accounts contain an `IsActivated` status, and an inactive account cannot access the student dashboard.

### 👨‍🏫 Faculty

Faculty members can:

* Activate their university account
* Log in using their Faculty ID
* Access the faculty dashboard
* Recruit Teaching Assistants
* Manage TA recruitment activities
* Receive student TA applications

Faculty accounts contain an `IsActivated` field and a `RecruitingTA` field in the database.

Faculty activation requires the Faculty ID and university email to match an existing faculty record.

---

### 👨‍💼 Admin

Administrators can access the administrative section of the system.

The login system identifies administrators separately and redirects authenticated administrators to:

```text
admin/dashboard.php
```

## The database also contains a dedicated `Admin` table.

### 👥 Committee Member

Faculty members who are registered as committee members can choose between two roles after logging in:

* Continue as Faculty
* Continue as Committee Member

The system checks both whether the user is logged in and whether the user has committee-member privileges before displaying the role-selection page.

---

## 🔐 Authentication & Account Activation

The system provides a centralized login page for:

* Admin
* Student
* Faculty

Users provide their University ID and password. The system checks the appropriate database table and redirects the user to the corresponding dashboard.

### Student Activation

Students can activate their accounts using:

* Student ID
* University Email
* New Password

The system verifies that the Student ID and email match an existing record before activating the account.

### Faculty Activation

Faculty members follow a similar process using:

* Faculty ID
* University Email
* New Password

## After successful activation, the user is redirected to the login page.

## 🗄️ Database Structure

The project uses a MySQL database named:

```text
ewu_ta_management
```

The database script recreates the database and defines the required tables.

### Main Tables

| Table            | Purpose                                           |
| ---------------- | ------------------------------------------------- |
| `Department`     | Stores department information                     |
| `Admin`          | Stores administrator accounts                     |
| `Student`        | Stores student information and account status     |
| `Faculty`        | Stores faculty information and recruitment status |
| `Course`         | Stores course information                         |
| `Faculty_Course` | Associates faculty members with courses           |
| `Student_Course` | Stores courses taken/completed by students        |
| `TA_Application` | Stores student TA applications                    |

---

## 🔗 Database Relationships

The database uses primary keys and foreign keys to maintain relationships between entities.

### Department → Student

Each student belongs to a department.

### Department → Faculty

Each faculty member belongs to a department.

### Department → Course

Each course belongs to a department.

### Faculty → Faculty_Course

`Faculty_Course` stores the courses taught by faculty members.

### Student → Student_Course

`Student_Course` stores courses completed or taken by students and is used to determine student eligibility.

### Student → TA_Application

A student can submit TA applications.

### Faculty → TA_Application

Applications are submitted directly to a faculty member.

The current database design explicitly stores `StudentID` and `FacultyID` in `TA_Application`.

---

## 📋 TA Application

The `TA_Application` table contains:

* Application ID
* Student ID
* Faculty ID
* Resume File
* Cover Letter
* Application Date
* Application Status

Application status can be:

```text
Pending
Accepted
Rejected
```

The database also prevents the same student from submitting duplicate applications to the same faculty member through a unique constraint on:

```text
StudentID + FacultyID
```

---

## 🛠️ Technologies Used

### Frontend

* HTML5
* CSS3
* JavaScript

### Backend

* PHP

### Database

* MySQL

### Development Environment

* XAMPP
* Apache
* MySQL

The PHP pages use a shared database connection through:

```text
includes/db_conn.php
```

## For example, both student and faculty account activation pages include the database connection file.

## 📁 Project Structure

A simplified structure of the project is:

```text
ewu_ta_management/
│
├── index.php
├── login.php
├── logout.php
├── choose_role.php
├── activate_student.php
├── activate_faculty.php
├── test_connection.php
├── sql.txt
│
├── admin/
│   └── dashboard.php
│
├── student/
│   └── dashboard.php
│
├── faculty/
│   └── dashboard.php
│
├── committee/
│   └── dashboard.php
│
├── includes/
│   ├── db_conn.php
│   └── session.php
│
├── css/
│   └── style.css
│
└── ...
```

> The exact contents of the `admin`, `student`, `faculty`, `committee`, `includes`, and `css` directories may vary depending on the current project files.

---

## ⚙️ Installation & Setup

### 1. Install XAMPP

Install **XAMPP** with:

* Apache
* MySQL
* PHP

---

### 2. Clone the Repository

Clone the project into the XAMPP `htdocs` directory:

```bash
git clone <YOUR-GITHUB-REPOSITORY-URL>
```

Then place the project inside:

```text
C:\xampp\htdocs\
```

For example:

```text
C:\xampp\htdocs\ewu_ta_management
```

---

### 3. Start XAMPP

Open XAMPP Control Panel and start:

```text
Apache
MySQL
```

---

### 4. Create the Database

Open:

```text
http://localhost/phpmyadmin
```

Import:

```text
sql.txt
```

The SQL script creates the database:

```text
ewu_ta_management
```

and creates the required tables.

---

### 5. Configure Database Connection

Make sure the database connection file contains the correct MySQL configuration:

```text
includes/db_conn.php
```

The application relies on this file for database connectivity.

---

### 6. Test the Database Connection

Open:

```text
http://localhost/ewu_ta_management/test_connection.php
```

A successful connection should display:

```text
Database Connected Successfully!
```

---

## 🚀 Running the Application

After starting Apache and MySQL, open:

```text
http://localhost/ewu_ta_management/
```

The home page provides navigation to:

* Home
* Login

---

## 🔑 Default Admin Accounts

The provided SQL script contains the following administrator records:

| Admin ID | Password   | Email               |
| -------- | ---------- | ------------------- |
| `ADM001` | `admin123` | `admin1@ewu.edu.bd` |
| `ADM002` | `admin456` | `admin2@ewu.edu.bd` |

These credentials are present in the supplied database script.

> **Security Note:** These are development/demo credentials. Change them before using the application in a real environment.

---

## 🔄 Basic System Workflow

```text
                    ┌──────────────────┐
                    │      Home Page   │
                    └────────┬─────────┘
                             │
                             ▼
                    ┌──────────────────┐
                    │      Login       │
                    └────────┬─────────┘
                             │
              ┌──────────────┼──────────────┐
              │              │              │
              ▼              ▼              ▼
          Admin          Student         Faculty
              │              │              │
              ▼              ▼              ▼
       Admin Dashboard  Student Dashboard Faculty Dashboard
                             │              │
                             │              │
                             ▼              ▼
                       Apply for TA    Recruit TA
                             │              │
                             └──────┬───────┘
                                    ▼
                              TA Application
                                    │
                                    ▼
                              Pending / Accepted /
                                 Rejected
```

---

## 🔒 Session & Logout

The application uses PHP sessions for authentication and role management.

The logout functionality destroys the current session and redirects the user to the login page.

Role-based pages also perform session checks before allowing access. For example, the committee role-selection page verifies that a user is logged in and has committee privileges.

---

## 🎯 Project Objectives

The main objectives of the system are to:

* Digitize the TA recruitment process
* Provide a centralized TA application platform
* Allow students to apply for TA opportunities
* Allow faculty members to recruit TAs
* Allow administrators to manage the system
* Implement role-based access
* Maintain TA applications in a relational database
* Reduce manual TA recruitment procedures

---

## 🔮 Future Improvements

Possible future improvements include:

* Password hashing using `password_hash()`
* Password reset functionality
* Email notifications
* Advanced application filtering
* Faculty application review interface
* TA assignment tracking
* Application deadline management
* Improved admin reporting
* Dashboard statistics and charts
* File validation for resumes
* Improved security and input validation
* Responsive UI improvements
* Deployment to a production server

---

## 👨‍💻 Development

This project was developed as an **East West University Teaching Assistant Recruitment and Management System**.

### Core Technologies

```text
PHP
MySQL
HTML
CSS
JavaScript
XAMPP
```

---

## 📄 License

This project is intended for educational and academic purposes.

---

## 🙏 Acknowledgment

Developed for **East West University** to demonstrate the design and implementation of a web-based Teaching Assistant recruitment and management system.
