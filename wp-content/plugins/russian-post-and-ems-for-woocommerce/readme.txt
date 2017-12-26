=== Russian Post and EMS for WooCommerce ===
Contributors: artemkomarov
Tags: woocommerce, woocommerce shipping, ecommerce, shipping
Requires at least: 4.4
Tested up to: 4.8.1
Stable tag: 0.9
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

The plugin allows you to automatically calculate shipping costs of "Russian Post" or "EMS"

== Description ==

The plugin allows you to automatically calculate shipping costs of "Russian Post" or "EMS" using [postcalc.ru](http://www.postcalc.ru).

The plugin can calculate:

* Calculate shipping costs based on weight and cost of cart
* Display time of delivery for shipments
* Prices for different types of delivery
* Save and send email with tracking number to customer

It is also possible to specify an additional fixed cost.

= Attention! = 

To calculate the COD there is a separate [plugin](https://ru.wordpress.org/plugins/cash-on-delivery-of-russian-post-or-ems-for-woocommerce/) which adds the appropriate method of payment in WooCommerce

== Installation ==

= From your WordPress dashboard =

Visit 'Plugins > Add New'
Search for 'Russian Post and EMS'
Activate Cash on Delivery of Russian Post from your Plugins page.

Then create new Shipping Zone and add Russian Post as a method.

== Frequently Asked Questions ==

= What kind of project - postcalc.ru =

It is a special non-profit project of one person from Moscow. This project is not related to With the Russian Post or EMS either technically or as something else.

= How postcalc.ru accurate counts? =

As accurately as possible but there may be minor errors.

= Paid access =

Initially, the service is free, but if the number of requests from your Internet project regularly exceed 500 requests per day - you need to switch to [paid access](http://www.postcalc.ru/faq.html#commercial).

= Well, I still have questions... =

here is [more](http://www.postcalc.ru/faq.html)


== Screenshots ==

1. Основные настройки

== Changelog ==

= 0.9 =

Исправлена ошибка с id методами
Исправлена ошибка при отсутствии даты доставки (спасибо @evanre)
Исправлен расчет доставки для цифровых (виртуальных) товаров

Добавлена валидация индекса для России
Добавлена валидация веса отправления
Добавлена опция ввода максимальной фиксированной суммы объявленной стоимости
Добавлена возможность отключить метод доставки если вес превышает допустимый для отправления
Добавлена опция показывать метод только если сумма заказа выше указанной

= 0.8 =

Исправлена ошибка file_get_contents для тестового сервера postcalc

= 0.7 =

Добавлен функционал для отправки трек-номеров Почты России и EMS. При отправке номер отсылается на почту клиента с соответствующими комментариями.
Добавлен статус заказа - Доставляется.

= 0.6 =

Добавлены опции для международной доставки. Добавлена опция простая посылка.

= 0.5 =

Добавленно склонение для сроков доставки и пофиксен стиль копирайта

= 0.4 =

Устранена проблема с символом рубля

= 0.3 =

Добавлена возможность указать дополнительный вес и стоимость упаковки.

= 0.2 =

Если поле индекс отсутствует то берется введенный город получателя за конечный пункт.

= 0.1 =

Первая версия.

