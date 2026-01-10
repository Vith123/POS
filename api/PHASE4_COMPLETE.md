# 🛒 PHASE 4 COMPLETE: Sales & Checkout System

## ✅ What We Built

A complete Point of Sale checkout system with:

- ✅ **Full Checkout Process** - Create sales with multiple items
- ✅ **Automatic Inventory Deduction** - Real-time stock updates
- ✅ **Tax Calculation** - Percentage-based tax
- ✅ **Discount System** - Fixed or percentage discounts
- ✅ **Multiple Payment Methods** - Cash, card, e-wallet
- ✅ **Invoice Generation** - Auto-numbered invoices (INV-YYYY-NNNN)
- ✅ **Stock Movement Tracking** - Complete audit trail
- ✅ **Sales Summary** - Today's sales and statistics
- ✅ **Void Sales** - Cancel sales and restore inventory

---

## 🎯 How Checkout Works (Step-by-Step)

```
CUSTOMER COMES TO CASHIER
    ↓
1. SCAN/SELECT PRODUCTS
   - Add items to cart
   - Check stock availability
    ↓
2. CALCULATE TOTALS
   - Subtotal = Sum of all items
   - Apply tax (%)
   - Apply discount (fixed or %)
   - Total = Subtotal + Tax - Discount
    ↓
3. COLLECT PAYMENT
   - Cash, Card, or E-wallet
   - Can split payment (e.g., $50 cash + $30 card)
    ↓
4. PROCESS SALE
   - Deduct inventory
   - Generate invoice
   - Record payment
   - Create receipt
    ↓
5. COMPLETE ✅
```

---

## 📡 API Endpoints

### Base URL: `http://localhost:8000/api`
### Authentication: All endpoints require `Bearer Token`

---

## 🛍️ SALES ENDPOINTS

### 1. Create Sale (Checkout)
```http
POST /api/sales
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "unit_price": 2499.00
    },
    {
      "product_id": 3,
      "quantity": 1,
      "unit_price": 59.99
    }
  ],
  "tax_percentage": 10,
  "discount_type": "percentage",
  "discount_value": 5,
  "payments": [
    {
      "payment_method": "cash",
      "amount": 5000.00
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Sale completed successfully",
  "data": {
    "id": 1,
    "invoice_number": "INV-2026-0001",
    "user_id": 1,
    "subtotal": "5057.99",
    "tax_percentage": "10.00",
    "tax_amount": "505.80",
    "discount_type": "percentage",
    "discount_value": "5.00",
    "discount_amount": "252.90",
    "total_amount": "5310.89",
    "status": "completed",
    "created_at": "2026-01-08 14:30:00",
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@pos.com"
    },
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "quantity": 2,
        "unit_price": "2499.00",
        "subtotal": "4998.00",
        "product": {
          "id": 1,
          "name": "MacBook Pro 16\"",
          "sku": "LAP-MAC-16-001"
        }
      },
      {
        "id": 2,
        "product_id": 3,
        "quantity": 1,
        "unit_price": "59.99",
        "subtotal": "59.99",
        "product": {
          "id": 3,
          "name": "USB-C Cable",
          "sku": "ACC-USB-C-001"
        }
      }
    ],
    "payments": [
      {
        "id": 1,
        "payment_method": "cash",
        "amount": "5310.89"
      }
    ]
  }
}
```

**Validation Rules:**
- `items` - Required, array with minimum 1 item
- `items.*.product_id` - Required, must exist
- `items.*.quantity` - Required, integer, minimum 1
- `items.*.unit_price` - Required, numeric, minimum 0
- `tax_percentage` - Optional, numeric, 0-100
- `discount_type` - Optional, "fixed" or "percentage"
- `discount_value` - Optional, numeric, minimum 0
- `payments` - Required, array with minimum 1 payment
- `payments.*.payment_method` - Required, "cash", "card", or "e-wallet"
- `payments.*.amount` - Required, numeric, minimum 0
- `payments.*.reference_number` - Optional, string (for card/e-wallet)

**Business Rules:**
1. **Stock Validation** - Checks sufficient inventory before processing
2. **Product Active Status** - Only sells active products
3. **Payment Match** - Total payment must equal sale total (±1 cent for rounding)
4. **Automatic Inventory Deduction** - Reduces stock immediately
5. **Stock Movement Logging** - Records all inventory changes
6. **Invoice Auto-numbering** - Format: INV-YYYY-NNNN (auto-increments)

**Error Examples:**

Insufficient stock:
```json
{
  "success": false,
  "message": "Failed to process sale",
  "error": "Insufficient stock for 'MacBook Pro 16\"'. Available: 5, Requested: 10"
}
```

Payment mismatch:
```json
{
  "success": false,
  "message": "Failed to process sale",
  "error": "Payment mismatch. Total: 5310.89, Paid: 5000.00"
}
```

---

### 2. Get All Sales
```http
GET /api/sales
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` - Filter by status (completed, void)
- `start_date` - Filter from date (YYYY-MM-DD)
- `end_date` - Filter to date (YYYY-MM-DD)
- `user_id` - Filter by cashier
- `per_page` - Pagination (default: 15)

**Examples:**
```http
GET /api/sales?status=completed&start_date=2026-01-01&end_date=2026-01-31
GET /api/sales?user_id=2&per_page=20
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
        "invoice_number": "INV-2026-0001",
        "total_amount": "5310.89",
        "status": "completed",
        "created_at": "2026-01-08 14:30:00",
        "user": {...},
        "items": [...],
        "payments": [...]
      }
    ],
    "total": 1,
    "per_page": 15
  }
}
```

---

### 3. Get Single Sale
```http
GET /api/sales/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "invoice_number": "INV-2026-0001",
    "subtotal": "5057.99",
    "tax_amount": "505.80",
    "discount_amount": "252.90",
    "total_amount": "5310.89",
    "status": "completed",
    "user": {...},
    "items": [...],
    "payments": [...]
  }
}
```

---

### 4. Update Sale Status (Void Sale)
```http
PUT /api/sales/{id}/status
Authorization: Bearer {token} (Admin only)
Content-Type: application/json
```

**Request Body:**
```json
{
  "status": "void",
  "reason": "Customer returned all items"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Sale status updated successfully",
  "data": {
    "id": 1,
    "status": "void"
  }
}
```

**Business Rules:**
1. **Admin Only** - Only administrators can void sales
2. **Same Day Only** - Can only void sales from today
3. **Inventory Restoration** - Automatically adds stock back
4. **Stock Movement** - Logs return with (VOID) reference
5. **Cannot Re-void** - Already void sales cannot be changed

**When to Void:**
- Customer cancels immediately after checkout
- Wrong sale processed
- System error during checkout
- Full refund (same day only)

**Note:** For returns after the sale day, use the Returns module (Phase 5)

---

### 5. Get Today's Sales Summary
```http
GET /api/sales/today
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "date": "2026-01-08",
    "total_sales": 15,
    "total_revenue": 12450.50,
    "total_tax": 1132.77,
    "total_discount": 622.50,
    "sales": [...]
  }
}
```

**Use Case:**
- End of day cashier report
- Daily revenue tracking
- Quick performance overview

---

### 6. Get Sales Statistics
```http
GET /api/sales/statistics
Authorization: Bearer {token}
```

**Query Parameters:**
- `period` - today, week, month, year (default: today)

**Examples:**
```http
GET /api/sales/statistics?period=today
GET /api/sales/statistics?period=week
GET /api/sales/statistics?period=month
GET /api/sales/statistics?period=year
```

**Response:**
```json
{
  "success": true,
  "data": {
    "period": "month",
    "total_sales": 245,
    "total_revenue": 156780.50,
    "total_tax": 14252.77,
    "total_discount": 7839.00,
    "average_sale": 639.92,
    "top_products": [
      {
        "id": 1,
        "name": "MacBook Pro 16\"",
        "total_quantity": 45,
        "total_revenue": "112455.00"
      },
      {
        "id": 2,
        "name": "iPhone 15 Pro",
        "total_quantity": 38,
        "total_revenue": "45562.00"
      }
    ]
  }
}
```

**Use Case:**
- Business performance analysis
- Identify best-selling products
- Revenue trends
- Inventory planning

---

## 💡 Calculation Examples

### Example 1: Simple Sale with Tax
```
Items:
- MacBook Pro x2 @ $2499 = $4998
- USB Cable x1 @ $59.99 = $59.99

Subtotal: $5057.99
Tax (10%): $505.80
Discount: $0
─────────────────
TOTAL: $5563.79
```

### Example 2: Sale with Percentage Discount
```
Subtotal: $5057.99
Tax (10%): $505.80
Discount (5%): -$252.90
─────────────────
TOTAL: $5310.89
```

### Example 3: Sale with Fixed Discount
```
Subtotal: $5057.99
Tax (10%): $505.80
Discount (Fixed $100): -$100.00
─────────────────
TOTAL: $5463.79
```

### Example 4: Split Payment
```
Total: $5310.89

Payments:
- Cash: $3000.00
- Card: $2310.89
─────────────────
Total Paid: $5310.89 ✅
```

---

## 🧪 Testing Guide

### Test 1: Complete Simple Sale

```bash
# Get auth token
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@pos.com","password":"password"}' \
  | jq -r '.token')

# Create sale
curl -X POST http://localhost:8000/api/sales \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {
        "product_id": 1,
        "quantity": 1,
        "unit_price": 2499.00
      }
    ],
    "tax_percentage": 10,
    "payments": [
      {
        "payment_method": "cash",
        "amount": 2748.90
      }
    ]
  }' | jq '.'
```

**Expected:**
- ✅ Sale created with invoice number
- ✅ Inventory deducted (check product 1)
- ✅ Stock movement logged
- ✅ Payment recorded

---

### Test 2: Sale with Discount

```bash
curl -X POST http://localhost:8000/api/sales \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {
        "product_id": 2,
        "quantity": 2,
        "unit_price": 1199.00
      }
    ],
    "tax_percentage": 10,
    "discount_type": "percentage",
    "discount_value": 10,
    "payments": [
      {
        "payment_method": "card",
        "amount": 2377.02,
        "reference_number": "CARD-12345"
      }
    ]
  }' | jq '.'
```

**Calculation:**
```
Subtotal: $2398.00 (2 x $1199)
Tax 10%: $239.80
Discount 10%: -$239.80
Total: $2398.00
```

---

### Test 3: Multiple Items with Split Payment

```bash
curl -X POST http://localhost:8000/api/sales \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {
        "product_id": 1,
        "quantity": 1,
        "unit_price": 2499.00
      },
      {
        "product_id": 3,
        "quantity": 2,
        "unit_price": 59.99
      }
    ],
    "tax_percentage": 8,
    "discount_type": "fixed",
    "discount_value": 50,
    "payments": [
      {
        "payment_method": "cash",
        "amount": 2000.00
      },
      {
        "payment_method": "card",
        "amount": 793.83,
        "reference_number": "VISA-9876"
      }
    ]
  }' | jq '.'
```

---

### Test 4: Check Inventory After Sale

```bash
# Check product inventory
curl -X GET http://localhost:8000/api/inventory/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

# Expected: quantity reduced, recent movement shows sale
```

---

### Test 5: Get Today's Summary

```bash
curl -X GET http://localhost:8000/api/sales/today \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

# Expected: All sales made today with totals
```

---

### Test 6: Void a Sale (Admin Only)

```bash
curl -X PUT http://localhost:8000/api/sales/1/status \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "void",
    "reason": "Customer cancellation"
  }' | jq '.'

# Expected: Sale voided, inventory restored
```

---

### Test 7: Error - Insufficient Stock

```bash
curl -X POST http://localhost:8000/api/sales \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {
        "product_id": 1,
        "quantity": 999,
        "unit_price": 2499.00
      }
    ],
    "payments": [
      {
        "payment_method": "cash",
        "amount": 999999.00
      }
    ]
  }' | jq '.'

# Expected: Error message about insufficient stock
```

---

### Test 8: Get Sales Statistics

```bash
# Today
curl -X GET http://localhost:8000/api/sales/statistics?period=today \
  -H "Authorization: Bearer $TOKEN" | jq '.'

# This month
curl -X GET http://localhost:8000/api/sales/statistics?period=month \
  -H "Authorization: Bearer $TOKEN" | jq '.'
```

---

## 🔐 Authorization

| Endpoint | Admin | Cashier |
|----------|-------|---------|
| Create Sale | ✅ | ✅ |
| View Sales | ✅ | ✅ |
| Void Sale | ✅ | ❌ |
| View Statistics | ✅ | ✅ |

---

## 🎓 Business Logic Deep Dive

### 1. Why Database Transactions?

```php
DB::beginTransaction();
try {
    // 1. Create sale
    // 2. Create sale items
    // 3. Deduct inventory
    // 4. Log stock movements
    // 5. Record payments
    DB::commit(); // All or nothing
} catch (Exception $e) {
    DB::rollback(); // Undo everything
}
```

**Why this matters:**
- If inventory fails → Sale rollback
- If payment fails → Inventory restored
- **Data integrity guaranteed** ✅

---

### 2. Invoice Number Generation

```
Format: INV-YYYY-NNNN

Examples:
- INV-2026-0001 (First sale of 2026)
- INV-2026-0002 (Second sale)
- INV-2027-0001 (Resets each year)
```

**Algorithm:**
1. Get last sale of current year
2. Extract number from invoice
3. Increment by 1
4. Pad with zeros (4 digits)

---

### 3. Discount Calculation

**Percentage Discount:**
```
Subtotal: $1000
Discount 10%: $1000 × 0.10 = $100
```

**Fixed Discount:**
```
Subtotal: $1000
Discount: $50 (fixed)
```

**Order of Operations:**
```
1. Calculate Subtotal (sum all items)
2. Apply Tax to Subtotal
3. Apply Discount to Subtotal
4. Final = Subtotal + Tax - Discount
```

---

### 4. Stock Movement Tracking

Every sale creates:
```json
{
  "type": "out",
  "quantity": 2,
  "reference": "INV-2026-0001",
  "notes": "Sale transaction",
  "user_id": 1
}
```

When voiding:
```json
{
  "type": "in",
  "quantity": 2,
  "reference": "INV-2026-0001 (VOID)",
  "notes": "Sale voided: Customer cancellation",
  "user_id": 1
}
```

**Complete audit trail for:**
- Accountability
- Reconciliation
- Loss prevention
- Business insights

---

### 5. Payment Validation

```php
$totalPayment = array_sum($payments);
$saleTotal = $subtotal + $tax - $discount;

// Allow 1 cent difference (rounding)
if (abs($totalPayment - $saleTotal) > 0.01) {
    throw new Exception("Payment mismatch");
}
```

**Why 1 cent tolerance?**
- Floating-point arithmetic
- Rounding differences
- Real-world practicality

---

## ⚠️ Common Errors & Solutions

### Error 1: Insufficient Stock
```json
{
  "error": "Insufficient stock for 'Product Name'. Available: 5, Requested: 10"
}
```
**Solution:** Check inventory before adding to cart

---

### Error 2: Payment Mismatch
```json
{
  "error": "Payment mismatch. Total: 5310.89, Paid: 5000.00"
}
```
**Solution:** Ensure total payments equal sale total

---

### Error 3: Cannot Void Old Sales
```json
{
  "message": "Cannot void sales from previous days"
}
```
**Solution:** Use Returns module for old sales (Phase 5)

---

### Error 4: Inactive Product
```json
{
  "error": "Product 'Name' is not active"
}
```
**Solution:** Activate product or use different product

---

## 📊 Real-World Scenarios

### Scenario 1: Regular Sale
**Situation:** Customer buys laptop and accessories

```json
{
  "items": [
    {"product_id": 1, "quantity": 1, "unit_price": 2499.00},
    {"product_id": 3, "quantity": 1, "unit_price": 59.99}
  ],
  "tax_percentage": 10,
  "payments": [{"payment_method": "card", "amount": 2813.79}]
}
```

---

### Scenario 2: Employee Discount
**Situation:** Staff member gets 20% discount

```json
{
  "items": [...],
  "tax_percentage": 10,
  "discount_type": "percentage",
  "discount_value": 20,
  "payments": [...]
}
```

---

### Scenario 3: Promotional Sale
**Situation:** Fixed $50 off coupon

```json
{
  "items": [...],
  "discount_type": "fixed",
  "discount_value": 50,
  "payments": [...]
}
```

---

### Scenario 4: Split Payment
**Situation:** Customer pays partly cash, partly card

```json
{
  "payments": [
    {"payment_method": "cash", "amount": 1000.00},
    {"payment_method": "card", "amount": 1813.79, "reference_number": "TXN-123"}
  ]
}
```

---

### Scenario 5: Same-Day Return
**Situation:** Customer changes mind immediately

```bash
# Void the sale
PUT /api/sales/1/status
{
  "status": "void",
  "reason": "Customer changed mind"
}

# Result:
# - Sale marked void
# - Inventory restored
# - Stock movements logged
```

---

## 🚀 Performance Tips

1. **Use Pagination** - Don't load all sales at once
2. **Date Filters** - Narrow down large datasets
3. **Index on invoice_number** - Fast lookups
4. **Cache Statistics** - Store daily summaries
5. **Eager Load Relations** - Use `with()` to prevent N+1

---

## 🎯 Key Learnings

### 1. Transaction Safety
Always wrap multi-step operations in transactions to prevent data corruption.

### 2. Validation First
Validate ALL data before processing to fail fast.

### 3. Audit Everything
Track who did what and when for accountability.

### 4. Business Rules Matter
Same-day void vs returns - different processes for different timeframes.

### 5. Payment Flexibility
Support multiple payment methods and split payments.

---

## 📈 What's Next: Phase 5

**Customer & Order Management:**
- Customer records
- Order history
- Return processing
- Customer loyalty program
- Credit sales

---

**Phase 4 Complete! Ready for Phase 5? Type "phase 5" when ready! 🎯**
