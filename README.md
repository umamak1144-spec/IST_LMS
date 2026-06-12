# 📚 IST Library Management System (LMS)

A full-stack web application for managing library operations at the Institute of Science and Technology. Built with HTML, CSS, JavaScript, PHP, and MySQL.

---

## 👥 Group Members

| Name | Registration No |
|------|----------------|
| Umama Khan | 250901013 |
| Naba Rana | 250901017 |
| Noor Fatima | 250901020 |

**Subject:** Database Management Systems  
**Instructor:** Mam Shakira Musa Baig  

---

## 🚀 Features

### Admin Panel
- ✅ Secure login & registration
- ✅ Add, delete, and search books
- ✅ Assign books to students
- ✅ Mark books as returned
- ✅ Live dashboard with stats (total books, assigned, returned, students)
- ✅ View all currently issued books

### Student Panel
- ✅ Secure login with Student ID
- ✅ View currently issued books
- ✅ Full borrowing history
- ✅ Search library catalog
- ✅ Overdue book alerts
- ✅ Profile with library usage stats

---

## 🛠️ Technologies Used

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP 8.0 |
| Database | MySQL (MariaDB) |
| Server | Apache (XAMPP) |
| Security | bcrypt password hashing |
| API | Fetch API (JSON) |

---

## 🗂️ Project Structure

```
ist_lms/
├── index.html               # Welcome page
├── login.html               # Login type selector
├── signup_type.html         # Signup type selector
├── admin_login.html         # Admin login
├── admin_signup.html        # Admin registration
├── admin-dashboard.html     # Admin dashboard
├── student_login.html       # Student login
├── student_signup.html      # Student registration
├── student-dashboard.html   # Student dashboard
├── stylesheet.css           # Main stylesheet
├── database.sql             # Database setup file
└── backend/
    ├── db.php               # Database connection
    ├── admin_auth.php       # Admin login/signup API
    ├── student_auth.php     # Student login/signup API
    ├── books.php            # Books CRUD API
    ├── borrowings.php       # Borrow/Return API
    └── stats.php            # Dashboard stats API
```

---

## 🗄️ Database Schema

```
admins        → admin_id (PK), name, email, password
students      → student_id (PK), full_name, password, department
books         → book_id (PK), title, author, category, total_copies, available_copies
borrowings    → borrow_id (PK), student_id (FK), book_id (FK), borrow_date, due_date, return_date, status
```

---

## ⚙️ How to Run Locally

### Requirements
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP)

### Steps

**1. Clone the repository**
```bash
git clone https://github.com/YOUR_USERNAME/ist-lms.git
```

**2. Move to XAMPP folder**
```
Copy the ist_lms folder to:
C:\xampp\htdocs\ist_lms\
```

**3. Start XAMPP**
- Open XAMPP Control Panel
- Start **Apache**
- Start **MySQL**

**4. Setup Database**
- Open: `http://localhost/phpmyadmin`
- Create database: `ist_lms`
- Import: `database.sql`

**5. Open the project**
```
http://localhost/ist_lms/index.html
```

---

## 🔑 Default Login Credentials

| Role | ID / Email | Password |
|------|-----------|----------|
| Admin | `admin@gmail.com` | `admin123` |
| Student | `IST101` | `student123` |

---

## 📊 SQL Queries Used

```sql
-- Create tables
CREATE TABLE books (...);
CREATE TABLE students (...);
CREATE TABLE borrowings (...);

-- Assign book
INSERT INTO borrowings (student_id, book_id, due_date) VALUES (?, ?, ?);
UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ?;

-- Return book
UPDATE borrowings SET return_date = CURDATE(), status = 'returned' WHERE borrow_id = ?;
UPDATE books SET available_copies = available_copies + 1 WHERE book_id = ?;

-- Search books
SELECT * FROM books WHERE title LIKE '%keyword%' OR author LIKE '%keyword%';

-- Dashboard stats
SELECT COUNT(*) FROM books;
SELECT COUNT(*) FROM borrowings WHERE status = 'issued';
```

---

## 🔐 Security

- Passwords encrypted using **bcrypt hashing** (`password_hash()`)
- SQL Injection prevention using **PDO Prepared Statements**
- Session management using **sessionStorage**

---

## 📁 Submission Package

- ✅ Final PDF Report
- ✅ SQL Script File (`database.sql`)
- ✅ Source Code (HTML/CSS/JS/PHP)
- ✅ ER Diagram
- ✅ Relational Schema

---

## 📌 ER Diagram

[View ER Diagram](https://drive.google.com/file/d/1bbJ1pO48SQeutJv5Rx9YwCdgD0-25FM_/view?usp=sharing)

## 📌 Relational Schema

[View Relational Schema](https://drive.google.com/file/d/1w5UiyTwFmoTnOJ9EYMgkZBdbejA9_OmP/view?usp=sharing)
