# Laravel Nova Warehouse Scan Tool

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Tests][ico-tests]][link-tests]
[![Total Downloads][ico-downloads]][link-downloads]

## Installation

You may install this package into a Laravel app that uses [Nova](https://nova.laravel.com) with composer:

```bash
composer require mvdnbrk/nova-warehouse-scan
```

Next up, you must register the tool with Nova. This is typically done in the `tools` method of the `NovaServiceProvider`.

```php
// in app/Providers/NovaServiceProvider.php

// ...

public function tools()
{
    return [
        // ...
        new \Just\Warehouse\Nova\Scan\Tool,
    ];
}
```

## Usage

Click on the "Scan" menu item in your Nova app to see the tool provided by this package.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mark van den Broek][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/mvdnbrk/nova-warehouse-scan.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/mvdnbrk/nova-warehouse-scan.svg?style=flat-square
[ico-tests]: https://github.com/mvdnbrk/nova-warehouse-scan/workflows/tests/badge.svg?branch=main

[link-packagist]: https://packagist.org/packages/mvdnbrk/nova-warehouse-scan
[link-downloads]: https://packagist.org/packages/mvdnbrk/nova-warehouse-scan
[link-tests]: https://github.com/mvdnbrk/nova-warehouse-scan/actions?query=workflow%3Atests
[link-author]: https://github.com/mvdnbrk
[link-contributors]: ../../contributors
