# Padellers E-Commerce Backend (Laravel API)

A Laravel-based REST API for a multi-supplier e-commerce platform that enables suppliers to manage products and customers to shop across multiple suppliers.

## Project Overview

This is the backend API for an e-commerce platform with two main user types:
- **Suppliers**: Can register, manage their products, and view orders containing their products
- **Customers**: Can browse products from all suppliers, add to cart, and purchase items

## Tech Stack

- **Framework**: Laravel 12.x
- **Authentication**: Laravel Sanctum (API tokens)
- **Authorization**: Spatie Laravel Permission (role-based)
- **Database**: MySQL/PostgreSQL/SQLite
- **PHP Version**: 8.2+

## Features Implemented

### Authentication & Authorization
- User registration and login
- JWT-like token authentication using Laravel Sanctum
- Role-based access control (Admin, Supplier, Customer)
- Permission-based feature access

### User Management
- User registration with role assignment
- Profile management
- Role-based dashboard access

### Product Management
- **CRUD Operations**: Create, Read, Update, Delete products
- **Supplier Ownership**: Only product owners can edit/delete their products
- **Product Attributes**: Name, description, price, image URL
- **Supplier Association**: Products linked to their supplier

### Order System
- **Cart to Order Conversion**: Transform cart items into orders
- **Order Items**: Detailed breakdown of purchased products
- **Order Status Tracking**: pending, paid, shipped, completed, cancelled
- **Price Snapshot**: Captures product prices at time of purchase
- **Order History**: Users can view their purchase history

### Supplier Order Management
- **Supplier Orders View**: Suppliers can see orders containing their products
- **Customer Information**: Access to buyer details for orders
- **Order Filtering**: Only shows orders relevant to supplier's products

## Database Schema

### Users Table
- `id`, `name`, `email`, `password`
- `email_verified_at`, `remember_token`
- Timestamps

### Products Table
- `id`, `user_id` (supplier), `name`, `description` 
- `price` (decimal), `image_url`
- Timestamps

### Orders Table
- `id`, `user_id` (customer), `price`, `status`
- Timestamps

### Order Items Table
- `id`, `order_id`, `product_id`, `quantity`, `price`
- Timestamps

### Roles & Permissions Tables (Spatie Package)
- Role-based permission system
- Three roles: admin, supplier, user (customer)


## Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- Database (MySQL/PostgreSQL/SQLite)
- DBngin (for local development) 

### Installation Steps

1. **Clone the repository**
   ```bash artisan key:generate
   ```

4. **Database configuration**
   Edit `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=padellers_db
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000`

## Key Implementation Details

### Role-Based Permissions
- **Admin**: Full access to all features
- **Supplier**: Can manage own products and view related orders
- **Customer**: Can browse products and manage own orders

### Security Features
- API rate limiting (60 requests per minute)
- CSRF protection for web routes
- Input validation on all endpoints
- Owner-based authorization for products and orders

### Database Relationships
- User → Products (One-to-Many)
- User → Orders (One-to-Many) 
- Order → OrderItems (One-to-Many)
- Product → OrderItems (One-to-Many)

### API Response Format
All API responses follow a consistent JSON format:
```json
{
  "data": {},
  "message": "Success message",
  "status": 200
}
```

## Testing

Run the test suite:
```bash
php artisan test
```

Test files are located in:
- `tests/Feature/` - Integration tests
- `tests/Unit/` - Unit tests

## Additional Notes

### Implemented Shortcuts & Future Improvements
Given the 6-8 hour time constraint, the following shortcuts were taken:

**Current Implementation:**
- Basic image URL storage (no file upload handling)
- Simple order status management
- Basic error handling

**Future Enhancements:**
- File upload system for product images
- Advanced order status workflow
- Email notifications for orders
- Inventory management
- Payment gateway integration
- Advanced search and filtering
- API documentation with Swagger
- Comprehensive test coverage
- Caching layer for better performance

### Development Environment
- Uses DBngin for local database management
- Laravel Breeze for authentication scaffolding
- Spatie Permission package for role management
