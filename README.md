![Project Logo](artwork/xendivel.jpg)

# Xendivel — A Laravel package for Xendit payment gateway

A Laravel package to easily integrate Xendit to your Laravel powered apps or websites. It supports credit, debit cards, and eWallet payments. This package also supports custom invoicing, queued invoice or refund email notifications, webhook integration and verifications, etc.

## Roadmap

Other features offered by Xendit listed below are not yet included in this package but will be added in the future.

- Subscription services
- Direct Bank Debits
- Disbursement APIs (for mass payments like payroll)
- PayLater
- QR Code payments

## Table of Contents

1. [Features](#features)
2. [Pre-requisites](#pre-requisites)
3. [Installation](#installation)
4. [Initial Setup](#installation)
    - [Setup Xendit API keys](#setup-xendit-api-keys)
    - [Xendit Webhook URL](#xendit-webhook-url)
    - [Mail Driver Setup](#mail-driver-setup)
5. [Usage](#usage)
6. [Tests](#tests)

## Features

- **Credit/Debit Cards** - Accepts major credit or debit cards.
- **eWallet Payments** - Accepts wide variety of eWallet payments depending on your region (GCash, ShopeePay, PayMaya, GrabPay, etc.)
- **Custom Invoicing** - It has built-in, highly customizable, and professional looking invoice templates.
- **Queued Email Notifications** - Supports markdown email templates and pushing email notifications to queue for background processing.
- **Webhooks** - It has built-in webhook event listeners from Xendit and webhook verification.

### Pre-requisites

- PHP 8.0 or higher
- Laravel 10 or higher

## Installation

**Clone the repository**

```bash
git@github.com:glennraya/jsonfakery.git
```

**Install composer dependencies**

```bash
composer install
```

**Install NPM dependencies**

```bash
npm install
```

**Generate App Key**

```bash
php artisan key:generate
```

**Configure Mailer**
```ini
MAIL_MAILER=smtp
MAIL_HOST=your-mailer-host
MAIL_PORT=your-mailer-port
MAIL_USERNAME=your-mailer-username
MAIL_PASSWORD=your-mailer-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="fromaddress@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Create storage symbolic link**

```bash
php artisan storage:link
```

**Run the migrations**

```bash
php artisan migrate
```

**Then finally, run the database seeders**

```bash
php artisan db:seed
```
