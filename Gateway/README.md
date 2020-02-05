magento2-Easytransac_Gateway
============================

EasyTransac payment gateway Magento2 extension. For Magento 2.3.4.


Install
=======

1. Copy module to Magento2 app/code folder.

2. Enter following commands to enable module:

    ```bash
    php bin/magento module:enable Easytransac_Gateway --clear-static-content
    php bin/magento setup:upgrade
    ```
3. Enable and configure Easytransac in Magento Admin under Stores/Configuration/Payment Methods/EasyTransac

Notes
===========

* EasyTransac works with EUR only.

