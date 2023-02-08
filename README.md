Magento2- Easytransac_Gateway
============================

## Important functions for Magento 2

EasyTransac payment gateway Magento2 extension. For Magento 2.4.x

## Installation

### API Library

This module is using the EasyTransac APIs Library for PHP for all (API) connections to EasyTransac.
<a href="https://github.com/easytransac/easytransac-sdk-php">This library can be found here</a>

Or

Download using Command:

`composer require easytransac/easytransac-sdk-php`

Download the extension form <a href="https://github.com/easytransac/EasyTransac-Magento">github</a>.

1. Unzip the extension and move to app/code directory of magento root
2. Enter following commands to enable module:

* `php bin/magento module:enable Easytransac_Gateway`
* `php bin/magento setup:upgrade`
* `php bin/magento setup:di:compile`
* `php bin/magento cache:clean`

## Verified payment methods

1. **Card payment method (3DS)**
    1. Pay using card without saved card
    2. Pay with card with save card (Pay with Oneclick while pay next time)
2. **One click payment methods**
    1. For the 1st time customer will save cards while checkout it will use 2nd time and no need to fill card detials
3. **Multipayments or recurring payment methods**
    1. Customer pay with serveral installments while paying with save card details
    2. Customer pay with serveral installments while paying without save card details

## Configure the plugin in magento admin panel.

### Set Currency

1. The plugin should work in **Euro** Currency.
2. On the Admin Panel, go Stores > Settings > Configuration.
3. On the left panel, under General , select Currency Setup.
4. Open the Currency Options section, Choose the primary currency for the Base Currency in the online transaction.
5. Before you begin, make sure that you have set up your Easytransac account and get an API key from the account.

### Configure payment methods

* Log in to your Magento admin panel.
* In the left navigation bar, go to Stores > Configuration.
* In the menu, go to Sales > Payment Methods > Choose **EasyTransac**.
* Select Required Settings and fill out the following fields:

##### General settings

| **Field**                                | **Description**                                                                                                   |
|------------------------------------------|-------------------------------------------------------------------------------------------------------------------|
| API Key                                  | The API key you generated in the Easytransac account.                                                             |
| Debug                                    | Select Yes to enable debug logging on the Magento server.                                                         |
| Easytransac Icon at Checkout             | Select yes to display the esaytransac logo at the checkout page                                                   |
| Payment Applicable Countries             | Countries or regions where you can make payments for the selected country                                         |
| **CreditCard configurations**            |
| Title                                    | Payment method title, visible at checkout page                                                                    |
| Oneclick for Card Payments               | Select Yes to visible save cards option at checkout.                                                              |
| Min Order total                          | Put an amount, it will display if the amount is greeted then this amount otherwise payment method is not visible. |
| **Multipayments configurations**         |                                                                                                                   |
| Title                                    | Payment method title, visible at checkout page                                                                    |
| Available multipayment installments      | Customer choose installment while paying the amount at checkout page(2 to 12 installments)                        |
| OneClick for Multi Installments Payments | Display pre saved cards while paying 2nd time.                                                                    |
| Min Order total                          | Put an amount, it will display if the amount is greeted then this amount otherwise payment method is not visible. |

## Database Modification

- Extend `sales_order` table for save installments amount
    - easytransac_installment_amount : To save installments amounts
- Extend `sales_order_grid` table for display installments amount
    - easytransac_installment_amount : To save installments amounts

## Logger

Logs are stored in a single file on Magento server: **/var/log/easyTransac.log**

## Magento Version Support

We follow Magento's version 2.4.x
