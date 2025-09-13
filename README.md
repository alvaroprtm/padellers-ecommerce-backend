# Padellers E-Commerce Backend

A Laravel REST API for a multi-supplier e-commerce platform enabling suppliers to manage products and customers to shop across multiple suppliers.

Front-end code: https://github.com/alvaroprtm/padellers-ecommerce-frontend 

## Tech Stack

- **Laravel 12.x** with PHP 8.2+
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission
- **Database**: MySQL/PostgreSQL/SQLite

## Features

### Core Functionality
- **User Management**: Registration, authentication, role-based access (Admin, Supplier, Customer)
- **Product Management**: CRUD operations with supplier ownership
- **Order System**: Cart to order conversion, status tracking, order history
- **Supplier Dashboard**: View orders containing their products

### Security & Performance
- API rate limiting (60 requests/minute)
- Input validation and authorization
- Consistent JSON API responses

## Quick Start

### Prerequisites
- PHP 8.2+, Composer, Database
- DBngin (for local development)

### Installation

1. **Clone and setup**
   ```bash
   git clone https://github.com/alvaroprtm/padellers-ecommerce-backend.git
   cd padellers-back
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

2. **Configure database**
   Edit `.env` with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=padellers_db
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

3. **Initialize database and start server**
   ```bash
   php artisan migrate
   php artisan db:seed
   php artisan serve
   ```

The API will be available at `http://localhost:8000`

## Database Schema

| Table | Key Fields |
|-------|------------|
| **users** | id, name, email, password |
| **products** | id, user_id (supplier), name, description, price, image_url |
| **orders** | id, user_id (customer), price, status |
| **order_items** | id, order_id, product_id, quantity, price |

**Roles**: admin, supplier, user (customer)

## User Roles

- **Admin**: Full system access
- **Supplier**: Manage own products, view related orders
- **Customer**: Browse products, manage own orders

## Testing

```bash
php artisan test
```

## Development Notes

**Current Implementation:**
- Basic image URL storage
- Simple order status management
- Essential security features

**Future Enhancements:**
- File upload system
- Email notifications
- Payment gateway integration
- Advanced search and filtering
- API documentation
