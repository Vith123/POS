# 🎉 PHASE 3 COMPLETE: Product & Inventory Management API

## ✅ What We Built

You now have a complete Product and Inventory Management system with:

- ✅ **Product CRUD** - Create, Read, Update, Delete products
- ✅ **Image Upload** - Product photos with automatic storage
- ✅ **Category Management** - Organize products
- ✅ **Inventory Tracking** - Real-time stock levels
- ✅ **Stock In/Out** - Add and remove inventory
- ✅ **Stock Movements** - Complete audit trail
- ✅ **Low Stock Alerts** - Automatic alerts
- ✅ **Business Validation** - Prevent errors

---

## 📡 API Endpoints Reference

### Base URL: `http://localhost:8000/api`
### Authentication: All endpoints require `Bearer Token` except `/login`

---

## 🏷️ CATEGORY ENDPOINTS

### 1. Get All Categories
```http
GET /api/categories
```

**Query Parameters:**
- `is_active` (boolean) - Filter by active status
- `with_products` (boolean) - Include product count

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "description": "Electronic devices",
      "is_active": true,
      "products_count": 4
    }
  ]
}
```

---

### 2. Get Single Category
```http
GET /api/categories/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Electronics",
    "description": "Electronic devices",
    "is_active": true,
    "products_count": 4
  }
}
```

---

### 3. Create Category
```http
POST /api/categories
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "Accessories",
  "description": "Phone and laptop accessories",
  "is_active": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "Category created successfully",
  "data": {
    "id": 4,
    "name": "Accessories",
    "description": "Phone and laptop accessories",
    "is_active": true
  }
}
```

---

### 4. Update Category
```http
PUT /api/categories/{id}
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "Updated Name",
  "is_active": false
}
```

---

### 5. Delete Category
```http
DELETE /api/categories/{id}
Authorization: Bearer {token}
```

**Note:** Cannot delete category with products

---

### 6. Get Products by Category
```http
GET /api/categories/{id}/products
```

**Response:**
```json
{
  "success": true,
  "data": {
    "category": {
      "id": 1,
      "name": "Electronics"
    },
    "products": [...]
  }
}
```

---

## 📦 PRODUCT ENDPOINTS

### 1. Get All Products
```http
GET /api/products
```

**Query Parameters:**
- `search` (string) - Search by name, barcode, or SKU
- `category_id` (integer) - Filter by category
- `is_active` (boolean) - Filter by active status
- `with_inventory` (boolean) - Include inventory data
- `per_page` (integer) - Pagination (default: 15)

**Example:**
```http
GET /api/products?search=laptop&category_id=1&with_inventory=1
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "MacBook Pro 16\"",
        "barcode": "0194252456937",
        "sku": "LAP-MAC-16-001",
        "cost_price": "1800.00",
        "selling_price": "2499.00",
        "image": "products/abc123.jpg",
        "is_active": true,
        "category": {
          "id": 1,
          "name": "Electronics"
        },
        "inventory": {
          "quantity": 15,
          "min_stock_level": 5
        }
      }
    ],
    "total": 1,
    "per_page": 15
  }
}
```

---

### 2. Get Single Product
```http
GET /api/products/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "MacBook Pro 16\"",
    "barcode": "0194252456937",
    "sku": "LAP-MAC-16-001",
    "description": "Apple MacBook Pro 16-inch with M2 chip",
    "cost_price": "1800.00",
    "selling_price": "2499.00",
    "image": "products/abc123.jpg",
    "is_active": true,
    "category": {...},
    "inventory": {...},
    "profit_margin": 28.09,
    "is_low_stock": false,
    "stock_quantity": 15
  }
}
```

---

### 3. Create Product
```http
POST /api/products
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
```
category_id: 1
name: iPhone 15 Pro Max
barcode: 0194253448891
sku: IPH-15-PRO-MAX-256
description: Latest iPhone with 256GB storage
cost_price: 900.00
selling_price: 1199.00
image: [file upload]
is_active: true
initial_stock: 20
min_stock_level: 5
```

**Response:**
```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "id": 9,
    "name": "iPhone 15 Pro Max",
    "category": {...},
    "inventory": {
      "quantity": 20,
      "min_stock_level": 5
    }
  }
}
```

**Validation Rules:**
- `category_id` - Required, must exist
- `name` - Required, max 255 characters
- `barcode` - Optional, unique
- `sku` - Required, unique
- `cost_price` - Required, minimum 0
- `selling_price` - Required, minimum 0
- `image` - Optional, jpeg/png/jpg/gif, max 2MB
- `initial_stock` - Optional, minimum 0
- `min_stock_level` - Optional, minimum 0

---

### 4. Update Product
```http
POST /api/products/{id}
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Note:** Use POST (not PUT) for file uploads

**Request Body:**
```
name: Updated Product Name
selling_price: 1299.00
image: [file upload - optional]
```

**Response:**
```json
{
  "success": true,
  "message": "Product updated successfully",
  "data": {...}
}
```

---

### 5. Delete Product
```http
DELETE /api/products/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Product deleted successfully"
}
```

**Business Rule:** Cannot delete products that have been sold

---

### 6. Get Low Stock Products
```http
GET /api/products/low-stock
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "count": 1,
  "data": [
    {
      "id": 8,
      "name": "Wireless Headphones",
      "inventory": {
        "quantity": 3,
        "min_stock_level": 10
      }
    }
  ]
}
```

---

### 7. Get Out of Stock Products
```http
GET /api/products/out-of-stock
Authorization: Bearer {token}
```

---

## 📊 INVENTORY ENDPOINTS

### 1. Get Inventory Overview
```http
GET /api/inventory
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "summary": {
    "total_products": 8,
    "total_stock_value": 45890.50,
    "low_stock_count": 1,
    "out_of_stock_count": 0
  },
  "inventory": [...]
}
```

---

### 2. Get Product Inventory
```http
GET /api/inventory/{productId}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "product": {
      "id": 1,
      "name": "MacBook Pro 16\"",
      "sku": "LAP-MAC-16-001"
    },
    "inventory": {
      "quantity": 15,
      "min_stock_level": 5
    },
    "is_low_stock": false,
    "recent_movements": [
      {
        "id": 1,
        "type": "in",
        "quantity": 20,
        "reference": "Initial Stock",
        "user": {
          "name": "Admin User"
        },
        "created_at": "2026-01-08 10:00:00"
      }
    ]
  }
}
```

---

### 3. Stock In (Add Stock)
```http
POST /api/inventory/stock-in
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "product_id": 1,
  "quantity": 50,
  "reference": "PO-2026-001",
  "notes": "Received from supplier ABC"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Added 50 units to MacBook Pro 16\"",
  "data": {
    "product": {
      "id": 1,
      "name": "MacBook Pro 16\"",
      "sku": "LAP-MAC-16-001"
    },
    "new_quantity": 65,
    "added": 50
  }
}
```

**When to use:**
- Receiving new inventory from supplier
- Restocking items
- Correcting inventory upward

---

### 4. Stock Out (Remove Stock)
```http
POST /api/inventory/stock-out
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "product_id": 1,
  "quantity": 2,
  "reference": "Damaged Items",
  "notes": "Water damage during storage"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Removed 2 units from MacBook Pro 16\"",
  "data": {
    "product": {
      "id": 1,
      "name": "MacBook Pro 16\"",
      "sku": "LAP-MAC-16-001"
    },
    "new_quantity": 63,
    "removed": 2
  }
}
```

**When to use:**
- Damaged/expired items
- Returns to supplier
- Manual inventory correction
- Lost/stolen items

**Note:** Sales automatically handle stock out via sale process

---

### 5. Adjust Inventory
```http
POST /api/inventory/adjust
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "product_id": 1,
  "new_quantity": 60,
  "reason": "Physical count correction",
  "notes": "Quarterly inventory audit"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Inventory adjusted successfully",
  "data": {
    "product": {...},
    "old_quantity": 63,
    "new_quantity": 60,
    "difference": -3
  }
}
```

**When to use:**
- After physical count
- Fixing discrepancies
- Major inventory corrections

**Warning:** Use carefully! This directly sets quantity.

---

### 6. Get Stock Movements
```http
GET /api/inventory/movements
Authorization: Bearer {token}
```

**Query Parameters:**
- `product_id` (integer) - Filter by product
- `type` (string) - Filter by type (in/out)
- `start_date` (date) - Filter by date range
- `end_date` (date) - Filter by date range
- `per_page` (integer) - Pagination (default: 20)

**Example:**
```http
GET /api/inventory/movements?product_id=1&type=in&start_date=2026-01-01&end_date=2026-01-31
```

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "product": {
          "name": "MacBook Pro 16\""
        },
        "type": "in",
        "quantity": 50,
        "reference": "PO-2026-001",
        "notes": "Received from supplier",
        "user": {
          "name": "Admin User"
        },
        "created_at": "2026-01-08 10:00:00"
      }
    ],
    "total": 1
  }
}
```

---

### 7. Update Min Stock Level
```http
PUT /api/inventory/{productId}/min-level
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "min_stock_level": 20
}
```

**Response:**
```json
{
  "success": true,
  "message": "Min stock level updated successfully",
  "data": {
    "quantity": 65,
    "min_stock_level": 20
  }
}
```

---

## 🧪 Testing with cURL

### 1. Login First
```bash
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@pos.com","password":"password"}' \
  | jq -r '.token')

echo "Token: $TOKEN"
```

### 2. Get All Products
```bash
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'
```

### 3. Search Products
```bash
curl -X GET "http://localhost:8000/api/products?search=laptop&with_inventory=1" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'
```

### 4. Create Product (without image)
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "category_id": 1,
    "name": "Test Product",
    "sku": "TEST-001",
    "cost_price": 50,
    "selling_price": 100,
    "initial_stock": 10
  }' | jq '.'
```

### 5. Stock In
```bash
curl -X POST http://localhost:8000/api/inventory/stock-in \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 25,
    "reference": "PO-TEST-001",
    "notes": "Test stock addition"
  }' | jq '.'
```

### 6. Get Low Stock
```bash
curl -X GET http://localhost:8000/api/products/low-stock \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'
```

---

## 📝 Business Logic Explained

### **Product Creation Flow:**
```
1. Validate input
2. Upload image (if provided)
3. Create product record
4. Create inventory record
5. If initial_stock > 0:
   - Create stock movement "IN"
6. Commit or rollback
```

### **Stock In Flow:**
```
1. Validate product exists
2. Increment inventory quantity
3. Create stock movement record (type: IN)
4. Track user who added stock
5. Commit or rollback
```

### **Stock Out Flow:**
```
1. Validate product exists
2. Check sufficient stock
3. Decrement inventory quantity
4. Create stock movement record (type: OUT)
5. Track user who removed stock
6. Commit or rollback
```

### **Why Database Transactions?**
```php
DB::beginTransaction();
try {
    // Multiple operations
    DB::commit(); // All success
} catch (Exception $e) {
    DB::rollback(); // Any fail = undo all
}
```

**Example:** Creating product with initial stock:
- If product created BUT inventory fails → Rollback both
- If product AND inventory succeed BUT stock movement fails → Rollback all
- All-or-nothing = Data integrity ✅

---

## 🔒 Authorization Rules

| Action | Admin | Cashier |
|--------|-------|---------|
| View Products | ✅ | ✅ |
| Create Products | ✅ | ❌ |
| Update Products | ✅ | ❌ |
| Delete Products | ✅ | ❌ |
| View Inventory | ✅ | ✅ |
| Stock In/Out | ✅ | ❌ |
| Adjust Inventory | ✅ | ❌ |

---

## 🖼️ Image Upload Guide

### **Upload Product Image:**

```bash
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer $TOKEN" \
  -F "category_id=1" \
  -F "name=Test Product with Image" \
  -F "sku=TEST-IMG-001" \
  -F "cost_price=50" \
  -F "selling_price=100" \
  -F "image=@/path/to/image.jpg"
```

### **Update Product Image:**

```bash
curl -X POST http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer $TOKEN" \
  -F "name=Updated Name" \
  -F "image=@/path/to/new-image.jpg"
```

### **Access Image:**
```
http://localhost:8000/storage/products/abc123.jpg
```

### **Image Validation:**
- Formats: JPEG, PNG, JPG, GIF
- Max Size: 2MB
- Auto-deleted when product updated/deleted

---

## ⚠️ Error Handling

### **Common Errors:**

**401 Unauthorized**
```json
{
  "message": "Unauthenticated."
}
```
**Solution:** Include valid token in Authorization header

**403 Forbidden**
```json
{
  "success": false,
  "message": "Unauthorized. Only admins can create products."
}
```
**Solution:** Login as admin

**404 Not Found**
```json
{
  "success": false,
  "message": "Product not found"
}
```
**Solution:** Check product ID exists

**422 Validation Error**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "sku": ["The sku has already been taken."]
  }
}
```
**Solution:** Fix validation errors

**500 Server Error**
```json
{
  "success": false,
  "message": "Failed to create product",
  "error": "Database error message"
}
```
**Solution:** Check logs, database connection

---

## 🎓 Key Learnings

### **1. Why Separate Inventory Table?**
- Product = Static info (name, price)
- Inventory = Dynamic data (quantity changes frequently)
- Better performance (fewer updates to product table)
- Cleaner structure

### **2. Why Track Stock Movements?**
- **Audit trail** - Know who changed what and when
- **Accountability** - Track mistakes
- **Business insights** - Analyze patterns
- **Legal compliance** - Required for accounting

### **3. Image Upload Best Practices**
- ✅ Validate file type and size
- ✅ Store in separate directory
- ✅ Use unique filenames (auto-generated)
- ✅ Delete old images when updating
- ✅ Create storage symlink for public access

### **4. Validation Rules Purpose**
- `unique` - Prevent duplicate SKU/barcode
- `exists` - Ensure category exists
- `min:0` - No negative prices
- `image|max:2048` - Security and performance

---

## 🚀 What's Next: Phase 4

**Sales & Checkout System:**
- Shopping cart functionality
- Apply discounts
- Calculate tax
- Process payments
- Generate receipts
- Automatic inventory updates

---

**Phase 3 Complete! Ready for Phase 4? Type "phase 4" when you're ready! 🎯**
