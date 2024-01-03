![Project Logo](artwork/xendivel.jpg)

# Xendivel — A Laravel package for Xendit payment gateway

A Laravel package designed for seamless integration of Xendit into your Laravel-powered applications or websites. It facilitates payments through credit cards, debit cards, and eWallets. Additionally, the package provides support for custom invoicing, queued invoice or refund email notifications, webhook integration, verifications, and more.

## Roadmap

The following features offered by Xendit are not currently included in this package but will be incorporated in future updates.

- Promotions (coupon/discount codes)
- Subscription services
- Direct Bank Debits
- Disbursement APIs (for mass payments like payroll)
- PayLater
- QR Code payments

## Table of Contents

1. [Features](#features)
2. [Pre-requisites](#pre-requisites)
3. [Installation](#installation)
4. [Initial Setup](#initial-setup)
    - [Setup Xendit API keys](#setup-xendit-api-keys)
    - [Xendit Webhook URL](#xendit-webhook-url)
    - [Mail Driver Setup](#mail-driver-setup)
    - [Jobs/Queues](#job-queues)
    - [Configuration File](#configuration)
5. [Usage](#usage)
    - [Card Payments](#card-payments)
        - [Card Details Tokenization](#card-details-tokenization)
        - [Charge Card](#charge-card)
        - [Get Card Charge](#get-card-charge)
    - [eWallet Payments](#ewallet-payments)
    - [PDF Invoicing](#invoicing)
    - [Refunds](#refunds)
    - [Webhook](#webhook)
6. [Tests](#tests)

## Features

- **Credit/Debit Cards** - Easily process payments through major credit or debit cards.
- **eWallet Payments** - Accepts a diverse range of eWallet payments based on your region (GCash, ShopeePay, PayMaya, GrabPay, etc.).
- **Custom Invoicing** - Provides built-in, highly customizable, and professional-looking invoice templates.
- **Queued Email Notifications** - Enables the use of markdown email templates and the option to schedule email notifications for background processing.
- **Webhooks** - Comes with built-in webhook event listeners from Xendit and ensures secure webhook verification.

### Pre-requisites

- PHP 8.0 or higher
- Laravel 10 or higher

## Installation

**Composer**

```bash
composer install glennraya/xendivel
```

## Initial Setup

**Xendit API Keys**

Prior to using Xendivel, it's essential to have a Xendit account with properly configured API keys. Activation of your Xendit account for production is not necessary to test Xendivel's features. Test mode will be automatically enabled upon signing up for a Xendit account. Obtain your API keys from the following URLs:

- Secret Key/Public Key: https://dashboard.xendit.co/settings/developers#api-keys
- Webhook Verification Token: https://dashboard.xendit.co/settings/developers#webhooks

Generate <code>Money-In</code> <code>secret key</code> with <code>read</code> and <code>write</code> permissions from your dashboard API keys section.

After you acquired all these keys, please make sure you include them to your Laravel's <code>.env</code> file:

```ini
XENDIT_SECRET_KEY=your-secret-key
XENDIT_PUBLIC_KEY=your-public-key
XENDIT_WEBHOOK_VERIFICATION_TOKEN=your-webhook-verification-token
```

**Configure Laravel Mail**

Xendivel is equipped to send invoices to your customers as email attachments. To leverage this feature, ensure that your [Laravel Mail](https://laravel.com/docs/10.x/mail#main-content) configuration is set up correctly before Xendivel can dispatch invoice or refund email notifications.

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

**Jobs/Queues**

Xendivel facilitates the queuing of email processes for background execution. If you intend to employ queued emails for tasks such as invoicing or refund notifications, ensure that you have properly configured Laravel's jobs/queues.

[Laravel Queues](https://laravel.com/docs/10.x/queues#main-content)

Then, make sure you have a queue worker running:

```bash
php artisan queue:work
```

**Configuration File**

Publish Xendivel's configuration file to your Laravel application's config directory using the following command:

```bash
php artisan vendor:publish --tag=xendivel
```

## Usage
