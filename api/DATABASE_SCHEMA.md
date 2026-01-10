# 🗄️ POS System - Database Schema Visual Guide

## Complete Entity Relationship Diagram (ERD)

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           POS SYSTEM DATABASE                            │
└─────────────────────────────────────────────────────────────────────────┘

┏━━━━━━━━━━━━━━━━┓
┃     ROLES      ┃
┣━━━━━━━━━━━━━━━━┫
┃ id             ┃
┃ name           ┃ ← 'admin', 'cashier'
┃ permissions    ┃ ← JSON array
┃ created_at     ┃
┃ updated_at     ┃
┗━━━━━━━━┯━━━━━━━┛
         │
         │ One role has many users
         │
         ▼
┏━━━━━━━━━━━━━━━━┓
┃     USERS      ┃
┣━━━━━━━━━━━━━━━━┫
┃ id             ┃
┃ role_id        ┃ ◄── Foreign Key (roles.id)
┃ name           ┃
┃ email          ┃
┃ password       ┃
┃ is_active      ┃
┃ created_at     ┃
┃ updated_at     ┃
┗━━━━━┯━━━━━━┯━━━┛
      │      │
      │      └────────────┐
      │                   │
      │                   │ User has many Stock Movements
      │                   │
      │                   ▼
      │            ┏━━━━━━━━━━━━━━━━━━━┓
      │            ┃  STOCK_MOVEMENTS  ┃
      │            ┣━━━━━━━━━━━━━━━━━━━┫
      │            ┃ id                ┃
      │            ┃ product_id        ┃ ◄── Foreign Key
      │            ┃ type              ┃ ← 'in' or 'out'
      │            ┃ quantity          ┃
      │            ┃ reference         ┃ ← 'Sale #123', 'PO #456'
      │            ┃ notes             ┃
      │            ┃ user_id           ┃ ◄── Foreign Key (users.id)
      │            ┃ created_at        ┃
      │            ┃ updated_at        ┃
      │            ┗━━━━━━━━━━━━━━━━━━━┛
      │
      │ User has many Sales
      │
      ▼
┏━━━━━━━━━━━━━━━━━━━┓
┃      SALES        ┃
┣━━━━━━━━━━━━━━━━━━━┫
┃ id                ┃
┃ invoice_number    ┃ ← Unique (INV-20260108-001)
┃ user_id           ┃ ◄── Foreign Key (users.id) = Cashier
┃ customer_name     ┃
┃ customer_phone    ┃
┃ subtotal          ┃ ← Sum of all items
┃ tax_percentage    ┃
┃ tax_amount        ┃
┃ discount_type     ┃ ← 'percentage' or 'fixed'
┃ discount_value    ┃
┃ total_amount      ┃ ← Final amount to pay
┃ status            ┃ ← 'completed', 'pending', 'cancelled'
┃ created_at        ┃
┃ updated_at        ┃
┗━━━━━┯━━━━━━┯━━━━━━┛
      │      │
      │      │ Sale has many Payments
      │      │
      │      ▼
      │   ┏━━━━━━━━━━━━━━━━━━┓
      │   ┃    PAYMENTS      ┃
      │   ┣━━━━━━━━━━━━━━━━━━┫
      │   ┃ id               ┃
      │   ┃ sale_id          ┃ ◄── Foreign Key (sales.id)
      │   ┃ payment_method   ┃ ← 'cash', 'card', 'e-wallet'
      │   ┃ amount           ┃
      │   ┃ reference_number ┃ ← Card/E-wallet transaction ID
      │   ┃ created_at       ┃
      │   ┃ updated_at       ┃
      │   ┗━━━━━━━━━━━━━━━━━━┛
      │
      │ Sale has many Sale Items
      │
      ▼
┏━━━━━━━━━━━━━━━━━━━┓
┃   SALE_ITEMS      ┃
┣━━━━━━━━━━━━━━━━━━━┫
┃ id                ┃
┃ sale_id           ┃ ◄── Foreign Key (sales.id)
┃ product_id        ┃ ◄── Foreign Key (products.id)
┃ quantity          ┃
┃ unit_price        ┃ ← Price at time of sale
┃ subtotal          ┃ ← quantity × unit_price
┃ created_at        ┃
┃ updated_at        ┃
┗━━━━━━━━━━━━━━━━━━━┛
         │
         │ Sale Item belongs to Product
         │
         ▼
┏━━━━━━━━━━━━━━━━━━━┓           ┏━━━━━━━━━━━━━━━━━┓
┃    PRODUCTS       ┃           ┃   CATEGORIES    ┃
┣━━━━━━━━━━━━━━━━━━━┫           ┣━━━━━━━━━━━━━━━━━┫
┃ id                ┃           ┃ id              ┃
┃ category_id       ┃ ◄─────────┃ name            ┃
┃ name              ┃           ┃ description     ┃
┃ barcode           ┃           ┃ is_active       ┃
┃ sku               ┃           ┃ created_at      ┃
┃ description       ┃           ┃ updated_at      ┃
┃ cost_price        ┃           ┗━━━━━━━━━━━━━━━━━┛
┃ selling_price     ┃
┃ image             ┃
┃ is_active         ┃
┃ created_at        ┃
┃ updated_at        ┃
┗━━━━━━━━┯━━━━━━━━━━┛
         │
         │ Product has one Inventory
         │
         ▼
┏━━━━━━━━━━━━━━━━━━━┓
┃   INVENTORIES     ┃
┣━━━━━━━━━━━━━━━━━━━┫
┃ id                ┃
┃ product_id        ┃ ◄── Foreign Key (products.id) UNIQUE
┃ quantity          ┃ ← Current stock
┃ min_stock_level   ┃ ← Alert threshold
┃ created_at        ┃
┃ updated_at        ┃
┗━━━━━━━━━━━━━━━━━━━┛
```

---

## 📊 Relationship Types Explained

### **1:Many (One-to-Many)**
```
1 Role → Many Users
1 Category → Many Products
1 Product → Many Sale Items
1 Sale → Many Sale Items
1 Sale → Many Payments
1 User → Many Sales
```

### **1:1 (One-to-One)**
```
1 Product → 1 Inventory
```

---

## 🔄 Data Flow Example: Complete Sale Transaction

```
STEP 1: Cashier Logs In
┌──────────┐
│  LOGIN   │
└────┬─────┘
     │ Check credentials
     ▼
┌─────────────┐
│ USER Table  │ ← Find user by email
└─────────────┘
     │
     ▼
┌─────────────┐
│ ROLE Table  │ ← Check permissions
└─────────────┘
     │
     ▼
 Generate Token ✅


STEP 2: Add Products to Cart (Frontend Only)
┌──────────────┐
│ Search       │ → Type "MacBook"
└──────┬───────┘
       │
       ▼
┌─────────────────┐
│ PRODUCTS Table  │ ← Search by name/barcode
└─────────────────┘
       │
       ▼
  Add to Cart
  [MacBook Pro × 1]
  [Mouse × 2]


STEP 3: Process Payment & Create Sale
┌────────────────────┐
│ Submit Sale        │
└──────┬─────────────┘
       │
       ▼
┏━━━━━━━━━━━━━━━━━━━┓
┃  BEGIN TRANSACTION ┃ ← Database Transaction Start
┗━━━━━━━━━━━━━━━━━━━┛
       │
       ├─► Create SALE record
       │   (invoice, user_id, totals)
       │
       ├─► Create SALE_ITEMS records
       │   (for each product in cart)
       │
       ├─► Create PAYMENT record
       │   (amount, method)
       │
       ├─► Update INVENTORIES
       │   (reduce quantity for each product)
       │
       └─► Create STOCK_MOVEMENTS
           (record 'out' for each product)
       │
       ▼
┏━━━━━━━━━━━━━━━━━━━┓
┃  COMMIT            ┃ ← All success = Save
┃  or ROLLBACK       ┃ ← Any error = Undo all
┗━━━━━━━━━━━━━━━━━━━┛


STEP 4: Generate Receipt
┌─────────────┐
│ Get Sale    │
└──────┬──────┘
       │
       ├─► SALE (invoice, totals)
       ├─► SALE_ITEMS (products, quantities)
       ├─► PRODUCTS (names, prices)
       └─► PAYMENTS (method, amount)
       │
       ▼
   Print Receipt 🧾
```

---

## 🔍 Query Examples in Real Life

### **Example 1: Get Today's Sales Total**
```php
$total = Sale::today()
    ->where('status', 'completed')
    ->sum('total_amount');

// SQL: SELECT SUM(total_amount) FROM sales 
//      WHERE DATE(created_at) = '2026-01-08' 
//      AND status = 'completed'
```

### **Example 2: Get Low Stock Products**
```php
$lowStock = Product::with('inventory')
    ->whereHas('inventory', function($q) {
        $q->whereColumn('quantity', '<=', 'min_stock_level');
    })
    ->get();

// Returns: Wireless Headphones (3 units, min: 10)
```

### **Example 3: Get Sales by Cashier**
```php
$cashierSales = Sale::where('user_id', 2)
    ->with(['saleItems.product', 'payments'])
    ->whereBetween('created_at', [$startDate, $endDate])
    ->get();

// Shows: All sales made by John Cashier in date range
```

### **Example 4: Product Performance Report**
```php
$topProducts = SaleItem::select('product_id')
    ->selectRaw('SUM(quantity) as total_sold')
    ->selectRaw('SUM(subtotal) as total_revenue')
    ->groupBy('product_id')
    ->orderBy('total_sold', 'desc')
    ->with('product')
    ->limit(10)
    ->get();

// Returns: Top 10 best-selling products
```

---

## 💾 Index Strategy

### **Why These Indexes?**

```sql
-- FAST SEARCHES
products: barcode, sku, category_id, is_active
users: email, role_id

-- FAST REPORTS
sales: created_at, user_id, invoice_number, status
payments: payment_method, sale_id

-- FAST RELATIONSHIPS
All foreign keys are automatically indexed
```

---

## 🛡️ Data Integrity Rules

### **Foreign Key Constraints**

```php
// CASCADE DELETE
products → inventories (delete product = delete inventory)
sales → sale_items (delete sale = delete items)
sales → payments (delete sale = delete payments)

// SET NULL
users → role_id (delete role = set user.role_id to null)

// PREVENT DELETE
categories → products (can't delete category if products exist)
```

---

## 📈 Scalability Considerations

### **Current Structure Supports:**
- ✅ Multiple users/cashiers
- ✅ Thousands of products
- ✅ Unlimited sales transactions
- ✅ Complete audit trail
- ✅ Multiple payment methods per sale

### **Future Extensions:**
- Multi-store support (add `store_id` to sales)
- Customer loyalty (add `customers` table)
- Product variants (add `product_variants` table)
- Suppliers (add `suppliers` table)
- Returns/refunds (add `returns` table)

---

**This database design is production-ready and follows industry best practices!** 🚀
