# 🧾 ORDER_QUEUE_KITCHEN_THROTTLING

A **Slim 3-based REST API** built in **PHP 7.3**, demonstrating clean code architecture (MVC + SOLID principles), DTOs, validators, repository & service layers, and MySQL integration.

---

## 📁 Project Structure

Below is the overall folder layout (same as shown in your project explorer screenshot):


ORDER_QUEUE_KITCHEN_THROTTLING/
│
├─ .vscode/
│   └─ settings.json
│
├─ logs/
│   └─ app.log
│
├─ sql/
│   └─ migrations.sql             # MySQL schema
│
├─ src/
│   ├─ Controller/
│   │   └─ OrderController.php
│   ├─ Db/
│   │   └─ PDOSingleton.php
│   ├─ DTO/
│   │   ├─ OrderRequest.php
│   │   └─ OrderResponse.php
│   ├─ Http/
│   │   └─ Exceptions/
│   │       └─ HttpException.php
│   ├─ Middleware/
│   │   └─ AuthMiddleware.php
│   ├─ Model/
│   │   └─ OrderModel.php
│   ├─ Repository/
│   │   ├─ OrderRepository.php
│   │   └─ OrderRepositoryInterface.php
│   ├─ Service/
│   │   ├─ OrderService.php
│   │   └─ OrderServiceInterface.php
│   ├─ Validator/
│   │   └─ OrderValidator.php
│   ├─ Worker/
│   │   └─ auto_complete_worker.php
│   ├─ bootstrap.php
│   ├─ middleware.php
│   ├─ routes.php
│   ├─ Settings.php
│
├─ templates/
│   └─ index.phtml
│
├─ vendor/                       # Pre-installed dependencies (keep committed)
│
├─ .htaccess
├─ composer.json
├─ composer.lock
├─ CONTRIBUTING.md
├─ docker-compose.yml
├─ index.php                      # Application entry point
├─ phpunit.xml
└─ README.md


---

## ⚙️ Prerequisites

| Requirement        | Version / Details                              |
| ------------------ | ---------------------------------------------- |
| **PHP**            | 7.3 .x                                         |
| **Web Server**     | Apache 2.4 (XAMPP/WAMP) or PHP built-in server |
| **Database**       | MySQL 5.7 / 8 .x                               |
| **Slim Framework** | 3 .x (already included under `vendor/`)        |

Since the `vendor` folder is committed, you **don’t need to run composer install**.

---

## 🗄️ Database Setup

1. Open your MySQL console or phpMyAdmin.

2. Run the script in [`sql/migrations.sql`](./sql/migrations.sql):

   sql
   CREATE DATABASE IF NOT EXISTS restaurant;
   USE restaurant;

   CREATE TABLE IF NOT EXISTS orders (
       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       items JSON NOT NULL,
       vip TINYINT(1) NOT NULL DEFAULT 0,
       status ENUM('active','completed') NOT NULL DEFAULT 'active',
       created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       pickup_time DATETIME NULL,
       completed_at DATETIME NULL
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   

3. Update your DB credentials inside **`src/Settings.php`** (look for the `settings['db']` array).

---

## 🚀 How to Run the Project

### Option 1 – Using Apache (XAMPP/WAMP)

1. Copy the entire folder `ORDER_QUEUE_KITCHEN_THROTTLING` into:

   
   C:\xampp\htdocs\
   
2. Ensure your Apache service is running.
3. Enable rewrite module in Apache (`mod_rewrite` must be on).
4. Check `.htaccess` file inside the project root (it already routes all requests to `index.php`).
5. Access the API at:

   
   http://localhost/order_queue_kitchen_throttling/
   

---

### Option 2 – Using PHP Built-in Server

From project root:

bash
php -S localhost:8000 -t .


Then access:


http://localhost:8000/orders/active


---

## 🔌 API Endpoints

### 1️⃣ Get Active Orders

**GET**


http://localhost/order_queue_kitchen_throttling/orders/active


**Response:**

json
{
  "data": [
    {
      "id": 1,
      "items": ["pizza","burger"],
      "VIP": false,
      "status": "active",
      "created_at": "2025-10-30 10:20:00",
      "pickup_time": "2025-10-30T12:30:00Z",
      "completed_at": null
    }
  ]
}


---

### 2️⃣ Create New Order

**POST**


http://localhost/order_queue_kitchen_throttling/orders


**Request Body (Normal):**

json
{
  "items": ["coke", "garlic bread"],
  "pickup_time": "2025-10-30T12:30:00Z"
}


**Request Body (VIP):**

json
{
  "items": ["coke", "garlic bread"],
  "pickup_time": "2025-10-30T12:30:00Z",
  "VIP": true
}


**Success Response:**

json
{
  "data": {
    "id": 2,
    "items": ["coke", "garlic bread"],
    "VIP": false,
    "status": "active",
    "created_at": "2025-10-30 11:00:00",
    "pickup_time": "2025-10-30T12:30:00Z",
    "completed_at": null
  }
}


**If kitchen is full (non-VIP):**

json
{
  "error": "Kitchen is full"
}


---

### 3️⃣ Mark Order as Completed

**POST**


http://localhost/order_queue_kitchen_throttling/orders/{id}/complete


**Example:**


http://localhost/order_queue_kitchen_throttling/orders/5/complete


**Response:**

json
{ "message": "Order marked completed" }


---

## 🧩 Internal Architecture

| Layer          | Description                                                 |
| -------------- | ----------------------------------------------------------- |
| **Controller** | Handles incoming requests and returns responses             |
| **Service**    | Business logic (throttling, VIP handling, order completion) |
| **Repository** | Database operations (CRUD on orders)                        |
| **Model**      | Represents data entity (`OrderModel`)                       |
| **DTO**        | Request/response mapping objects                            |
| **Validator**  | Input validation for incoming payloads                      |
| **Middleware** | (Optional) for authentication, logging                      |
| **Worker**     | Background logic or cron jobs                               |
| **Settings**   | Configuration for Slim app, DB, logger, etc.                |

---

## 🧱 Key Concepts Used

* **SOLID Principles**

  * Single Responsibility → Separate layers for controller, service, repo
  * Dependency Inversion → Interfaces (`OrderRepositoryInterface`, `OrderServiceInterface`)
* **Design Patterns**

  * Repository Pattern
  * Singleton Pattern (for PDO)
  * DTO (Request / Response)
  * Service Layer
* **Error Handling**

  * Custom `HttpException`
  * Centralized JSON response
* **Validation**

  * `OrderValidator` ensures correct data before saving
* **Logging**

  * Logs stored in `logs/app.log`
* **Database**

  * Uses PDO Singleton connection (`PDOSingleton.php`)

---

## 📜 Example Logs

All API logs and exceptions are written to:


/logs/app.log


---

## 🧪 Testing

Use Postman or `curl`.

Example:

bash
curl -X POST http://localhost/order_queue_kitchen_throttling/orders \
  -H "Content-Type: application/json" \
  -d '{"items":["coke","garlic bread"],"pickup_time":"2025-10-30T12:30:00Z"}'


Goto project folder order_queue_kitchen_throttling
cd order_queue_kitchen_throttling/
php src/Worker/auto_complete_worker.php             # This script is to mark existing active orders mark as completed after certain seconds


**Tech Stack:**

* PHP 7.3
* Slim Framework 3.x
* MySQL 5.7/8.x
  

‍💻 Author
Vijaya Durga Prasanna
Senior Software Engineer — PHP | AWS | Angular | Full Stack
LinkedIn: https://www.linkedin.com/in/durgakota-seniorfullstackengineer/                                   |
