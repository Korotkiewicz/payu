# Laravel PayU
Simplify Integration PayU in Laravel

## Installation

Via [Composer][link-composer]

```bash
composer require korotkiewicz/payu
```

## Configuration

Add to your config/app.php

```php
'providers' => [
	Korotkiewicz\PayU\PayUServiceProvider::class
]
```

```bash
php artisan config:clear
php artisan vendor:publish
```

Your `.env` file must end up looking like:


```ini
PAYU_PRODUCTION_MODE=false
PAYU_METCHANT_ID=""
PAYU_SIGNATURE_KEY=""
PAYU_CLIENT_ID=""
PAYU_CLIENT_SECRET=""
PAYU_CONTINUE_URL=""
PAYU_NOTIFY_URL=""
PAYU_SHOP_NAME=""
```

Add you notify url to App\Http\Middleware\VerifyCsrfToken exclude list ($except):

```php
public function __construct(Application $app, Encrypter $encrypter)
{
    $this->except[] = config('payu.notify_url');

    parent::__construct($app, $encrypter);
}

```