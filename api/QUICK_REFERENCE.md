# 🚀 Quick Reference Guide - POS System

## 📍 Common Commands

### Start Server
```bash
cd /Users/chhunchandevit/Desktop/project-all/laravel/POS
php artisan serve
```

### Database Commands
```bash
# Run migrations
php artisan migrate

# Fresh migration (reset database)
php artisan migrate:fresh

# Seed database with sample data
php artisan db:seed

# Fresh migration + seed
php artisan migrate:fresh --seed
```

### Tinker (Laravel Console)
```bash
php artisan tinker

# Examples in tinker:
>>> User::all()
>>> Product::with('inventory')->get()
>>> Sale::today()->count()
```

---

## 🔐 Login Credentials

```
ADMIN:
Email: admin@pos.com
Password: password
```

```
CASHIER:
Email: cashier@pos.com
Password: password
```

---

## 📡 API Endpoints

### Base URL: `http://localhost:8000/api`

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/login` | No | User login |
| POST | `/logout` | Yes | User logout |
| GET | `/me` | Yes | Get current user |

---

## 🧪 Test API with cURL

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@pos.com",
    "password": "password"
  }'
```

### Get User Info
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Logout
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

## 📊 Database Queries (Tinker)

### Get all products with inventory
```php
Product::with('inventory')->get()
```

### Get low stock products
```php
Product::with('inventory')
    ->whereHas('inventory', function($q) {
        $q->whereColumn('quantity', '<=', 'min_stock_level');
    })
    ->get()
```

### Get today's sales
```php
Sale::today()->with(['saleItems.product', 'user'])->get()
```

### Get sales by cashier
```php
Sale::where('user_id', 2)->with('saleItems')->get()
```

### Count total products
```php
Product::count()
```

### Get active products only
```php
Product::active()->get()
```

### Search products
```php
Product::search('macbook')->get()
```

---

## 🗂️ File Locations

```
POS/
├── app/
│   ├── Models/                    ← All models
│   └── Http/Controllers/Api/      ← API controllers
├── database/
│   ├── migrations/                ← Database schema
│   └── seeders/                   ← Sample data
├── routes/
│   └── api.php                    ← API routes
├── PHASE2_COMPLETE.md             ← Full guide
├── DATABASE_SCHEMA.md             ← DB diagrams
└── LEARNING_SUMMARY.md            ← What you learned
```

---

## 🔍 Common Errors & Solutions

### Error: "SQLSTATE[HY000] [2002] Connection refused"
**Solution:** Check database configuration in `.env`

### Error: "Class 'App\Models\Role' not found"
**Solution:** Run `composer dump-autoload`

### Error: "Target class [api.login] does not exist"
**Solution:** Clear cache `php artisan route:clear`

### Error: "Unauthenticated"
**Solution:** Check token in Authorization header

---

## 💡 Useful Laravel Commands

```bash
# Generate model
php artisan make:model Product

# Generate controller
php artisan make:controller ProductController

# Generate migration
php artisan make:migration create_products_table

# Generate seeder
php artisan make:seeder ProductSeeder

# List all routes
php artisan route:list

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# View logs
tail -f storage/logs/laravel.log
```

---

## 📚 Model Relationships Quick Reference

### One-to-Many
```php
// Category has many Products
public function products() {
    return $this->hasMany(Product::class);
}

// Product belongs to Category
public function category() {
    return $this->belongsTo(Category::class);
}
```

### One-to-One
```php
// Product has one Inventory
public function inventory() {
    return $this->hasOne(Inventory::class);
}

// Inventory belongs to Product
public function product() {
    return $this->belongsTo(Product::class);
}
```

---

## 🎯 Next Steps

1. ✅ Phase 2 Complete - Database & Auth
2. ⏭️ Phase 3 - Product & Inventory Management
3. ⏭️ Phase 4 - Sales & Checkout System
4. ⏭️ Phase 5 - Transactions & Receipts
5. ⏭️ Phase 6 - Reports & Analytics

---

**Current Status: Phase 2 Complete! ✅**
**Ready for Phase 3 when you are! 🚀**
