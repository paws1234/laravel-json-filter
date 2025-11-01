## 💡 Comprehensive Usage Guide

The `jsonFilter()` macro adds a fluent, driver-agnostic way to query JSON fields in Laravel.

It automatically detects whether you're using **MySQL** or **PostgreSQL** and translates  
your filter to the correct syntax — so you can focus on clean, expressive Eloquent queries.

---

### 🧱 Basic Example

```php
use App\Models\User;

$activeUsers = User::query()
    ->jsonFilter('meta->status', '=', 'active')
    ->get();
