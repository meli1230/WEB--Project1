# Women TechPower - Web Management System  

## Overview  
This project is a **PHP-based web application** for managing **events, job postings, mentorship programs, and user accounts**. It includes **CRUD operations, authentication, and structured data handling** for an interactive and secure experience.  

## Features  

### **User Management**  
- **User authentication** (Login/Logout).  
- **Account management** with user details.  

### **Event Management**  
- **Create, update, delete, and view events**.  
- **Detailed event pages** with descriptions.  

### **Job Postings**  
- **Add, edit, and delete job opportunities**.  
- **View job details and download related materials**.  

### **Mentorship Program**  
- **Add and manage mentorship opportunities**.  
- **Structured mentorship details with user interactions**.  

### **Data Management & Security**  
- **CRUD operations** for all entities (events, jobs, mentorships, members).  
- **User authentication & role-based access control**.  
- **Error handling & validation** for secure operations.  

## **Project Structure**  
account_details.php → Manages user accounts.
add_event.php, add_job.php, add_mentorship.php → Adds new records.
edit_event.php, edit_job.php, edit_mentorship.php → Modifies existing records.
delete_event.php, delete_job.php, delete_mentorship.php → Removes records securely.
details_event.php, details_job.php, details_mentorship.php → Displays detailed views.
download.php → Handles file downloads.
## **How to Run**  

### **1. Clone the Repository**  

git clone https://github.com/YOUR_USERNAME/YOUR_REPOSITORY.git
cd YOUR_REPOSITORY
2. Set Up a Local Server
Install XAMPP or WAMP.
Place the project inside the htdocs folder.
Start Apache and MySQL services.
3. Import the Database
Open phpMyAdmin (http://localhost/phpmyadmin).
Create a new database (e.g., women_techpower).
Import the provided .sql file into the database.
4. Run the Application
Access the project in a browser:
sh
Copy
Edit
http://localhost/women_techpower


Technologies Used
PHP (Backend)
MySQL (Database)
HTML, CSS, JavaScript (Frontend)
XAMPP/WAMP (Local Server)
