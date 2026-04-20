# 🚀 ERP Module: Ultimate Dynamic Page Builder Plan

**Last Updated:** April 20, 2026
**Objective:** Transform existing static ERP module pages into fully dynamic, customizable, SaaS-style landing pages using Filament's Block Builder.

---

## 🛠️ Step 1: Database Migration
আমরা `erp_modules` টেবিলে একটি পাওয়ারফুল JSON কলাম যোগ করব যা সম্পূর্ণ পেজ লেআউট মেইনটেইন করবে।

- `dynamic_sections` (JSON, nullable): পেজের লিনিয়ার লেআউট (Blocks) সেভ করবে। 

*(বি.দ্র. পেজের অন্যান্য বেসিক ইনফো যেমন নাম, স্লাগ ইত্যাদির জন্য আগের কলামগুলোই বহাল থাকবে)*

---

## 🎛️ Step 2: Filament Admin Panel Updates (`ErpModuleForm.php`)
ফিলামেন্ট প্যানেলে মডিউল এডিটের সময় একটি **"Builder"** ফিল্ড যুক্ত করা হবে। নিচের ব্লকগুলো ড্র্যাগ-অ্যান্ড-ড্রপ করে যুক্ত করা যাবে:

1. **Hero Section Block:** 
   - Fields: Title, Subtitle, Background (Color/Image), Animations.
   - Dual Call To Action (CTA) buttons configuration.
2. **Features Grid Block:**
   - Repeater for adding multi-column feature cards (Icon, Title, Description).
   - Style selection (e.g. Glassmorphism or solid).
3. **Gallery Lightbox Block:**
   - Multiple image upload for software screenshots.
4. **Testimonials Block:**
   - Client reviews slider.
5. **Video Playlist Block:**
   - Sync YouTube URLs to show tutorial videos.
6. **FAQ Accordion Block:**
   - Question & Answer fields.

### 💡 "Default Layout" Strategy (প্রি-পপুলেটেড ব্লক)
* **নতুন মডিউলের ক্ষেত্রে:** Builder ফিল্ডটি ফাঁকা থাকবে না, বরং ডিফল্টভাবে Hero, Features, Video, এবং Bottom CTA ব্লকগুলো অ্যাড করা থাকবে। অ্যাডমিন শুধু ডেটা পাল্টাবেন।
* **পুরোনো মডিউলের ক্ষেত্রে:** একটি ওয়ান-টাইম মাইগ্রেশন কমান্ড চালু করা হবে, যা ডাটাবেসে থাকা আগের ফিচার্স এবং ইউটিউব ভিডিওগুলোকে এই নতুন `dynamic_sections` ব্লক ফরম্যাটে কনভার্ট করে দেবে।

---

## 🎨 Step 3: Frontend Implementation (`module-detail.blade.php`)
ফ্রন্টএন্ডে ব্লেড লুপ ব্যবহার করে ডাটাবেসের `dynamic_sections` রেন্ডার করা হবে।

* **Smart Rendering:** `dynamic_sections` লুপ হবে এবং ব্লকের টাইপ অনুযায়ী Tailwind CSS দিয়ে ডিজাইন করা কম্পোনেন্ট শো করবে।
* **Dynamic Classes:** ফিলামেন্ট থেকে আসা ব্যাকগ্রাউন্ড কালার, ইমেজ এবং অ্যানিমেশন স্টাইলগুলো Alpine.js এবং ইনলাইন CSS-এর মাধ্যমে ডাইনামিকভাবে রেন্ডার হবে।
* **Responsive Design:** প্রতিটি ব্লক সব স্ক্রিন সাইজের জন্য 100% রেসপন্সিভ হবে।

**Status:** Completed ✅ 

### Additional Fixes Implemented 
* **Storage Logic Strategy:** Instead of routing images through S3/R2 Cloudflare (which caused CORS issues on previews and domain mismatches), the ERP Page Builder storage disk logic in `ErpModuleForm` was permanently hardcoded to `public`. This ensures extreme stability for `cPanel` and avoids 524 Cloudflare timeout issues during Git GUI deployment.
* **Idempotent Migrations:** Fixed partial DDL migration failures by wrapping new column creations in `!Schema::hasColumn(...)` conditionals.
