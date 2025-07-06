# Citruse Database Design

## Overview
This document outlines the current database design for the Citruse Fruit Management System, designed to handle citrus fruit operations across South Africa, Mauritius, Mozambique, and Tanzania.

## Design Principles
- **Scalability**: Designed to grow with the business
- **Data Integrity**: Strong relationships and constraints
- **Audit Trail**: Created/updated timestamps on all entities
- **Performance**: Indexed for common query patterns

## Entity Relationship Overview

```
Users (1) ←→ (∞) PurchaseOrders (created_by)
Suppliers (1) ←→ (∞) PurchaseOrders
Distributors (1) ←→ (∞) PurchaseOrders
Products (1) ←→ (∞) ProductPrices
Products (1) ←→ (∞) PurchaseOrderItems
PurchaseOrders (1) ←→ (∞) PurchaseOrderItems
PurchaseOrders (1) ←→ (1) PurchaseOrders (linked_po_id)
```

## Current Database Schema

### 1. Users
**Purpose**: System user authentication and authorization

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY | Auto-incrementing unique identifier |
| name | VARCHAR(255) | NOT NULL | Full name |
| email | VARCHAR(255) | NOT NULL, UNIQUE | Email address |
| role | ENUM | NOT NULL | system_administrator, purchasing_manager, field_sales_associate |
| email_verified_at | TIMESTAMP | NULL | Email verification timestamp |
| password | VARCHAR(255) | NOT NULL | Hashed password |
| remember_token | VARCHAR(100) | NULL | Remember me token |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

**Indexes**:
- PRIMARY KEY on (id)
- UNIQUE INDEX on (email)

---

### 2. Suppliers
**Purpose**: Supplier/grower information management

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY | Auto-incrementing unique identifier |
| business_name | VARCHAR(255) | NOT NULL | Company name |
| registered_address | TEXT | NOT NULL | Business address |
| country | VARCHAR(255) | NOT NULL | Country |
| vat_number | VARCHAR(255) | NOT NULL | VAT registration number |
| primary_sales_contact_name | VARCHAR(255) | NOT NULL | Sales contact name |
| primary_sales_contact_email | VARCHAR(255) | NOT NULL | Sales contact email |
| primary_sales_contact_telephone | VARCHAR(255) | NOT NULL | Sales contact phone |
| primary_logistics_contact_name | VARCHAR(255) | NOT NULL | Logistics contact name |
| primary_logistics_contact_email | VARCHAR(255) | NOT NULL | Logistics contact email |
| primary_logistics_contact_telephone | VARCHAR(255) | NOT NULL | Logistics contact phone |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

**Indexes**:
- PRIMARY KEY on (id)

---

### 3. Distributors
**Purpose**: Distributor/customer information management

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY | Auto-incrementing unique identifier |
| business_name | VARCHAR(255) | NOT NULL | Company name |
| registered_address | TEXT | NOT NULL | Business address |
| country | VARCHAR(255) | NOT NULL | Country |
| vat_number | VARCHAR(255) | NOT NULL | VAT registration number |
| primary_sales_contact_name | VARCHAR(255) | NOT NULL | Sales contact name |
| primary_sales_contact_email | VARCHAR(255) | NOT NULL | Sales contact email |
| primary_sales_contact_telephone | VARCHAR(255) | NOT NULL | Sales contact phone |
| primary_logistics_contact_name | VARCHAR(255) | NOT NULL | Logistics contact name |
| primary_logistics_contact_email | VARCHAR(255) | NOT NULL | Logistics contact email |
| primary_logistics_contact_telephone | VARCHAR(255) | NOT NULL | Logistics contact phone |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

**Indexes**:
- PRIMARY KEY on (id)

---

### 4. Products
**Purpose**: Citrus fruit product catalog

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY | Auto-incrementing unique identifier |
| product_code | VARCHAR(255) | NOT NULL, UNIQUE | Product SKU/code |
| description | VARCHAR(255) | NOT NULL | Product description |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

**Indexes**:
- PRIMARY KEY on (id)
- UNIQUE INDEX on (product_code)

---

### 5. Product_Prices
**Purpose**: Product pricing by year

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY | Auto-incrementing unique identifier |
| product_id | BIGINT | NOT NULL, FOREIGN KEY | Reference to products.id |
| year | INTEGER | NOT NULL | Pricing year |
| price_per_kg | DECIMAL(10,2) | NOT NULL | Price per kilogram |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

**Constraints**:
- FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
- UNIQUE(product_id, year) - One price per product per year

**Indexes**:
- PRIMARY KEY on (id)
- UNIQUE INDEX on (product_id, year)

---

### 6. Purchase_Orders
**Purpose**: Core purchase order management

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY | Auto-incrementing unique identifier |
| po_number | VARCHAR(255) | NOT NULL, UNIQUE | Auto-generated PO number |
| type | ENUM | NOT NULL | distributor_order, supplier_order |
| supplier_id | BIGINT | NULL, FOREIGN KEY | Reference to suppliers.id |
| distributor_id | BIGINT | NULL, FOREIGN KEY | Reference to distributors.id |
| status | ENUM | NOT NULL | Order status (see values below) |
| linked_po_id | BIGINT | NULL, FOREIGN KEY | Reference to linked PO |
| created_by | BIGINT | NOT NULL, FOREIGN KEY | Reference to users.id |
| notes | TEXT | NULL | Order notes |
| required_delivery_date | DATE | NULL | Required delivery date |
| total_value_ex_vat | DECIMAL(12,2) | DEFAULT 0 | Total order value |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

**Status Values**:
- `new`, `accepted_by_supplier`, `in_delivery`, `delivered`, `rejected_by_supplier`, `rejected_by_distributor`, `cancelled`

**Constraints**:
- FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
- FOREIGN KEY (distributor_id) REFERENCES distributors(id) ON DELETE CASCADE
- FOREIGN KEY (linked_po_id) REFERENCES purchase_orders(id) ON DELETE SET NULL
- FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE

**Indexes**:
- PRIMARY KEY on (id)
- UNIQUE INDEX on (po_number)
- INDEX on (type, status)
- INDEX on (supplier_id, status)
- INDEX on (distributor_id, status)
- INDEX on (created_by)

---

### 7. Purchase_Order_Items
**Purpose**: Line items for purchase orders

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY | Auto-incrementing unique identifier |
| purchase_order_id | BIGINT | NOT NULL, FOREIGN KEY | Reference to purchase_orders.id |
| product_id | BIGINT | NOT NULL, FOREIGN KEY | Reference to products.id |
| quantity_kg | DECIMAL(10,2) | NOT NULL | Ordered quantity in kg |
| required_delivery_date | DATE | NOT NULL | Required delivery date |
| unit_price_ex_vat | DECIMAL(10,2) | NOT NULL | Unit price excluding VAT |
| total_value_ex_vat | DECIMAL(12,2) | NOT NULL | Line total excluding VAT |
| delivered_quantity_kg | DECIMAL(10,2) | NULL | Actually delivered quantity |
| actual_delivery_date | DATE | NULL | Actual delivery date |
| quality_status | ENUM | DEFAULT 'pending' | pending, accepted, rejected |
| quality_notes | TEXT | NULL | Quality assessment notes |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

**Constraints**:
- FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE
- FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE

**Indexes**:
- PRIMARY KEY on (id)
- INDEX on (purchase_order_id, product_id)
- INDEX on (required_delivery_date)
- INDEX on (quality_status)

---

### 8. Supporting Tables

#### Password Reset Tokens
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| email | VARCHAR(255) | PRIMARY KEY | Email address |
| token | VARCHAR(255) | NOT NULL | Reset token |
| created_at | TIMESTAMP | NULL | Token creation time |

#### Sessions
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | VARCHAR(255) | PRIMARY KEY | Session ID |
| user_id | BIGINT | NULL, FOREIGN KEY | Reference to users.id |
| ip_address | VARCHAR(45) | NULL | User's IP address |
| user_agent | TEXT | NULL | User's browser/client |
| payload | LONGTEXT | NOT NULL | Session data |
| last_activity | INTEGER | NOT NULL | Last activity timestamp |

**Indexes**:
- PRIMARY KEY on (id)
- INDEX on (user_id)
- INDEX on (last_activity)

---

## Business Logic Notes

### Purchase Order Workflow
1. **Distributor Orders (POD)**: Created when distributors place orders
2. **Supplier Orders (POS)**: Created in response to distributor orders
3. **Linking**: POS can be linked to POD via `linked_po_id`
4. **Status Flow**: new → accepted_by_supplier → in_delivery → delivered

### Product Pricing
- One price per product per year
- Prices are stored in the `product_prices` table
- Current year pricing is used for new orders

### Quality Control
- Each purchase order item has quality status tracking
- Quality notes can be added during delivery inspection
- Quality status affects order completion

## Future Enhancements

The current schema provides a solid foundation and can be extended with:
- Additional product attributes (variety, grade, packaging)
- Supplier performance metrics
- Contract management
- Audit logging
- Multi-currency support
- Document attachments
- Notification system