# TrustedU ERP Deployment & Update Guide (cPanel)

এই ফাইলটি আপনাকে ভবিষ্যতে প্রজেক্ট আপডেট এবং মেইনটেইন করতে সাহায্য করবে।

## ১. সাধারণ আপডেট (Code Update)
যখন আপনি লোকাল পিসি থেকে গিটহাবে (`GitHub`) কোড পুশ করবেন, তখন সার্ভারে নিচের ধাপগুলো অনুসরণ করুন:

1. cPanel-এ লগইন করুন।
2. **Git™ Version Control**-এ যান।
3. আপনার রিপোজিটরিটির পাশে **Manage** বাটনে ক্লিক করুন।
4. **Pull or Deploy** ট্যাব থেকে **Update from Remote** (বা **Pull**) বাটনে ক্লিক করুন।

---

## ২. পরিবর্তনগুলো কার্যকর করা (Essential Commands)
কোড পুল করার পর পরিবর্তনগুলো সাইটে দেখার জন্য আপনাকে cPanel **Terminal**-এ গিয়ে নিচের কমান্ডগুলো দিতে হবে:

```bash
# প্রজেক্ট ফোল্ডারে প্রবেশ করুন
cd ~/erp_core

# কনফিগারেশন এবং ক্যাশ ক্লিয়ার করুন (অবশ্যই করবেন)
php artisan optimize

# যদি নতুন কোনো ছবি বা CSS/JS অ্যাড করেন, তবে সেগুলো public_html এ সিনক্রোনাইজ করুন
cp -r ~/erp_core/public/* ~/public_html/
cp ~/erp_core/public/.htaccess ~/public_html/
```

---

## ৩. বিশেষ ক্ষেত্রে কমান্ড (Advanced Updates)

### ক) যদি কোনো নতুন লাইব্রেরি (Package) যোগ করেন:
```bash
php composer.phar install --optimize-autoloader --no-dev
```

### খ) যদি ডাটাবেস টেবিল পরিবর্তন (Migration) করেন:
```bash
php artisan migrate
```

### গ) যদি কোনো পারমিশন ইস্যু হয়:
```bash
chmod -R 775 storage bootstrap/cache
```

---

## ৪. মনে রাখার বিষয়
- **Security:** লাইভ সাইটে সবসময় `.env` ফাইলে `APP_DEBUG=false` রাখবেন।
- **DB Connection:** ডাটাবেসে সমস্যা হলে `.env` ফাইলে `DB_HOST=localhost` অথবা `DB_HOST=127.0.0.1` চেক করে দেখবেন।
- **Assets:** লোগো বা ছবি না দেখা গেলে `php artisan storage:link` কমান্ডটি চেক করবেন।

---
*Happy Coding! - TrustedU ERP Team*
