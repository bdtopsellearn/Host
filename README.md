# 🚀 VortexCloud - VPS Reseller & Hosting Platform

A high-performance Cloud VPS Hosting & Bot Hosting Reseller Web Application with automated bKash/Nagad/Rocket/Binance payment gateway and real-time Firebase Firestore database synchronization.

---

## 📦 How to Export and Deploy to Render (রেন্ডারে লাইভ করার পূর্ণ গাইডলাইন)

### ধাপ ১: Google AI Studio থেকে কোড এক্সপোর্ট বা ডাউনলোড
1. উপরের ডানপাশে **Settings / Share / Export** মেনুতে ক্লিক করুন।
2. **"Export to GitHub"** সিলেক্ট করুন অথবা **"Download ZIP"** ফাইল ডাউনলোড করে আপনার কম্পিউটারে আনজিপ করুন।

---

### ধাপ ২: GitHub Repository তে কোড আপলোড করা
1. [GitHub](https://github.com/) এ গিয়ে **New Repository** তৈরি করুন (যেমন: `vortexcloud-hosting`).
2. আপনার ডাউনলোড করা ফোল্ডার থেকে গিটহাব রিপোজিটরিতে কোড পুশ করুন:
```bash
git init
git add .
git commit -m "Initial commit - VortexCloud VPS Platform"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPOSITORY.git
git push -u origin main
```

---

### ধাপ ৩: Render.com এ ডিপ্লয় করে ফ্রি লাইভ ইউআরএল (Live URL) পাওয়া
1. [Render Dashboard](https://dashboard.render.com/) এ লগইন করুন।
2. **"New +"** বাটনে ক্লিক করে **"Web Service"** সিলেক্ট করুন।
3. **"Connect a repository"** দিয়ে আপনার GitHub রিপোজিটরিটি সিলেক্ট করুন।
4. নিচের সেটিংসগুলো দিন:
   - **Name:** `vortexcloud-hosting` (অথবা আপনার পছন্দের নাম)
   - **Region:** Singapore / Frankfurt / Oregon
   - **Branch:** `main`
   - **Runtime:** `Node`
   - **Build Command:** `npm install`
   - **Start Command:** `npm start`
   - **Instance Type:** `Free`
5. **"Deploy Web Service"** বাটনে ক্লিক করুন!

---

### 🌐 ফলাফল (Live URL):
রেন্ডার কয়েক সেকেন্ডে স্বয়ংক্রিয়ভাবে প্যাকেজ ইনস্টল ও সার্ভার স্টার্ট করবে এবং আপনাকে একটি লাইভ পাবলিক ইউআরএল প্রদান করবে:
👉 `https://vortexcloud-hosting.onrender.com`

---

## 🛠️ Features Included
- ⚡ 1-Click Server Provisioning & Plan Selector
- 💳 bKash, Nagad, Rocket, and Binance Pay TrxID Submissions
- 🛡️ Super Admin Console (`/admin`) for order management
- 👤 Client Cloud Dashboard (`/customer`) for VPS instances and receipts
- 🔥 Real-Time Firebase Firestore persistence (`bot-host-website-9f118`)
