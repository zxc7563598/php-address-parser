# hejunjie/address-parser

<div align="center">
  <a href="./README.md">English</a>｜<a href="./README.zh-CN.md">简体中文</a>
  <hr width="50%"/>
</div>

An intelligent address parser that extracts name, phone number, ID number, region, and detailed address from unstructured text—perfect for e-commerce, logistics, and CRM systems.

**This project has been parsed by Zread. If you need a quick overview of the project, you can click here to view it：[Understand this project](https://zread.ai/zxc7563598/php-address-parser)**

---

A simple and practical PHP address parsing tool that can extract **name, phone number, ID card number, postal code, and full province-city-district address** from unstructured strings.

> 🚀 Ideal for use cases like shipping addresses and user information entry, automatically identifying and structuring key data.

If you don’t want to deploy it yourself and just want to use it directly, you can 👉 [Click here to use it](https://hejunjie.life/composer/address-parser)

Batch queries are supported.

---

## ✨ Features

- Auto Recognition: Supports extraction of name, phone number, ID card, and postal code
- Address Parsing: Intelligently matches administrative regions based on province/city/district data
- Structured Output: Returns data in a unified structure, easy for frontend-backend integration
- Zero Dependencies: Written in pure PHP, no additional extensions required
- PHP 8+ Supported

---

## 📦 Installation

Install via Composer:

```bash
composer require hejunjie/address-parser
```

## 🧠 Usage Example

```php
use Hejunjie\AddressParser\AddressParser;

$raw = '张三，13512345678,410123199001011234 重庆攀枝花市东区机场路88号 邮编100000';

$parsed = AddressParser::parse($raw);

print_r($parsed);

```

Output Result

```php
[
    'name' => '张三',
    'mobile' => '13512345678',
    'idn' => '410123199001011234',
    'postcode' => '100000',
    'province' => '四川省',
    'city' => '攀枝花市',
    'region' => '东区',
    'street' => '机场路88号'
]
```

## 🧩 Response Field Description

| Parameter | Description |
|:-------|:-----|
| name | User's full name |
| mobile | User's mobile number |
| idn | User's national ID card number |
| postcode | Zip/postal code |
| province | Name of the province |
| city | Name of the city |
| region | Name of the district or county |
| street | Remaining address after removing province, city, and district |

## 🧰 Purpose & Background

In real-world scenarios, user-submitted addresses are often unstructured. For example:

> 张三 13512345678 北京市朝阳区建国路88号 邮编100000

Manually parsing such data is not only inefficient but also error-prone. The `hejunjie/address-parser` is designed specifically to solve this problem of **unstructured address parsing**, and is suitable for a wide range of use cases:

- 🛒 Processing order addresses in e-commerce systems
- 📦 Address recognition in logistics and delivery services
- 🧾 User profile completion in admin panels
- 📱 Address input validation in mini-programs or mobile apps

Whether for personal projects or enterprise systems, it significantly boosts automation and accuracy in address handling.

If you have any questions or suggestions, feel free to submit an issue or PR — I’ll do my best to respond.

## 🙏 Acknowledgements

The inspiration for this package originally came from a friend who mentioned the idea in an issue on one of my other repositories, even kindly sharing another friend's implementation: [pupuk/address](https://github.com/pupuk/address). I found the concept very interesting, and thanks to that inspiration, I created `hejunjie/address-parser`.

Thanks to him for the idea — I hope this little tool helps more people 🙌

## 🔧 Additional Toolkits (Can be used independently or installed together)

This project was originally extracted from [hejunjie/tools](https://github.com/zxc7563598/php-tools).
To install all features in one go, feel free to use the all-in-one package:

```bash
composer require hejunjie/tools
```

Alternatively, feel free to install only the modules you need：

[hejunjie/utils](https://github.com/zxc7563598/php-utils) - A lightweight and practical PHP utility library that offers a collection of commonly used helper functions for files, strings, arrays, and HTTP requests—designed to streamline development and support everyday PHP projects.

[hejunjie/cache](https://github.com/zxc7563598/php-cache) - A layered caching system built with the decorator pattern. Supports combining memory, file, local, and remote caches to improve hit rates and simplify cache logic.

[hejunjie/china-division](https://github.com/zxc7563598/php-china-division) - Regularly updated dataset of China's administrative divisions with ID-card address parsing. Distributed via Composer and versioned for use in forms, validation, and address-related features

[hejunjie/error-log](https://github.com/zxc7563598/php-error-log) - An error logging component using the Chain of Responsibility pattern. Supports multiple output channels like local files, remote APIs, and console logs—ideal for flexible and scalable logging strategies.

[hejunjie/mobile-locator](https://github.com/zxc7563598/php-mobile-locator) - A mobile number lookup library based on Chinese carrier rules. Identifies carriers and regions, suitable for registration checks, user profiling, and data archiving.

[hejunjie/address-parser](https://github.com/zxc7563598/php-address-parser) - An intelligent address parser that extracts name, phone number, ID number, region, and detailed address from unstructured text—perfect for e-commerce, logistics, and CRM systems.

[hejunjie/url-signer](https://github.com/zxc7563598/php-url-signer) - A PHP library for generating URLs with encryption and signature protection—useful for secure resource access and tamper-proof links.

[hejunjie/google-authenticator](https://github.com/zxc7563598/php-google-authenticator) - A PHP library for generating and verifying Time-Based One-Time Passwords (TOTP). Compatible with Google Authenticator and similar apps, with features like secret generation, QR code creation, and OTP verification.

[hejunjie/simple-rule-engine](https://github.com/zxc7563598/php-simple-rule-engine) - A lightweight and flexible PHP rule engine supporting complex conditions and dynamic rule execution—ideal for business logic evaluation and data validation.

👀 All packages follow the principles of being lightweight and practical — designed to save you time and effort. They can be used individually or combined flexibly. Feel free to ⭐ star the project or open an issue anytime!

---

This library will continue to be updated with more practical features. Suggestions and feedback are always welcome — I’ll prioritize new functionality based on community input to help improve development efficiency together.
