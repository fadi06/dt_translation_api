# 🈯 Translation API

A powerful and scalable API-driven translation system built with Laravel. Designed to handle large volumes of translations across multiple locales with features like tagging, searching, and exporting for frontend applications such as Vue.js or React.

---

## 🚀 Key Features

- **Multi-Locale Translation**  
  Easily manage translations across multiple locales (e.g., `en`, `fr`, `de`) with the flexibility to add new languages anytime.

- **Tag-Based Organization**  
  Categorize translations using context-based tags (like `mobile`, `dashboard`, `marketing`) for better filtering and management.

- **CRUD Operations**  
  Fully functional endpoints to Create, Read, Update, and Delete translation records.

- **Search & Filter Support**  
  Search translations by keys, values, tags, and locales with blazing-fast performance.

- **Frontend Export (JSON)**  
  Export translations to JSON files tailored for frontend consumption—ideal for Vue.js or similar frameworks.

- **High Performance**  
  Optimized to handle 100,000+ records with advanced caching, indexed querying, and asynchronous job support.

- **Secure Access**  
  Token-based authentication using Laravel Sanctum for secure API consumption.

- **Dockerized Setup**  
  Comes with Docker configuration for hassle-free local development.

- **Test Coverage**  
  Comprehensive test suite covering all core functionalities (95%+ coverage).

---

## 📚 API Endpoints Overview

### 📌 Create Translation
```http
POST /api/translations
```

### ✏️ Update Translation
```http
PUT /api/translations/{id}
```

### 🌍 Get Translations by Locale
```http
GET /api/translations/{locale}
```

### 🔑 Get Translation by Key or ID
```http
GET /api/translations/{identifier}
```

### 📦 Export Translations
```http
GET /api/translations/export
```

### 🔍 Search Translations
```http
GET /api/translations/search?query={keyword}
```

## 🛠️ Requirements
        Composer
        Docker & Docker Compose
## 🧪 Installation Guide

    1. Clone the Repository
        a. git clone https://github.com/your-username/translation-api.git
        b. cd translation-api/.docker

    2. Install Docker
        To install Docker, please follow the instructions in this [File](./.docker/README.md)
        Make sure to complete all the steps outlined there.

## ✅ Final Step

    After completing all the steps, your project will be accessible at: [URL](http://localhost:6266). 