# Marketplace API

This is a RESTful API built with Laravel 12 as a backend solution for a marketplace platform. It includes user authentication, role-based authorization, employee management, benefit redemption using credits, and gift card generation.

## 🔧 Tech Stack

- Laravel 12
- Sanctum for API authentication
- PHPUnit for testing
- Laravel Policies for authorization
- Form Requests for validation

## 🧠 Domain

The system allows companies to manage employees, and enable them to redeem benefits with credits (such as gift cards) based on available variations. Users have different roles and permissions.

### Key Entities

- **User**: Authenticated entity (Admin, Company Admin, or Employee).
- **Company**: Associated with Company Admins and Employees.
- **Employee**: User profile for employee-type users, holding credits.
- **Brand**: Brand linked to benefits.
- **Benefit**: A benefit that can be redeemed (e.g., medical insurance).
- **Variation**: Different price/cost versions of a benefit.
- **GiftCard**: Generated when a variation is redeemed by an employee.
- **Order**: Records a redemption transaction.

## 🔐 Authentication & Authorization

Authentication is handled via **Sanctum**. Users must be authenticated to interact with protected routes.

Authorization is enforced using **Laravel Policies**, which define access control per role:
- `Admin`: Full access
- `Admin_Company`: Can manage resources within their company
- `User_Company` (Employee): Can only access their own data

## ✅ Example Endpoints

- `POST /api/login`: Authenticate user
- `GET /api/employees`: List employees (filtered by company for Admin_Company)
- `POST /api/variations/{variation}/redeem`: Redeem a benefit using credits
- `POST /api/users`: Create user (restricted by role)

## 🚀 Setup Instructions

```bash
git clone https://github.com/nachoyegro/marketplace-api.git
cd marketplace-api

composer install

cp .env.example .env
php artisan key:generate

# Set up your DB connection in .env (SQLite is preconfigured for testing)

php artisan migrate --seed

php artisan serve
```

## 🧪 Tests

The application includes feature tests covering:
- Role-based access to endpoints
- Gift card generation
- Credit deduction upon redemption
- Authorization logic

Run tests with:

```bash
php artisan test
```

## 📂 Project Structure Highlights


- `app/Models`: Model definitions
- `app/Policies`: Authorization logic
- `app/Services`: Encapsulates reusable domain logic outside of models
- `app/Http/Requests`: Form validation
- `app/Http/Controllers`: Business logic for each resource
- `tests/Feature`: High-level endpoint tests
