# Laravel PayU
Simplify Integration PayU in Laravel

## Installation

Via [Composer][link-composer]

```bash
composer require korotkiewicz/payu
```

## Configuration

```bash
php artisan vendor:publish
```

Your `.env` file must end up looking like:


```ini
PAYU_PRODUCTION_MODE=false
PAYU_METCHANT_ID=""
PAYU_SIGNATURE_KEY=""
PAYU_CLIENT_ID=""
PAYU_CLIENT_SECRET=""
```