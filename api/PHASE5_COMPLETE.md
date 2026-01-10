# 👥 PHASE 5 COMPLETE: Customer & Order Management

## ✅ What We Built

A complete Customer Relationship Management (CRM) and Returns system with:

- ✅ **Customer Management** - Full CRUD for customer records
- ✅ **Customer Types** - Regular and VIP customers
- ✅ **Credit System** - Credit limits and balance tracking
- ✅ **Loyalty Points** - Points accumulation and redemption
- ✅ **Purchase History** - Complete customer order tracking
- ✅ **Returns System** - Product returns with approval workflow
- ✅ **Return Tracking** - Complete audit trail for returns
- ✅ **Statistics** - Customer and return analytics

---

## 📊 Database Changes

### New Tables:

**1. `customers` Table:**
```sql
- id
- name (required)
- email (unique, optional)
- phone (optional)
- address (optional)
- type (regular/vip)
- credit_limit (default: 0)
- credit_balance (default: 0)
- loyalty_points (default: 0)
- is_active (default: true)
- timestamps
```

**2. `returns` Table:**
```sql
- id
- return_number (unique, auto-generated)
- sale_id (foreign key)
- product_id (foreign key)
- user_id (foreign key - who processed)
- quantity
- refund_amount
- reason (defective/wrong_item/customer_request/damaged/other)
- notes
- status (pending/approved/rejected)
- timestamps
```

**3. `sales` Table Updated:**
- Added `customer_id` (nullable foreign key)

---

## 📡 API Endpoints

### Base URL: `http://localhost:8000/api`
### Authentication: All endpoints require `Bearer Token`

---

## 👥 CUSTOMER ENDPOINTS

### 1. Get All Customers
```http
GET /api/customers
```

**Query Parameters:**
- `search` (string) - Search by name, email, or phone
- `type` (string) - Filter by type (regular, vip)
- `is_active` (boolean) - Filter by active status
- `per_page` (integer) - Pagination (default: 15)

**Examples:**
```http
GET /api/customers?search=john
GET /api/customers?type=vip&is_active=1
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
        "name": "John Smith",
        "email": "john.smith@example.com",
        "phone": "+1-555-0101",
        "address": "123 Main Street, New York, NY 10001",
        "type": "regular",
        "credit_limit": "1000.00",
        "credit_balance": "0.00",
        "loyalty_points": 250,
        "is_active": true,
        "created_at": "2026-01-08 10:00:00"
      }
    ],
    "total": 5
  }
}
```

---

### 2. Get Single Customer
```http
GET /api/customers/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "customer": {
      "id": 2,
      "name": "Sarah Johnson",
      "email": "sarah.j@example.com",
      "phone": "+1-555-0102",
      "type": "vip",
      "credit_limit": "5000.00",
      "credit_balance": "0.00",
      "loyalty_points": 1500,
      "is_active": true
    },
    "statistics": {
      "total_purchases": 15,
      "total_spent": "12450.75",
      "available_credit": "5000.00",
      "loyalty_points": 1500
    }
  }
}
```

---

### 3. Create Customer
```http
POST /api/customers
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Alice Williams",
  "email": "alice.w@example.com",
  "phone": "+1-555-0106",
  "address": "555 Maple Drive, Miami, FL 33101",
  "type": "regular",
  "credit_limit": 2000.00
}
```

**Validation Rules:**
- `name` - Required, max 255 characters
- `email` - Optional, must be valid email, unique
- `phone` - Optional, max 20 characters
- `address` - Optional, text
- `type` - Optional, "regular" or "vip" (default: regular)
- `credit_limit` - Optional, numeric, min 0 (default: 0)
- `is_active` - Optional, boolean (default: true)

**Response:**
```json
{
  "success": true,
  "message": "Customer created successfully",
  "data": {
    "id": 6,
    "name": "Alice Williams",
    "email": "alice.w@example.com",
    "type": "regular",
    "credit_limit": "2000.00",
    "loyalty_points": 0
  }
}
```

---

### 4. Update Customer
```http
PUT /api/customers/{id}
Content-Type: application/json
```

**Request Body:**
```json
{
  "type": "vip",
  "credit_limit": 10000.00
}
```

**Response:**
```json
{
  "success": true,
  "message": "Customer updated successfully",
  "data": {
    "id": 6,
    "name": "Alice Williams",
    "type": "vip",
    "credit_limit": "10000.00"
  }
}
```

---

### 5. Delete Customer
```http
DELETE /api/customers/{id}
```

**Business Rule:** Cannot delete customers with existing sales

**Response:**
```json
{
  "success": true,
  "message": "Customer deleted successfully"
}
```

**Error (if has sales):**
```json
{
  "success": false,
  "message": "Cannot delete customer with existing sales. Deactivate instead."
}
```

---

### 6. Get Customer Purchase History
```http
GET /api/customers/{id}/purchases
```

**Query Parameters:**
- `start_date` (date) - Filter from date
- `end_date` (date) - Filter to date
- `status` (string) - Filter by status
- `per_page` (integer) - Pagination

**Example:**
```http
GET /api/customers/2/purchases?start_date=2026-01-01&status=completed
```

**Response:**
```json
{
  "success": true,
  "data": {
    "customer": {
      "id": 2,
      "name": "Sarah Johnson",
      "type": "vip"
    },
    "sales": {
      "data": [
        {
          "id": 1,
          "invoice_number": "INV-2026-0001",
          "total_amount": "5310.89",
          "status": "completed",
          "created_at": "2026-01-08 14:30:00",
          "items": [...],
          "payments": [...]
        }
      ],
      "total": 15
    }
  }
}
```

---

### 7. Manage Loyalty Points (Admin Only)
```http
POST /api/customers/{id}/loyalty-points
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "action": "add",
  "points": 100,
  "reason": "Promotion bonus"
}
```

**Actions:**
- `add` - Add points to customer
- `deduct` - Remove points from customer

**Response:**
```json
{
  "success": true,
  "message": "Added 100 loyalty points",
  "data": {
    "old_points": 250,
    "new_points": 350,
    "reason": "Promotion bonus"
  }
}
```

**Error (insufficient points):**
```json
{
  "success": false,
  "message": "Insufficient loyalty points"
}
```

---

### 8. Get VIP Customers
```http
GET /api/customers/vip
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 2,
      "name": "Sarah Johnson",
      "email": "sarah.j@example.com",
      "type": "vip",
      "loyalty_points": 1500,
      "sales_count": 15
    },
    {
      "id": 4,
      "name": "Emily Davis",
      "type": "vip",
      "loyalty_points": 3200,
      "sales_count": 28
    }
  ]
}
```

---

## 🔄 RETURN ENDPOINTS

### 1. Get All Returns
```http
GET /api/returns
```

**Query Parameters:**
- `status` (string) - Filter by status (pending/approved/rejected)
- `start_date` (date) - Filter from date
- `end_date` (date) - Filter to date
- `product_id` (integer) - Filter by product
- `per_page` (integer) - Pagination

**Examples:**
```http
GET /api/returns?status=pending
GET /api/returns?start_date=2026-01-01&end_date=2026-01-31
```

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "return_number": "RET-2026-0001",
        "quantity": 1,
        "refund_amount": "199.99",
        "reason": "defective",
        "status": "pending",
        "created_at": "2026-01-08 16:00:00",
        "sale": {
          "id": 5,
          "invoice_number": "INV-2026-0005"
        },
        "product": {
          "id": 8,
          "name": "Wireless Headphones"
        },
        "user": {
          "id": 1,
          "name": "Admin User"
        }
      }
    ],
    "total": 1
  }
}
```

---

### 2. Get Single Return
```http
GET /api/returns/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "return_number": "RET-2026-0001",
    "quantity": 1,
    "refund_amount": "199.99",
    "reason": "defective",
    "notes": "Device not turning on",
    "status": "pending",
    "created_at": "2026-01-08 16:00:00",
    "sale": {
      "id": 5,
      "invoice_number": "INV-2026-0005",
      "items": [...]
    },
    "product": {
      "id": 8,
      "name": "Wireless Headphones",
      "sku": "ELE-HEA-WIR-001"
    },
    "user": {
      "id": 1,
      "name": "Admin User"
    }
  }
}
```

---

### 3. Create Return Request
```http
POST /api/returns
Content-Type: application/json
```

**Request Body:**
```json
{
  "sale_id": 5,
  "product_id": 8,
  "quantity": 1,
  "refund_amount": 199.99,
  "reason": "defective",
  "notes": "Device not turning on after 2 days"
}
```

**Validation Rules:**
- `sale_id` - Required, must exist
- `product_id` - Required, must exist
- `quantity` - Required, integer, min 1
- `refund_amount` - Required, numeric, min 0
- `reason` - Required, one of: defective, wrong_item, customer_request, damaged, other
- `notes` - Optional, string

**Business Rules:**
1. Product must be in the specified sale
2. Cannot return more than purchased
3. Cannot return from void sales
4. Tracks already returned quantity

**Response:**
```json
{
  "success": true,
  "message": "Return request created successfully",
  "data": {
    "id": 1,
    "return_number": "RET-2026-0001",
    "status": "pending",
    "sale": {...},
    "product": {...}
  }
}
```

**Error Examples:**

Product not in sale:
```json
{
  "success": false,
  "message": "Failed to create return request",
  "error": "Product not found in this sale"
}
```

Quantity exceeded:
```json
{
  "error": "Cannot return more than purchased. Purchased: 2, Already returned: 1"
}
```

---

### 4. Approve/Reject Return (Admin Only)
```http
PUT /api/returns/{id}/status
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "status": "approved",
  "notes": "Confirmed defective, issued refund"
}
```

**Status Options:**
- `approved` - Accept return, restore inventory
- `rejected` - Reject return, no inventory change

**Response:**
```json
{
  "success": true,
  "message": "Return approved successfully",
  "data": {
    "id": 1,
    "return_number": "RET-2026-0001",
    "status": "approved"
  }
}
```

**What Happens on Approval:**
1. Inventory restored (quantity increased)
2. Stock movement logged (type: IN)
3. Reference: Return number
4. Status changed to approved

**Business Rules:**
- Only pending returns can be approved/rejected
- Only admins can change status
- Approved returns restore inventory automatically

---

### 5. Get Pending Returns
```http
GET /api/returns/pending
```

**Response:**
```json
{
  "success": true,
  "count": 3,
  "data": [
    {
      "id": 1,
      "return_number": "RET-2026-0001",
      "product": {
        "name": "Wireless Headphones"
      },
      "quantity": 1,
      "refund_amount": "199.99",
      "reason": "defective",
      "created_at": "2026-01-08 16:00:00"
    }
  ]
}
```

**Use Case:** Daily return processing workflow

---

### 6. Get Return Statistics
```http
GET /api/returns/statistics
```

**Query Parameters:**
- `period` (string) - today, week, month, year (default: month)

**Examples:**
```http
GET /api/returns/statistics?period=today
GET /api/returns/statistics?period=month
```

**Response:**
```json
{
  "success": true,
  "data": {
    "period": "month",
    "total_returns": 45,
    "total_refunded": "8950.55",
    "pending_count": 3,
    "approved_count": 38,
    "rejected_count": 4,
    "top_returned_products": [
      {
        "product_id": 8,
        "product_name": "Wireless Headphones",
        "total_quantity": 12,
        "total_refunded": "2399.88"
      },
      {
        "product_id": 3,
        "product_name": "Mechanical Keyboard RGB",
        "total_quantity": 8,
        "total_refunded": "639.92"
      }
    ],
    "reasons_breakdown": [
      {
        "reason": "defective",
        "count": 20,
        "total_refunded": "5200.00"
      },
      {
        "reason": "customer_request",
        "count": 15,
        "total_refunded": "2500.00"
      },
      {
        "reason": "damaged",
        "count": 10,
        "total_refunded": "1250.55"
      }
    ]
  }
}
```

---

## 💡 Updated Sales Flow

### Creating Sale with Customer

```bash
curl -X POST http://localhost:8000/api/sales \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 2,
    "items": [
      {"product_id": 1, "quantity": 1, "unit_price": 2499.00}
    ],
    "tax_percentage": 10,
    "payments": [
      {"payment_method": "cash", "amount": 2748.90}
    ]
  }'
```

**What Changed:**
- Added optional `customer_id` field
- Sales now track which customer made the purchase
- Customer statistics automatically updated

---

## 🧪 Testing Guide

### Test 1: Create Customer

```bash
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@pos.com","password":"password"}' \
  | jq -r '.token')

curl -X POST http://localhost:8000/api/customers \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Customer",
    "email": "test@example.com",
    "phone": "+1-555-9999",
    "type": "regular",
    "credit_limit": 1000
  }' | jq '.'
```

---

### Test 2: Search Customers

```bash
curl -X GET "http://localhost:8000/api/customers?search=sarah" \
  -H "Authorization: Bearer $TOKEN" | jq '.'
```

---

### Test 3: Get VIP Customers

```bash
curl -X GET http://localhost:8000/api/customers/vip \
  -H "Authorization: Bearer $TOKEN" | jq '.'
```

---

### Test 4: Create Sale with Customer

```bash
curl -X POST http://localhost:8000/api/sales \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 2,
    "items": [
      {"product_id": 2, "quantity": 2, "unit_price": 99.99}
    ],
    "tax_percentage": 10,
    "payments": [
      {"payment_method": "cash", "amount": 219.98}
    ]
  }' | jq '.'
```

---

### Test 5: Get Customer Purchase History

```bash
# Get the customer ID from previous sale
CUSTOMER_ID=2

curl -X GET "http://localhost:8000/api/customers/$CUSTOMER_ID/purchases" \
  -H "Authorization: Bearer $TOKEN" | jq '.'
```

---

### Test 6: Create Return Request

```bash
# First, get a sale ID from previous test
SALE_ID=1

curl -X POST http://localhost:8000/api/returns \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "sale_id": '$SALE_ID',
    "product_id": 2,
    "quantity": 1,
    "refund_amount": 99.99,
    "reason": "defective",
    "notes": "Mouse buttons not working"
  }' | jq '.'
```

---

### Test 7: Get Pending Returns

```bash
curl -X GET http://localhost:8000/api/returns/pending \
  -H "Authorization: Bearer $TOKEN" | jq '.'
```

---

### Test 8: Approve Return (Admin)

```bash
RETURN_ID=1

curl -X PUT http://localhost:8000/api/returns/$RETURN_ID/status \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "approved",
    "notes": "Return approved, refund issued"
  }' | jq '.'

# Check inventory was restored
curl -X GET http://localhost:8000/api/inventory/2 \
  -H "Authorization: Bearer $TOKEN" | jq '.'
```

---

### Test 9: Manage Loyalty Points

```bash
CUSTOMER_ID=1

# Add points
curl -X POST http://localhost:8000/api/customers/$CUSTOMER_ID/loyalty-points \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "action": "add",
    "points": 500,
    "reason": "Birthday bonus"
  }' | jq '.'

# Deduct points
curl -X POST http://localhost:8000/api/customers/$CUSTOMER_ID/loyalty-points \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "action": "deduct",
    "points": 100,
    "reason": "Redeemed for discount"
  }' | jq '.'
```

---

### Test 10: Get Return Statistics

```bash
curl -X GET "http://localhost:8000/api/returns/statistics?period=month" \
  -H "Authorization: Bearer $TOKEN" | jq '.'
```

---

## 🔐 Authorization

| Action | Admin | Cashier |
|--------|-------|---------|
| View Customers | ✅ | ✅ |
| Create Customers | ✅ | ✅ |
| Update Customers | ✅ | ✅ |
| Delete Customers | ✅ | ✅ |
| Manage Loyalty Points | ✅ | ❌ |
| Create Returns | ✅ | ✅ |
| Approve/Reject Returns | ✅ | ❌ |
| View Return Statistics | ✅ | ✅ |

---

## 🎓 Business Logic Explained

### 1. Customer Types

**Regular Customer:**
- Standard credit limit
- Basic loyalty points
- No special benefits

**VIP Customer:**
- Higher credit limit
- More loyalty points
- Priority service
- Exclusive promotions

---

### 2. Credit System

```
Credit Limit: $5000
Credit Balance: $1200 (amount owed)
Available Credit: $3800 ($5000 - $1200)
```

**Use Cases:**
- Allow customers to buy on credit
- Track outstanding balances
- Set credit limits per customer
- Payment plans

---

### 3. Loyalty Points

**Earning:**
- 1 point per $1 spent (configurable)
- Bonus points for VIP
- Promotional points

**Redeeming:**
- 100 points = $1 discount (configurable)
- Minimum redemption: 100 points
- Cannot redeem more than available

---

### 4. Return Workflow

```
CUSTOMER WANTS TO RETURN
    ↓
1. CASHIER CREATES RETURN REQUEST
   - Status: Pending
   - Inventory: Not changed yet
    ↓
2. ADMIN REVIEWS REQUEST
   - Check product condition
   - Verify purchase
   - Make decision
    ↓
3a. IF APPROVED:
    - Status: Approved
    - Inventory: +quantity
    - Stock movement logged
    - Refund processed
    ↓
3b. IF REJECTED:
    - Status: Rejected
    - Inventory: No change
    - Customer notified
```

---

### 5. Return Reasons

| Reason | Description | Common Action |
|--------|-------------|---------------|
| defective | Product doesn't work | Approve + Replace |
| wrong_item | Customer ordered wrong | Approve + Exchange |
| customer_request | Changed mind | Review case-by-case |
| damaged | Physical damage | Investigate + Approve |
| other | Other reasons | Admin review |

---

### 6. Return Number Generation

```
Format: RET-YYYY-NNNN

Examples:
- RET-2026-0001 (First return of 2026)
- RET-2026-0002 (Second return)
- RET-2027-0001 (Resets each year)
```

**Same algorithm as invoice numbers** ✅

---

## 💾 Sample Data

**5 Customers Created:**

1. **John Smith** - Regular
   - Email: john.smith@example.com
   - Phone: +1-555-0101
   - Credit Limit: $1000
   - Loyalty Points: 250

2. **Sarah Johnson** - VIP
   - Email: sarah.j@example.com
   - Phone: +1-555-0102
   - Credit Limit: $5000
   - Loyalty Points: 1500

3. **Michael Brown** - Regular
   - Email: mbrown@example.com
   - Phone: +1-555-0103
   - Credit Limit: $500
   - Loyalty Points: 50

4. **Emily Davis** - VIP
   - Email: emily.davis@example.com
   - Phone: +1-555-0104
   - Credit Limit: $10000
   - Loyalty Points: 3200

5. **David Wilson** - Regular (Walk-in)
   - Phone: +1-555-0105
   - No email/address
   - No credit
   - No points

---

## 📊 Complete System Overview

**Total API Endpoints: 43**

- 3 Authentication
- 6 Categories
- 7 Products
- 7 Inventory
- 6 Sales
- 8 Customers
- 6 Returns

**Database Tables: 10**

- roles
- users
- categories
- products
- inventories
- stock_movements
- sales
- sale_items
- payments
- **customers** (new)
- **returns** (new)

---

## 🚀 What's Next: Phase 6

**Reports & Analytics:**
- Sales reports (daily, weekly, monthly)
- Product performance analysis
- Customer insights
- Inventory reports
- Financial summaries
- Export to PDF/Excel

---

## 🎯 Key Learnings

### 1. Customer Data Structure
Keep customer data separate from transactions for flexibility and data integrity.

### 2. Returns Workflow
Two-step approval process prevents inventory errors and maintains accountability.

### 3. Loyalty System
Incentivize repeat customers with points and VIP status for business growth.

### 4. Credit Management
Enable credit sales while maintaining control with limits and balance tracking.

### 5. Audit Trail
Track who created/approved returns for accountability and dispute resolution.

---

**Phase 5 Complete! Ready for Phase 6? Type "phase 6" when ready! 📊**
