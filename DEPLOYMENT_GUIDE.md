# TrustedU ERP — Deployment & Update Guide (cPanel)

এই ফাইলটি আপনাকে ভবিষ্যতে প্রজেক্ট আপডেট এবং মেইনটেইন করতে সাহায্য করবে।

---

## 🖥️ সার্ভার ইনফর্মেশন

| বিষয় | তথ্য |
|-------|------|
| **cPanel Host** | turbo3-bd |
| **SSH User** | trusteduerp |
| **Project Directory** | `~/erp_core` |
| **Public HTML** | `~/public_html` |
| **PHP Path** | `/usr/local/bin/php` (v8.4) |
| **Composer Path** | `~/composer.phar` |
| **Git Remote** | `https://github.com/ZahidBabu95/TrustedU-ERP.git` |

---

## ১. সাধারণ আপডেট (শুধু Code পরিবর্তন)

যখন শুধু PHP/Blade/CSS/JS কোড পরিবর্তন করেন (কোনো নতুন প্যাকেজ বা ডাটাবেস পরিবর্তন নেই):

### cPanel GUI থেকে:
1. cPanel-এ লগইন করুন
2. **Git™ Version Control** → **Manage** → **Pull or Deploy** → **Update from Remote**

### অথবা SSH Terminal থেকে:
```bash
cd ~/erp_core
git pull origin main
php artisan optimize:clear
cp -r ~/erp_core/public/* ~/public_html/
cp ~/erp_core/public/.htaccess ~/public_html/
```

---

## ২. ফুল আপডেট (নতুন প্যাকেজ + Migration সহ)

যখন নতুন Composer প্যাকেজ যোগ হয় অথবা ডাটাবেস টেবিল পরিবর্তন হয়:

```bash
# ১. প্রজেক্ট ফোল্ডারে যান
cd ~/erp_core

# ২. কোড পুল করুন
git pull origin main

# ৩. নতুন প্যাকেজ ইনস্টল করুন (composer.lock পরিবর্তন হলে অবশ্যই)
php ~/composer.phar install --no-dev --optimize-autoloader

# ৪. ডাটাবেস মাইগ্রেশন চালান (নতুন টেবিল/কলাম থাকলে)
php artisan migrate --force

# ৫. ক্যাশ ক্লিয়ার করুন
php artisan optimize:clear

# ৬. পাবলিক ফাইল কপি করুন (নতুন CSS/JS/ছবি থাকলে)
cp -r ~/erp_core/public/* ~/public_html/
cp ~/erp_core/public/.htaccess ~/public_html/
```

---

## ৩. কখন কোন কমান্ড লাগবে?

| পরিবর্তনের ধরন | কমান্ড |
|----------------|--------|
| শুধু PHP/Blade কোড | `git pull` + `php artisan optimize:clear` |
| নতুন CSS/JS/ছবি | + `cp -r ~/erp_core/public/* ~/public_html/` |
| নতুন Composer প্যাকেজ | + `php ~/composer.phar install --no-dev --optimize-autoloader` |
| নতুন DB টেবিল/কলাম | + `php artisan migrate --force` |
| Storage কনফিগারেশন | Git pull + Admin Panel → System Settings → Storage ট্যাব |

---

## ৪. বিশেষ কমান্ড (Advanced)

### Composer আপডেট (প্রথমবার বা Composer নতুন ভার্সন দরকার হলে):
```bash
cd ~
curl -sS https://getcomposer.org/installer | php
```

### Storage Link তৈরি (প্রথমবারে একবার):
```bash
cd ~/erp_core
php artisan storage:link
```

### পারমিশন ফিক্স:
```bash
chmod -R 775 ~/erp_core/storage ~/erp_core/bootstrap/cache
```

### Filament ক্যাশ রিফ্রেশ:
```bash
php artisan filament:optimize-clear
php artisan icons:cache
```

---

## ৫. Cloudflare R2 Storage

R2 স্টোরেজ ডাটাবেসে কনফিগার করা আছে। কোনো `.env` পরিবর্তন লাগবে না।

- **কনফিগার করতে:** `/admin/system-settings` → **Storage** ট্যাব
- **বর্তমান সেটিং:** Cloudflare R2 (credentials encrypted in DB)
- **পুরানো ফাইল:** Local storage-এই থাকবে
- **নতুন আপলোড:** R2 bucket-এ যাবে → CDN দিয়ে serve হবে

> ⚠️ লাইভ সার্ভারে প্রথমবার System Settings → Storage ট্যাবে গিয়ে R2 credentials সেট করুন (যদি লোকাল ও লাইভে আলাদা DB হয়)।

---

## ৬. মনে রাখার বিষয়

- **Security:** লাইভ সাইটে `.env`-তে `APP_DEBUG=false` রাখুন
- **DB Connection:** সমস্যা হলে `.env`-তে `DB_HOST=localhost` চেক করুন
- **Assets দেখা যাচ্ছে না:** `php artisan storage:link` চালান
- **পারমিশন সমস্যা:** `chmod -R 775 storage bootstrap/cache` চালান
- **Composer not found:** `php ~/composer.phar` ব্যবহার করুন (`composer` এর বদলে)

---

## ৭. জরুরি ট্রাবলশুটিং

### ৫০০ Error:
```bash
cd ~/erp_core
php artisan optimize:clear
chmod -R 775 storage bootstrap/cache
tail -50 storage/logs/laravel.log
```

### Blank Page:
```bash
php artisan view:clear
php artisan config:clear
```

### Migration Error:
```bash
php artisan migrate:status    # কোন migration পেন্ডিং দেখুন
php artisan migrate --force   # চালান
```

---
*Last Updated: March 19, 2026 — TrustedU ERP Team* 🚀
