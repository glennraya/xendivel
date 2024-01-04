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
    - [Jobs/Queues (Optional)](#job-queues)
    - [Configuration File](#configuration-file)
5. [Checkout Templates](#checkout-templates)
6. [Usage](#usage)
    - [Card Payments](#card-payments)
        - [Card Details Tokenization](#card-details-tokenization)
        - [Charge Card](#charge-card)
        - [Get Card Charge](#get-card-charge)
    - [eWallet Payments](#ewallet-payments)
    - [PDF Invoicing](#invoicing)
    - [Refunds](#refunds)
    - [Webhook](#webhook)
7. [Tests](#tests)

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

Xendivel utilizes Composer's package auto-discovery. All you need to do is to install Xendivel via composer and it will automatically register itself.

```bash
composer install glennraya/xendivel
```

## Initial Setup

**Xendit API Keys**

Prior to using Xendivel, it's essential to have a Xendit account with properly configured API keys. Activation of your Xendit account for production is not necessary to test Xendivel's features. Test mode will be automatically enabled upon signing up for a Xendit account. Obtain your API keys from the following URLs:

- Secret Key/Public Key: https://dashboard.xendit.co/settings/developers#api-keys
- Webhook Verification Token: https://dashboard.xendit.co/settings/developers#webhooks

Generate `Money-In` `secret key` with `read` and `write` permissions from your dashboard API keys section.

After you acquired all these keys, please make sure you include them to your Laravel's `.env` file:

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

**Jobs/Queues (Optional, but highly recommended)**

Xendivel facilitates the queuing of email processes for background execution. If you intend to employ queued emails for tasks such as invoicing or refund notifications, ensure that you have properly configured Laravel's jobs/queues.

[Laravel Queues](https://laravel.com/docs/10.x/queues#main-content)

Then, make sure you have a queue worker running:

```bash
php artisan queue:work
```

Once you have successfully configured Laravel's queues, Xendivel is now capable of dispatching invoice or refund emails to the queue for background execution.

**Configuration File**

Publish Xendivel's assets and configuration file to your Laravel application's config directory using the following command:

```bash
php artisan vendor:publish --tag=xendivel-config
```

Executing this command will publish Xendivel's config file to your Laravel app's config directory.

## Checkout Templates

![Checkout Template](docs/image_assets/checkout-template.png)

Xendivel ships with a complete, fully working checkout template for card and eWallet payments. The template include various variants such as ReactJS component, ReactJS+TypeScript component, and a regular Blade template and VanillaJS.

You can choose between the currently available template variants, you can even create your own.

**ReactJS + TypeScript component `.tsx`:**

```bash
php artisan vendor:publish --tag=xendivel-checkout-react-typescript
```

This will be published under `/resources/js/vendor/xendivel` directory.

**ReactJS component `.jsx`:**

```bash
php artisan vendor:publish --tag=xendivel-checkout-react
```

This will be published under `/resources/js/vendor/xendivel` directory.

**Blade Template**

We also have a regular Blade template with VanillaJS for the checkout example. Xendivel ships with a route where you can preview this template: `/xendivel/checkout/blade`. So you could do something like, `https://your-domain.test/xendivel/checkout/blade`.

> NOTE: When you run the command `php artisan vendor:publish --tag=xendivel-views` the blade template will be on your `/resources/views/vendor/xendivel` directory.

These templates demonstrate card tokenization, credit/debit card, and eWallet payments. They serve to guide your payment collection process for implementation in your front-end stack. Alternatively, use them as fully functional standalone templates if you wish.

## Usage
