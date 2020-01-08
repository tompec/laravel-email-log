# A Laravel package to log emails sent to your users

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tompec/laravel-email-log.svg?style=flat-square)](https://packagist.org/packages/tompec/laravel-email-log)
[![Build Status](https://img.shields.io/travis/tompec/laravel-email-log/master.svg?style=flat-square)](https://travis-ci.org/tompec/laravel-email-log)
[![Quality Score](https://img.shields.io/scrutinizer/g/tompec/laravel-email-log.svg?style=flat-square)](https://scrutinizer-ci.com/g/tompec/laravel-email-log)
[![Coverage](https://img.shields.io/scrutinizer/coverage/g/tompec/laravel-email-log?style=flat-square)](https://scrutinizer-ci.com/g/tompec/laravel-email-log)
[![StyleCI](https://styleci.io/repos/231732412/shield)](https://styleci.io/repos/231732412)
[![Total Downloads](https://img.shields.io/packagist/dt/tompec/laravel-email-log.svg?style=flat-square)](https://packagist.org/packages/tompec/laravel-email-log)

`laravel-email-log` logs outgoing emails sent to your users.
If you use MailGun, you can also track deliveries, failures, opens and clicks.

## Installation

Install the package via composer:

```bash
composer require tompec/laravel-email-log
```

If you use Laravel 5.5+, the package will register itself, otherwise, add this to your `config/app.php`

``` php
'providers' => [
    Tompec\EmailLog\EmailLogServiceProvider::class,
],
```

Run the migration:

```bash
php artisan migrate
```

## Configuration (optional)

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Tompec\EmailLog\EmailLogServiceProvider"
```

Here's the content of the config file.
```php
return [
    /*
     * This is the name of the table that will be created by the migration.
     */
    'table_name' => 'email_log',

    /*
     * The model that will be attached to the email logs.
     */
    'recipient_model' => \App\User::class,

    /*
     * This is the name of the column that the `recipient_model` uses to store the email address.
     */
    'recipient_email_column' => 'email',

    /*
     * Whether or not you want to log emails that don't belong to any model
     */
    'log_unknown_recipients' => true,
];
```

If you want to get all the email logs for your a user, add this to your `App\User.php` file (or the model you chose as your `recipient_model`):

```php
public function email_logs()
{
    return $this->morphMany(\Tompec\EmailLog\EmailLog::class, 'recipient');
}
```

Then you can do `App\User::find(1)->email_logs` to retreive all the emails that this user has received.

## Webhooks

If you use [Mailgun](https://www.mailgun.com/), you can track these 4 events: `delivered `, `opened`, `clicked`, `permanent_fail`.

Add the following url [as a new webhook](https://documentation.mailgun.com/en/latest/user_manual.html#webhooks).

``` bash
https://www.yourdomain.com/email-log/mailgun
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email laravel-email-log@mail.tompec.com instead of using the issue tracker.

## Credits

- [Thomas Petracco](https://www.tompec.com/) ([GitHub](https://github.com/tompec))
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
