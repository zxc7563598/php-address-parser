# hejunjie/address-parser

<div align="center">
  <a href="./README.md">English</a>｜<a href="./README.zh-CN.md">简体中文</a>
  <hr width="50%"/>
</div>

收货地址智能解析工具，支持从非结构化文本中提取姓名、手机号、身份证号、省市区、详细地址等字段，适用于电商、物流、CRM 等系统。

**本项目已经经由 Zread 解析完成，如果需要快速了解项目，可以点击此处进行查看：[了解本项目](https://zread.ai/zxc7563598/php-address-parser)**

---

一个简单实用的 PHP 地址解析工具，可以从混杂的字符串中提取出 **姓名、手机号、身份证、邮编、以及完整的省市区地址信息**。

> 🚀 适用于快递地址、用户信息录入等场景，自动识别结构化信息。

如果你不想要部署，只是想要进行使用，可以 👉 [点击此处进行使用](https://tools.hejunjie.life/#/external/address-parser)

支持批量查询

---

## ✨ 特性

- 自动识别：支持姓名、手机号、身份证、邮编提取
- 地址解析：基于省市区行政区划数据，智能匹配行政区域
- 结构化返回：统一结构输出，便于前后端对接
- 零依赖：纯 PHP 编写，无需额外扩展
- 支持 PHP 8+

---

## 📦 安装

使用 Composer 安装：

```bash
composer require hejunjie/address-parser
```

## 🧠 使用示例

```php
use Hejunjie\AddressParser\AddressParser;

$raw = '张三，13512345678,410123199001011234 重庆攀枝花市东区机场路88号 邮编100000';

$parsed = AddressParser::parse($raw);

print_r($parsed);

```

输出结果

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

## 🧩 返回字段说明

| 字段名   | 说明                           |
| :------- | :----------------------------- |
| name     | 姓名                           |
| mobile   | 手机号                         |
| idn      | 身份证号                       |
| postcode | 邮政编码                       |
| province | 省份名称                       |
| city     | 城市名称                       |
| region   | 区/县名称                      |
| street   | 详细地址（去除省市区后的部分） |

## 🧰 用途 & 背景

在实际业务中，用户填写的地址往往是非结构化的，例如：

> 张三 13512345678 北京市朝阳区建国路 88 号 邮编 100000

将这些信息手动拆分不仅低效，而且容易出错。`hejunjie/address-parser` 就是为了解决这种 **非结构化地址的自动解析** 而设计的，广泛适用于：

- 🛒 电商系统中的订单地址处理
- 📦 快递物流系统地址识别
- 🧾 后台管理系统用户信息补全
- 📱 小程序/APP 用户地址录入校验

无论是个人项目还是企业系统，它都能快速提升地址处理的自动化与准确率。

有啥问题或者建议都欢迎提 issue 或 PR，我会尽量回复。

## 🙏 致谢

这个包最初的灵感，来自一位朋友在我另一个仓库的 issues 里提到的想法，还贴心地分享了其他朋友的实现：[pupuk/address](https://github.com/pupuk/address)。当时就觉得这个方向挺有意思，也正是因为他的启发，我才动手做了 `hejunjie/address-parser`。

感谢他的思路分享，也希望这个小工具能帮到更多人 🙌

## 🔧 更多工具包（可独立使用，也可统一安装）

本项目最初是从 [hejunjie/tools](https://github.com/zxc7563598/php-tools) 拆分而来，如果你想一次性安装所有功能组件，也可以使用统一包：

```bash
composer require hejunjie/tools
```

当然你也可以按需选择安装以下功能模块：

[hejunjie/utils](https://github.com/zxc7563598/php-utils) - 一个零碎但实用的 PHP 工具函数集合库。包含文件、字符串、数组、网络请求等常用函数的工具类集合，提升开发效率，适用于日常 PHP 项目辅助功能。

[hejunjie/cache](https://github.com/zxc7563598/php-cache) - 基于装饰器模式实现的多层缓存系统，支持内存、文件、本地与远程缓存组合，提升缓存命中率，简化缓存管理逻辑。

[hejunjie/china-division](https://github.com/zxc7563598/php-china-division) - 定期更新，全国最新省市区划分数据，身份证号码解析地址，支持 Composer 安装与版本控制，适用于表单选项、数据校验、地址解析等场景。

[hejunjie/error-log](https://github.com/zxc7563598/php-error-log) - 基于责任链模式的错误日志处理组件，支持多通道日志处理（如本地文件、远程 API、控制台输出），适用于复杂日志策略场景。

[hejunjie/mobile-locator](https://github.com/zxc7563598/php-mobile-locator) - 基于国内号段规则的手机号码归属地查询库，支持运营商识别与地区定位，适用于注册验证、用户画像、数据归档等场景。

[hejunjie/address-parser](https://github.com/zxc7563598/php-address-parser) - 收货地址智能解析工具，支持从非结构化文本中提取姓名、手机号、身份证号、省市区、详细地址等字段，适用于电商、物流、CRM 等系统。

[hejunjie/url-signer](https://github.com/zxc7563598/php-url-signer) - 用于生成带签名和加密保护的 URL 链接的 PHP 工具包，适用于需要保护资源访问的场景

[hejunjie/google-authenticator](https://github.com/zxc7563598/php-google-authenticator) - 一个用于生成和验证时间基础一次性密码（TOTP）的 PHP 包，支持 Google Authenticator 及类似应用。功能包括密钥生成、二维码创建和 OTP 验证。

[hejunjie/simple-rule-engine](https://github.com/zxc7563598/php-simple-rule-engine) - 一个轻量、易用的 PHP 规则引擎，支持多条件组合、动态规则执行，适合业务规则判断、数据校验等场景。

👀 所有包都遵循「轻量实用、解放双手」的原则，能单独用，也能组合用，自由度高，欢迎 star 🌟 或提 issue。

---

该库后续将持续更新，添加更多实用功能。欢迎大家提供建议和反馈，我会根据大家的意见实现新的功能，共同提升开发效率。
