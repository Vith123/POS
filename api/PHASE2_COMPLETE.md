# 🏪 POS System - Phase 2 Complete!

## ✅ What We've Built

You now have a complete Laravel backend foundation for your POS system with:

### 🗄️ **Database Structure (8 Tables)**
- ✅ `roles` - User roles and permissions
- ✅ `users` - Admin and cashier accounts
- ✅ `categories` - Product categories
- ✅ `products` - All sellable items
- ✅ `inventories` - Stock tracking
- ✅ `stock_movements` - Inventory history
- ✅ `sales` - Transaction records
- ✅ `sale_items` - Products in each sale
- ✅ `payments` - Payment records

### 🔗 **Relationships Explained**

```
USER (Admin/Cashier)
  ├── belongs to → ROLE
  ├── has many → SALES (cashier who made the sale)
  └── has many → STOCK_MOVEMENTS (who recorded it)

PRODUCT
  ├── belongs to → CATEGORY
  ├── has one → INVENTORY
  ├── has many → STOCK_MOVEMENTS
  └── has many → SALE_ITEMS

SALE
  ├── belongs to → USER (cashier)
  ├── has many → SALE_ITEMS
  └── has many → PAYMENTS
```

---

## 📊 Sample Data Loaded

Your database now contains:

### 👥 **Users (Login Credentials)**
```
🔐 ADMIN
Email: admin@pos.com
Password: password
Permissions: Full access

🔐 CASHIER
Email: cashier@pos.com
Password: password
Permissions: Sales only
```

### 📦 **Products (8 items)**
```
ELECTRONICS:
- MacBook Pro 16" ($2,499) - Stock: 15
- Logitech Mouse ($99.99) - Stock: 50
- Mechanical Keyboard ($79.99) - Stock: 30
- Wireless Headphones ($199.99) - Stock: 3 ⚠️ LOW STOCK

CLOTHING:
- Cotton T-Shirt ($19.99) - Stock: 100
- Blue Jeans ($49.99) - Stock: 60

FOOD & BEVERAGES:
- Coffee Beans ($24.99) - Stock: 40
- Mixed Nuts ($12.99) - Stock: 80
```

---

## 🔐 Authentication System (Laravel Sanctum)

### **How It Works:**

1. **User logs in** → API receives email & password
2. **System validates** → Checks credentials & user status
3. **Token generated** → Sanctum creates secure API token
4. **Frontend stores token** → Save in localStorage/cookies
5. **All requests** → Include token in headers
6. **Laravel verifies** → Validates token on every request

### **API Endpoints Created:**

| Method | Endpoint | Auth Required | Purpose |
|--------|----------|---------------|---------|
| POST | `/api/login` | No | User login |
| POST | `/api/logout` | Yes | User logout |
| GET | `/api/me` | Yes | Get current user info |

---

## 🧪 Test Your API

### **1. Start Laravel Server**
```bash
cd /Users/chhunchandevit/Desktop/project-all/laravel/POS
php artisan serve
```

### **2. Test Login (Using cURL or Postman)**

**Request:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@pos.com",
    "password": "password"
  }'
```

**Response:**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@pos.com",
    "role": "admin",
    "permissions": [
      "manage_products",
      "manage_users",
      "manage_inventory",
      "view_reports",
      "process_sales"
    ]
  },
  "token": "1|xxxxxxxxxxxxxxxxxxxx"
}
```

### **3. Test Get Current User (Protected Route)**

**Request:**
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### **4. Test Logout**

**Request:**
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

## 📚 Understanding the Code

### **Model Relationships Example**

```php
// Get user's sales
$user = User::find(1);
$sales = $user->sales; // All sales made by this user

// Get product with its inventory
$product = Product::with('inventory')->find(1);
echo $product->inventory->quantity; // Current stock

// Get sale with all items
$sale = Sale::with('saleItems.product')->find(1);
foreach ($sale->saleItems as $item) {
    echo $item->product->name;
    echo $item->quantity;
    echo $item->subtotal;
}
```

### **Using Helper Methods**

```php
// Check if product is low stock
$product = Product::find(1);
if ($product->isLowStock()) {
    echo "⚠️ Low stock alert!";
}

// Calculate profit margin
$margin = $product->getProfitMargin(); // Returns percentage

// Check user role
$user = User::find(1);
if ($user->isAdmin()) {
    // Allow access to admin features
}
```

### **Using Query Scopes**

```php
// Get only active products
$activeProducts = Product::active()->get();

// Search products
$results = Product::search('macbook')->get();

// Get today's sales
$todaySales = Sale::today()->get();

// Get low stock items
$lowStock = Inventory::lowStock()->get();
```

---

## 🔒 Role-Based Authorization

### **How Permissions Work:**

```php
// Check in Controller
if (!$user->role->hasPermission('manage_products')) {
    return response()->json(['error' => 'Unauthorized'], 403);
}

// Example in real code:
public function store(Request $request)
{
    $user = $request->user();
    
    if (!$user->isAdmin()) {
        return response()->json([
            'error' => 'Only admins can create products'
        ], 403);
    }
    
    // Create product...
}
```

---

## 📂 Project Structure

```
POS/
├── app/
│   ├── Http/Controllers/Api/
│   │   └── AuthController.php    ← Authentication logic
│   └── Models/
│       ├── User.php               ← User model with Sanctum
│       ├── Role.php               ← Roles & permissions
│       ├── Product.php            ← Products with helpers
│       ├── Inventory.php          ← Stock management
│       ├── Sale.php               ← Sales transactions
│       └── ...
├── database/
│   ├── migrations/                ← Database schema
│   └── seeders/
│       └── DatabaseSeeder.php     ← Sample data
└── routes/
    └── api.php                    ← API endpoints
```

---

## 🎯 Phase 2 Achievements

✅ **Database Design** - Clean, normalized structure  
✅ **Migrations** - All tables created with proper indexes  
✅ **Models** - Complete with relationships and helpers  
✅ **Authentication** - Laravel Sanctum configured  
✅ **Authorization** - Role-based permissions  
✅ **Sample Data** - Ready-to-use test data  
✅ **API Routes** - Authentication endpoints working  

---

## 🚀 What's Next? (Phase 3)

In Phase 3, we'll build:
- 📦 Product CRUD API (Create, Read, Update, Delete)
- 📷 Image upload for products
- 🏷️ Category management
- 📊 Inventory management API
- 📥 Stock in/out operations
- ⚠️ Low stock alerts
- 📜 Stock movement history

---

## 💡 Key Learnings from Phase 2

### **1. Why Separate Tables?**
- **Users & Roles** - One role, many users (flexible permissions)
- **Products & Inventories** - Separation of concerns (product info vs stock)
- **Sales & Sale Items** - One sale, many products (proper structure)

### **2. Why Foreign Keys?**
```php
$table->foreignId('category_id')->constrained();
```
- **Data integrity** - Can't delete category if products exist
- **Performance** - Faster queries with indexed relationships
- **Clarity** - Shows relationships in database structure

### **3. Why Indexes?**
```php
$table->index('barcode');
```
- **Speed** - Faster searches (barcode scanning, product lookup)
- **Performance** - Essential for reports and filtering

### **4. Why Laravel Sanctum?**
- **Stateless** - Perfect for API (no sessions needed)
- **Secure** - Token-based authentication
- **Simple** - Easy to implement and use

---

## 🐛 Common Issues & Solutions

### **Issue: Token not working**
**Solution:** Make sure to include header:
```
Authorization: Bearer YOUR_TOKEN
```

### **Issue: Migration errors**
**Solution:** Reset database:
```bash
php artisan migrate:fresh --seed
```

### **Issue: Can't login**
**Solution:** Check:
1. Email and password correct?
2. User is_active = true?
3. Role exists?

---

## 📖 Continue Learning

You now understand:
✅ Database relationships (one-to-many, one-to-one)  
✅ Why each table exists  
✅ How authentication works  
✅ Role-based permissions  
✅ API token management  

**Ready for Phase 3?** Let me know when you want to build the Product & Inventory Management system! 🚀
