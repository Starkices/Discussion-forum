 # School Discussion Forum

School Discussion Forum is a web-based discussion forum/hub built with Raw **PHP** and **MySQL** that provides schools with a dedicated space for students to discuss, join groups, communicate, access resources, and view FAQs in a  user-friendly interface, with student access controlled by school administrators.

The system is designed for schools, faculties, or educational communities that want to provide their students with a dedicated online space for discussions, group communication, resources, and frequently asked questions.

It is inspired by the community and discussion experience of platforms such as Facebook and the group communication concept of WhatsApp, but is designed specifically around a school community.

> This project was built before I began learning Laravel and represents my foundation in backend web development. I plan to rebuild it in Laravel as a more scalable and production-ready application.

[View Live Site](https://starkices-dforum.freedev.app)

---

## 📌 Project Overview

The School Discussion Forum is **not a school management system**.

Its primary purpose is to provide a centralized **discussion and communication hub for students within a school or educational institution**.

Instead of students relying entirely on external platforms such as WhatsApp or Facebook for school-related discussions, the platform provides a dedicated environment where members of a school community can communicate and access useful information.

The system has two main sides:

- **School/Faculty Administration**
- **Student Platform**

The school or authorized faculty administrators control student access to the platform, while students use the platform for discussions, groups, communication, resources, and other community activities.

---

## 🏫 How the System Works

Students do not simply visit the platform and create an account through public registration.

The intended process is controlled by the school.

### Student Account Creation

1. A student is admitted into the school.
2. The student provides their email address during the school's admission or registration process.
3. An authorized school/faculty administrator logs into the administrative section.
4. The administrator adds the student's information and email address.
5. The system creates an account for the student.
6. The student receives an email containing their account credentials.
7. The student uses the provided email and password to log into the platform.
8. After logging in, the student can change their password and edit their profile.

This approach allows the school to control who is allowed to join its online student community.

---

# 👥 User Types

## 🏫 School / Faculty Administrators

The administrative section is intended for authorized school or faculty personnel.

Administrators can use the administrative dashboard to manage student access to the platform.

The administrator side is separate from the normal student experience.

### Administrative responsibilities include:

- Adding students to the platform
- Creating student accounts
- Managing students account
- Using the administrative dashboard
- Managing the information required for the student platform

---

## 🎓 Students

Students use the normal user-facing side of the application.

After receiving their account from the school, students can log in and use the platform as a discussion and communication hub.

Students can:

- Log into their account
- Change their password
- Edit their profile
- Participate in discussion
- Participate in groups
- Communicate through group chats
- Access available resources
- View frequently asked questions (FAQ)
- Interact with other members of their school community

---

# 💬 Discussion Forum

The central purpose of the application is discussion.

Students have a dedicated environment where they can participate in conversations within their school community.

The forum is intended to provide a structured alternative to having all school-related discussions scattered across unrelated social media platforms.

---

# 👥 Groups & Group Chat

The platform includes groups that allow students to participate in more focused communities.

Groups can be used for communities within the school, depending on how the institution chooses to organize them.

The group communication experience is inspired by the simplicity of group conversations found on applications such as WhatsApp.

---

# 📚 Resources

The platform also provides a place for students to access resources made available through the system.

These resources are intended to give students useful information without requiring them to search through multiple communication channels.

---

# ❓ Frequently Asked Questions

The platform includes a **Frequently Asked Questions (FAQ)** section.

This provides students with a centralized place to find answers to common questions and information relevant to the school community.

---

# 🔐 Account & Profile

Students receive their accounts through the school/faculty administration system rather than creating accounts through unrestricted public registration.

After receiving their credentials, students can access their account and manage aspects of their profile.

Students can:

- Log in using their assigned credentials
- Change their password
- Edit their profile
- And lots more...

---

## Features

- User registration and authentication
- Secure login and logout
- Create discussion posts
- Comment on discussions
- Chat on Group
- User profile management
- Admin section
- Responsive interface

---

## Tech Stack

- PHP (Core PHP)
- MySQL
- HTML5
- CSS3
- JavaScript
- XAMPP (Development Environment)

---

## Database

Database Name: discussion_forum

---

## Installation

1. Clone the repository.
```bash
git clone https://github.com/Starkices/discussion-forum.git
```
2. Move the project into your web server directory.
3. Create a MySQL database named:
```
discussion_forum
```
4. Import the SQL database. [text](discussion_forum.sql)
5. Configure your database connection.
6. Start Apache and MySQL.
7. Visit:

### Student login
```
http://localhost/discussion-forum

```
- Username - johndoe
- password - dlaw12345

### Administrator login
```
http://localhost/discussion-forum/admin

```
- Username - NELLY
- password - DFnigeria01

---

## Screenshots

### Home Page

![alt text](homepage.jpg)

### Login

![alt text](loginpage.jpg)

### Registration

![alt text](registerstudent.jpg)

### Group chat

![alt text](groupchat.jpg)

### Admin Dashboard

![alt text](dashboard.jpg)

---

## Future Improvements

- Rebuild with Laravel
- REST API
- Email verification
- Notifications
- Rich text editor
- Search improvements
- Better UI/UX
- Improved security

---

## Author

**Wisdom Ogheneobrozie**

GitHub:
https://github.com/Starkices

---

## License

This project is available for learning purposes.
