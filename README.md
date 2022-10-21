# imi-meter

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-meter.svg)](https://packagist.org/packages/imiphp/imi-meter)
[![Php Version](https://img.shields.io/badge/php-%3E=7.4-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.8.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![imi License](https://img.shields.io/badge/license-MulanPSL%202.0-brightgreen.svg)](https://github.com/imiphp/imi-meter/blob/master/LICENSE)

## 介绍

此项目是 imi 框架的服务监控指标组件抽象。

> 正在开发中，随时可能修改，请勿用于生产环境！

## 安装

`composer require imiphp/imi-meter:~2.1.0`

## 使用说明

### 配置

`@app.beans`：

```php
[
    'MeterRegistry' => [
        'driver'  => '驱动类名',
        // 驱动配置数组
        'options' => [
        ],
    ],
]
```

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

* [PHP](https://php.net/) >= 7.4
* [Composer](https://getcomposer.org/) >= 2.0
* [Swoole](https://www.swoole.com/) >= 4.8.0
* [imi](https://www.imiphp.com/) >= 2.1

## 版权信息

`imi-meter` 遵循 MulanPSL-2.0 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://cdn.jsdelivr.net/gh/imiphp/imi@2.1/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
