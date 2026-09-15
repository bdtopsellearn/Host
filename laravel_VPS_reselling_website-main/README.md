# Laravel VPS Reselling Platform

A modern, production-ready VPS Reselling storefront and provisioning system built with Laravel 13, Filament V3, and Tailwind CSS v4.

## 🚀 Tech Stack

### Core Frameworks & Languages
- **Backend:** PHP 8.3 & Laravel 13.x
- **Frontend:** Javascript (ES Modules) via Vite

### Backend Packages (Composer)
- **[Filament V3](https://filamentphp.com/):** The core engine used to build the responsive Admin and Customer dashboards.
- **[Filament Shield](https://github.com/bezhanSalleh/filament-shield) & [Spatie Permission](https://spatie.be/docs/laravel-permission):** Provides robust role-based access control and security policies across the application.
- **[Stripe PHP](https://github.com/stripe/stripe-php):** The official SDK for secure credit card payment processing.
- **[Guzzle](https://docs.guzzlephp.org/):** Handles external HTTP requests (e.g., communicating with the Contabo API).

### Frontend Packages (NPM)
- **[Tailwind CSS v4](https://tailwindcss.com/):** The powerful utility-first CSS framework used for the storefront, pricing, checkout, and Filament themes.
- **[Tailwind Forms](https://github.com/tailwindlabs/tailwindcss-forms):** Provides normalized styling for HTML form elements.
- **[Vite](https://vitejs.dev/):** The modern lightning-fast build tool and development server.

---

## 🔒 Access & Credentials

The application uses two distinct panels separated by different guards for security.

### 1. Admin Panel (For Staff/Administrators)
- **URL / Route:** `/admin`
- **Default Email:** `admin@example.com` (or the email you created via `php artisan shield:generate --all`)
- **Password:** `password`

### 2. Customer Panel (For Clients)
- **URL / Route:** `/customer`
- **Default Email:** `test@example.com` (from the screenshots/tests)
- **Password:** `password`

*(Note: If you encounter login issues, you can always create a new user via the registration page or Tinker!)*

---

## ⚙️ Features

- **Storefront & Checkout:** Beautiful Tailwind CSS v4 landing pages and Stripe-integrated checkout flow. Includes support for fallback Manual Payments (Crypto/Bank Transfer).
- **Dual Dashboard Architecture:** Isolated Filament panels for administrators (`/admin`) and customers (`/customer`).
- **Provisioning Engine:** An extensible provisioning service layer. Contains a `MockProvisioningService` for local testing and a `ContaboProvisioningService` for interacting with the Contabo API.
- **Background Jobs:** Provisioning requests are dispatched to the queue (`ProvisioningJob`) so they execute asynchronously without blocking the user interface.
- **Invoicing System:** Invoices are automatically generated upon successful order placement. Stripe orders mark invoices as paid, while Manual orders remain unpaid for manual verification.

---

## 🛠️ Local Development & Setup

If you need to re-initialize the project locally, follow these steps:

1. **Install PHP Dependencies**
   ```bash
   composer install
   ```

2. **Install Node Dependencies & Build Assets**
   ```bash
   npm install
   npm run build
   ```

3. **Database Setup**
   ```bash
   php artisan migrate
   # Generate super admin if needed
   php artisan shield:generate --all
   ```

4. **Environment Configuration (`.env`)**
   Make sure to configure the following keys in your `.env`:
   - `STRIPE_KEY` and `STRIPE_SECRET` for payment processing.
   - `PROVISIONING_MODE=mock` (or `contabo`) depending on if you want to test locally without creating real VPS instances.
   - `CONTABO_API_*` keys if using the real Contabo API.

5. **Start Development Servers**
   To work on the project locally, run both of these commands in separate terminal windows:
   ```bash
   php artisan serve
   npm run dev
   ```
