# Citruse App

A comprehensive citrus fruit management system for handling operations across South Africa, Mauritius, Mozambique, and Tanzania.

## Overview

Citruse App is a Laravel-based web application designed to streamline citrus fruit trading operations. It provides complete management of suppliers, distributors, products, and purchase orders with robust reporting and analytics capabilities.

## Features

- **User Management**: Role-based access control (System Administrator, Purchasing Manager, Field Sales Associate)
- **Supplier Management**: Complete supplier profiles with performance tracking
- **Distributor Management**: Customer relationship management with contract tracking
- **Product Catalog**: Citrus-specific product management with seasonal data
- **Purchase Orders**: Dual-sided order management (distributor orders and supplier orders)
- **Pipeline Forecasting**: Order pipeline analysis and forecasting
- **Performance Analytics**: Supplier and distributor performance metrics

## Technology Stack

- **Backend**: Laravel 12 with PHP 8.2+
- **Frontend**: Vue.js 3.5+ with Inertia.js 2.0+
- **Database**: PostgreSQL
- **Styling**: Tailwind CSS 3.4+
- **Build Tool**: Vite 6.3+

## Database Design

The application uses a comprehensive PostgreSQL database with 11 main tables designed for scalability and data integrity. See [Database Design Documentation](docs/database-design.md) for detailed schema information.

## Installation

1. Clone the repository
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install Node.js dependencies:
   ```bash
   npm install
   ```
4. Copy environment file:
   ```bash
   cp .env.example .env
   ```
5. Configure your PostgreSQL database in `.env`
6. Generate application key:
   ```bash
   php artisan key:generate
   ```
7. Run database migrations:
   ```bash
   php artisan migrate
   ```
8. Seed the database:
   ```bash
   php artisan db:seed
   ```
9. Build frontend assets:
   ```bash
   npm run build
   ```

## Development

To start the development server:

```bash
# Start Laravel server
php artisan serve

# Start Vite dev server (in another terminal)
npm run dev
```

## Testing

Run the test suite:

```bash
php artisan test
```

## User Roles

- **System Administrator**: Full system access and configuration
- **Purchasing Manager**: Manage suppliers, orders, and inventory
- **Field Sales Associate**: Manage distributors and sales activities

## AI Assistance Disclosure

This project leverages AI coding assistants for enhanced productivity while maintaining human expertise and oversight. The following sections outline where AI assistance was utilized:

### AI Contributions

- **Database Design Documentation**: AI assisted in creating database schema documentation
- **Code Structure Analysis**: AI helped analyze existing codebase patterns and suggest improvements for consistency
- **Migration File Generation**: AI assisted in creating database migration files following Laravel conventions
- **API Controller Patterns**: AI helped standardize API controller structures and response formats
- **Vue Component Architecture**: AI assisted in creating consistent Vue.js component patterns and compositions
- **Documentation Generation**: AI helped generate README files and code documentation


## License

This project is licensed under the MIT License.