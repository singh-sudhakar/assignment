# 🚀 Laravel + Vue Fullstack Application (Dockerized)

This project is a full-stack web application built with **Laravel** (backend) and **Vue.js** (frontend), containerized using **Docker** for ease of deployment and sharing. The application supports API authentication with **Sanctum**, includes seeded test data, and uses MySQL and Nginx for development and testing.

---

## 📦 Features

- 🧰 Laravel backend with Sanctum-based API authentication
- 🌐 Vue 3 frontend 
- 🐳 Dockerized for easy setup and deployment
- 📊 Patient management dashboard with charts
- 🔐 Protected API with middleware and auth guards
- 📂 Automatic DB migrations and seeding on startup

---

## 🛠️ Technologies Used

- PHP 8.2 / Laravel 12.x
- Vue 3 + Vite
- Sanctum for API auth
- MySQL 8
- Docker & Docker Compose
- Nginx (web server)
- Chart.js (for charts)

---

## 🚀 Getting Started

### Prerequisites

Make sure you have the following installed:

- Docker
- Docker Compose

---

## 🧪 Quick Start (with Docker)

### 1. Clone the Repository

```bash
git clone https://github.com/singh-sudhakar/assignment.git
```

### 2. Change Directory

```bash
cd assignment
```

### 3. Build and Start the Containers

```bash
docker-compose up --build
```

## 🚪 Access the Application

Open your browser and go to:  

http://127.0.0.1:8000


## 📲 API Documentation

for api documnentation Go to:  
http://127.0.0.1:8000/api/documentation

Or upload the `patient-api.yaml` file to [Swagger](https://swagger.io) to view the API documentation for accessing api.
