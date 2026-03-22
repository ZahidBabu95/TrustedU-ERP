# 🚀 TrustedU ERP — Deployment & Update Guide

> **Last Updated:** March 22, 2026  
> **Author:** TrustedU ERP Team  
> **Version:** 2.2

---

## 📋 Table of Contents

1. [Server Information](#-server-information)
2. [Directory Structure](#-directory-structure)
3. [Important Files (DO NOT OVERWRITE)](#-important-files-do-not-overwrite)
4. [Regular Code Update (Daily)](#1-regular-code-update-সাধারণ-আপডেট)
5. [Full Update (With Packages & Migration)](#2-full-update-ফুল-আপডেট)
6. [Public Files Update](#3-public-files-update)
7. [Quick Reference Table](#-quick-reference-table)
8. [Cloudflare R2 Storage](#-cloudflare-r2-storage)
9. [First Time Setup](#-first-time-setup-নতুন-সার্ভারে)
10. [Troubleshooting](#-troubleshooting)
11. [Common Mistakes to Avoid](#-common-mistakes-to-avoid)
12. [Version History](#-version-history)

---

## 🖥️ Server Information

| Item | Value |
|------|-------|
| **cPanel Host** | turbo3-bd |
| **SSH User** | `trusteduerp` |
| **Domain** | `trusteduerp.com` |
| **Project Directory** | `~/erp_core` |
| **Public HTML** | `~/public_html` |
| **CLI PHP** | `/usr/local/bin/php` (v8.4) |
| **Web PHP** | PHP 8.3 (ea-php83) via cPanel MultiPHP |
| **Composer** | `~/composer.phar` |
| **Git Remote** | `https://github.com/ZahidBabu95/TrustedU-ERP.git` |
| **Git Branch** | `main` |

> ⚠️ **PHP Version Note:** CLI তে PHP 8.4 আছে কিন্তু Web Server-এ PHP 8.3 চলে।  
> Composer কমান্ডে সবসময় `--ignore-platform-req=php` ফ্ল্যাগ দিতে হবে।

---

## 📁 Directory Structure

```
/home/trusteduerp/
├── erp_core/                 ← Laravel Project (Git Repository)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/               ← Laravel-এর original public folder
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env                  ← Environment Configuration
│   ├── composer.json
│   └── composer.lock
│
├── public_html/              ← Web Root (Apache serves from here)
│   ├── index.php             ← ⚠️ CUSTOM — erp_core path দেওয়া আছে
│   ├── .htaccess             ← ⚠️ CUSTOM — overwrite করবেন না
│   ├── storage/              ← Symlink → ~/erp_core/storage/app/public
│   ├── css/
│   ├── js/
│   ├── fonts/
│   └── ...other assets
│
└── composer.phar             ← Composer executable
```

---

## 🔴 Important Files (DO NOT OVERWRITE)

### `~/public_html/index.php` — Custom Entry Point
এই ফাইলটি customized। এটি `../erp_core/` path ব্যবহার করে। **কখনো ওভাররাইট করবেন না!**

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../erp_core/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../erp_core/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../erp_core/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

### `~/public_html/.htaccess` — Apache Configuration
এটিও custom হতে পারে। সাবধানে handle করুন।

### `~/public_html/storage/` — Symlink
এটি একটি symbolic link (`~/erp_core/storage/app/public` → `~/public_html/storage`)।  
**এটি ডিলিট বা ওভাররাইট করবেন না!**

---

## 1️⃣ Regular Code Update (সাধারণ আপডেট)

**কখন:** শুধু PHP/Blade/CSS/JS কোড পরিবর্তন হলে, কোনো নতুন প্যাকেজ বা DB পরিবর্তন নেই।

### Option A: cPanel GUI থেকে
1. cPanel → **Git™ Version Control** → **Manage**
2. **Pull or Deploy** ট্যাব → **Update from Remote** ক্লিক

### Option B: SSH Terminal থেকে
```bash
cd ~/erp_core
git pull origin main
php artisan optimize:clear
```

> **Note:** শুধু কোড পরিবর্তনে `composer install` বা `migrate` দরকার নেই।

---

## 2️⃣ Full Update (ফুল আপডেট)

**কখন:** নতুন Composer প্যাকেজ যোগ হলে অথবা নতুন Database Migration থাকলে।

```bash
# ১. প্রজেক্ট ফোল্ডারে যান
cd ~/erp_core

# ২. কোড পুল করুন
git pull origin main

# ৩. নতুন প্যাকেজ ইনস্টল করুন
# ⚠️ --ignore-platform-req=php ফ্ল্যাগ অবশ্যই দিন (Web PHP 8.3 এর জন্য)
php ~/composer.phar install --no-dev --optimize-autoloader --ignore-platform-req=php

# ৪. ডাটাবেস মাইগ্রেশন চালান
php artisan migrate --force

# ৫. ক্যাশ ক্লিয়ার করুন
php artisan optimize:clear
```

---

## 3️⃣ Public Files Update

**কখন:** নতুন CSS, JS, ছবি, বা Filament assets পরিবর্তন হলে।

```bash
cd ~/erp_core

# ⚠️ --exclude দিয়ে index.php, .htaccess, storage কে বাদ দিন
rsync -av --exclude='storage' --exclude='.htaccess' public/ ~/public_html/
```

### ❌ এটি ব্যবহার করবেন না:
```bash
# ভুল! — এটি index.php ওভাররাইট করে সাইট ডাউন করবে!
cp -r ~/erp_core/public/* ~/public_html/
```

### ✅ যদি `rsync` না থাকে:
```bash
# শুধু specific ফোল্ডারগুলো কপি করুন
cp -r ~/erp_core/public/css ~/public_html/
cp -r ~/erp_core/public/js ~/public_html/
cp -r ~/erp_core/public/fonts ~/public_html/
cp -r ~/erp_core/public/images ~/public_html/ 2>/dev/null
# ⚠️ index.php এবং .htaccess কপি করবেন না!
```

---

## 📊 Quick Reference Table

| পরিবর্তনের ধরন | কমান্ড |
|----------------|--------|
| শুধু PHP/Blade কোড | `git pull` → `php artisan optimize:clear` |
| নতুন CSS/JS/Assets | + `rsync -av --exclude='storage' --exclude='.htaccess' public/ ~/public_html/` |
| নতুন Composer প্যাকেজ | + `php ~/composer.phar install --no-dev --optimize-autoloader --ignore-platform-req=php` |
| নতুন DB টেবিল/কলাম | + `php artisan migrate --force` |
| R2 Storage Config | Admin Panel → System Settings → Storage ট্যাব |

---

## ☁️ Cloudflare R2 Storage

R2 credentials **ডাটাবেসে encrypted** অবস্থায় সেভ থাকে। `.env` ফাইলে কিছু দিতে হয় না।

### কনফিগার করতে:
**Admin Panel** → `/admin/system-settings` → **Storage** ট্যাব

### কিভাবে কাজ করে:
- **পুরানো ফাইল:** Local storage-এই থাকবে (`storage/app/public/`)
- **নতুন আপলোড:** Cloudflare R2 bucket-এ যাবে → CDN URL দিয়ে serve হবে

### ⚠️ গুরুত্বপূর্ণ:
- লোকাল ও লাইভে **আলাদা DB** হলে → লাইভে আবার R2 credentials সেট করতে হবে
- একই DB হলে → স্বয়ংক্রিয়ভাবে কাজ করবে

---

## 🆕 First Time Setup (নতুন সার্ভারে)

নতুন সার্ভারে বা ফ্রেশ সেটআপে এই ধাপগুলো অনুসরণ করুন:

```bash
# ১. Composer ডাউনলোড করুন
cd ~
curl -sS https://getcomposer.org/installer | php

# ২. Git Clone করুন
git clone https://github.com/ZahidBabu95/TrustedU-ERP.git erp_core

# ৩. .env ফাইল তৈরি করুন
cd ~/erp_core
cp .env.example .env
# .env ফাইলে DB credentials, APP_URL ইত্যাদি সেট করুন

# ৪. Composer Install
php ~/composer.phar install --no-dev --optimize-autoloader --ignore-platform-req=php

# ৫. App Key Generate
php artisan key:generate

# ৬. Migration চালান
php artisan migrate --force

# ৭. Storage Link তৈরি করুন
php artisan storage:link

# ৮. Permissions সেট করুন
chmod -R 775 storage bootstrap/cache

# ৯. public_html সেটআপ করুন
# index.php কাস্টমাইজ করুন (উপরের "Important Files" সেকশন দেখুন)
# Assets কপি করুন (rsync ব্যবহার করুন)
rsync -av --exclude='storage' --exclude='.htaccess' public/ ~/public_html/

# ১০. স্টোরেজ সিমলিংক public_html-এ তৈরি করুন
ln -s ~/erp_core/storage/app/public ~/public_html/storage

# ১১. Cache Optimize
php artisan optimize:clear
```

---

## 🔧 Troubleshooting

### 🔴 500 Internal Server Error
```bash
cd ~/erp_core

# ১. Laravel Error Log চেক করুন
tail -50 storage/logs/laravel.log

# ২. ক্যাশ ক্লিয়ার করুন
php artisan optimize:clear

# ৩. Permissions চেক করুন
chmod -R 775 storage bootstrap/cache

# ৪. index.php ঠিক আছে কিনা চেক করুন
cat ~/public_html/index.php
# '../erp_core/' path থাকতে হবে, শুধু '../' হলে ভুল!
```

### 🔴 Composer: PHP >= 8.4.0 Required
```bash
# --ignore-platform-req=php ফ্ল্যাগ দিন
php ~/composer.phar install --no-dev --optimize-autoloader --ignore-platform-req=php
```

### 🔴 Composer: Command Not Found
```bash
# Composer ডাউনলোড করুন
cd ~
curl -sS https://getcomposer.org/installer | php
# তারপর php ~/composer.phar ব্যবহার করুন
```

### 🔴 Blank Page / White Screen
```bash
php artisan view:clear
php artisan config:clear
php artisan route:clear
tail -20 storage/logs/laravel.log
```

### 🔴 Assets (CSS/JS) লোড হচ্ছে না
```bash
cd ~/erp_core
rsync -av --exclude='storage' --exclude='.htaccess' public/ ~/public_html/
```

### 🔴 Storage / Uploaded Files দেখা যাচ্ছে না
```bash
# Storage link চেক করুন
ls -la ~/public_html/storage
# এটি ~/erp_core/storage/app/public এ point করা উচিত

# যদি না থাকে, তৈরি করুন:
ln -s ~/erp_core/storage/app/public ~/public_html/storage
```

### 🔴 Migration Error
```bash
php artisan migrate:status    # কোন migration পেন্ডিং দেখুন
php artisan migrate --force   # চালান
```

---

## ⛔ Common Mistakes to Avoid

| ❌ ভুল | ✅ সঠিক |
|--------|---------|
| `cp -r ~/erp_core/public/* ~/public_html/` | `rsync -av --exclude='storage' --exclude='.htaccess' public/ ~/public_html/` |
| `composer install` (without flag) | `php ~/composer.phar install --ignore-platform-req=php` |
| `~/public_html/index.php` ওভাররাইট করা | **কখনো ওভাররাইট করবেন না!** এতে `../erp_core/` path আছে |
| `~/public_html/storage/` ডিলিট করা | এটি symlink — ডিলিট করলে uploads ভেঙে যাবে |
| `.env`-তে `APP_DEBUG=true` রাখা (production) | সবসময় `APP_DEBUG=false` রাখুন |

---

## 📝 চেকলিস্ট (প্রতিটি Deploy-এর জন্য)

- [ ] `git pull origin main` করেছি
- [ ] `composer install` লাগলে `--ignore-platform-req=php` সহ চালিয়েছি
- [ ] `php artisan migrate --force` লাগলে চালিয়েছি
- [ ] `php artisan optimize:clear` চালিয়েছি
- [ ] Assets পরিবর্তন থাকলে `rsync` দিয়ে কপি করেছি (index.php বাদে!)
- [ ] সাইট ব্রাউজারে চেক করেছি
- [ ] `.env`-তে `APP_DEBUG=false` আছে

---

## 📦 Version History

### v2.2 — March 22, 2026 (AI Chatbot & Knowledge Base)

**নতুন ফিচারসমূহ:**
- 🤖 **AI Chatbot** — Gemini AI powered চ্যাটবট (ওয়েবসাইটে floating widget)
- 📚 **Chatbot Knowledge Base Engine** — Platform → Chatbot Engine মেনুতে Q&A ম্যানেজমেন্ট
- 🔑 **AI API Settings** — System Settings → Integrations → AI Chatbot Configuration
- 📊 **CRM Flow Upgrades** — Lead → Deal → Client পাইপলাইন উন্নতি
- 🔍 **Google Analytics Fix** — GA tracking এখন সঠিকভাবে কাজ করছে

**নতুন Migrations (৩টি):**
- `2026_03_20_101359_add_crm_flow_fields.php`
- `2026_03_22_032054_create_chat_conversations_and_messages_tables.php`
- `2026_03_22_035324_create_chatbot_knowledge_bases_table.php`

**ডেপ্লয় পদ্ধতি:** Full Update (Migration আছে)
```bash
cd ~/erp_core
git pull origin main
php ~/composer.phar install --no-dev --optimize-autoloader --ignore-platform-req=php
php artisan migrate --force
php artisan optimize:clear
```

**ডেপ্লয়ের পর করণীয়:**
1. `/admin/system-settings` → Integrations → AI Chatbot Configuration → Gemini API Key সেট করুন
2. `/admin/chatbot-knowledge` → "Load Defaults" বাটনে ক্লিক করে ডিফল্ট Q&A লোড করুন
3. চ্যাটবট টেস্ট করুন — ওয়েবসাইটে floating বাটনে ক্লিক করে দেখুন

---

### v2.1 — March 20, 2026
- Dashboard ও Website Dashboard আলাদা করা হয়েছে
- Login As User ফিচার (Super Admin only)
- SMS Module enhancements
- Cloudflare R2 Storage integration

---

*Happy Deploying! 🚀 — TrustedU ERP Team*
