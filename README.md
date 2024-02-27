# Chapa Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mdmasudsikdar/chapa.svg?style=flat-square)](https://packagist.org/packages/mdmasudsikdar71/chapa-payment-gateway-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/mdmasudsikdar/chapa/Tests?label=tests)](https://github.com/mdmasudsikdar71/chapa-payment-gateway-laravel/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/mdmasudsikdar/chapa.svg?style=flat-square)](https://packagist.org/packages/mdmasudsikdar71/chapa-payment-gateway-laravel)

Chapa is a Laravel package that provides a client for interacting with the Chapa API. This package simplifies the integration process for Chapa payments in your Laravel application.

## Features

- **Transaction Initialization:** Easily initialize Chapa transactions with a simple and clean API.
- **Transaction Verification:** Verify Chapa transactions to ensure their validity.
- **Customization:** Customize and configure Chapa transactions according to your needs.
- **Clear Documentation:** Well-documented codebase and API for easy integration and customization.

**more coming soon...**

## Installation

You can install the package via composer:

```bash
composer require mdmasudsikdar71/chapa-payment-gateway-laravel
```

## Configuration

After installing the package, you need to publish the configuration file:

```bash
php artisan vendor:publish --tag="chapa-config"
```

Then, update the `config/chapa.php` configuration file with your Chapa secret key.

## Usage

### Initialize a Chapa Transaction

```php
use MDMasudSikdar\Chapa\ChapaClient;

$chapaClient = new ChapaClient();

// Prepare the request body
$requestBody = [
    'amount' => 1000,
    'currency' => 'USD',
    'email' => 'user@example.com',
    // ... other required fields
];

// Initialize the transaction
$response = $chapaClient->transactionInitialize($requestBody);

// Handle the response
// $response contains the decoded response data
```

### Verify a Chapa Transaction

```php
use MDMasudSikdar\Chapa\ChapaClient;

$chapaClient = new ChapaClient();

// Transaction reference to verify
$txRef = 'your_transaction_reference';

// Verify the transaction
$response = $chapaClient->transactionVerify($txRef);

// Handle the response
// $response contains the decoded response data
```

## Security

If you discover any security-related issues, please email masudsikdar85@gmail.com.com instead of using the issue tracker.

## License

The Chapa Laravel Package is open-sourced software licensed under the [MIT license](LICENSE.md).
