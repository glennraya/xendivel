![Project Logo](artwork/xendivel.jpg)

# Xendivel — A Laravel package for Xendit payment gateway

A Laravel package to easily integrate Xendit to your Laravel powered apps or websites. It supports credit, debit cards, and eWallet payments. This package also supports custom invoicing, queued invoice or refund email notifications, webhook integration and verifications, etc.

## Roadmap

Other features offered by Xendit listed below are not yet included in this package but will be added in the future.

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

- **Credit/Debit Cards** - Accepts major credit or debit cards.
- **eWallet Payments** - Accepts wide variety of eWallet payments depending on your region (GCash, ShopeePay, PayMaya, GrabPay, etc.)
- **Custom Invoicing** - Has built-in, highly customizable, and professional looking invoice templates.
- **Queued Email Notifications** - Supports markdown email templates and option to push email notifications to queue for background processing.
- **Webhooks** - Has built-in webhook event listeners from Xendit and webhook verification.

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

Before you can use Xendivel, you should ensure that you have a Xendit account and API keys are properly setup. Your Xendit account doesn't need to be activated for production to test Xendivel's features. The test mode will be automatically enabled once you had signed up for a Xendit account. You can acquire your API keys from the following URLs:

- Secret Key:
- Public Key:
- Webhook Verification Token:

After you acquired all these keys, please make sure you include them to your Laravel's <code>.env</code> file:

```ini
XENDIT_SECRET_KEY=your-secret-key
XENDIT_PUBLIC_KEY=your-public-key
XENDIT_WEBHOOK_VERIFICATION_TOKEN=your-webhook-verification-token
```

**Configure Laravel Mail**

Xendivel has the ability to send invoices via email attachments to your customers. If you plan to use this feature, you should make sure that you have your Laravel Mail configuration properly setup before Xendivel can send invoice or refund email notifications.

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

Xendivel supports sending email process to queue for background processing. If you plan to utilize queued emails for invoices or refund notifications, please make sure you have configured Laravel's jobs/queues.

https://laravel.com/docs/10.x/queues#main-content

Then, make sure you have a queue worker running:

```bash
php artisan queue:work
```

**Configuration File**

Publish Xendivel's configuration file to your app's config directory:

```bash
php artisan vendor:publish --tag=xendivel
```

## Usage
