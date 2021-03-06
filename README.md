Fat Zebra Magento 2 Module
======================

Overview
--------
The Fat Zebra Magento 2 module provides a simple integration method for Magento 2.x with the Fat Zebra gateway services. This module includes support for the following functionality:

* IFRAME card details capture for de-scoping of PCI requirements
* Card tokenization and one-click saved card payment using the Magento Vault
* Refunds of orders through Magento
* Fraud screening of transactions

Fraud Screening of transactions is performed through the payment gateway inline with the payment request and has four possible outcomes:

* Accept – the fraud screening considers the transaction legitimate and the transaction is attempted with the bank.
* Challenge – the fraud screening considers the transaction to be moderate risk and the merchant should review the transaction and the fraud messages to determine whether to cancel/refund the order or fulfil it.
* Deny – the fraud screening considers the transaction to be high risk (or a predefined DENY rule has been triggered) and the order has been prevented form processing.


Installation
------------
Installation of the module can be performed using composer, or manually.

Installation using Composer
---------------------------

1.	Under the Magento root folder run the following command to setup the repository:
```
composer config repositories.fatzebra git https://github.com/fatzebra/magento2-fatzebra.git
```
2.	Then run the following command to add the module:
```
composer require fatzebra/magento2-fatzebra:dev-master
```
3.	Following this the dependencies will be fetched or updated where required – this may take a few minutes.
4.	Once all dependencies have installed the module needs to be enabled using the following commands:
```
php bin/magento module:enable FatZebra_Gateway --clear-static-content && php bin/magento setup:upgrade
```
5.	Once the setup:upgrade command completes the module will be available within the Store Admin to configure.


Manual Installation
-------------------

1.	Download the latest archive of the module from https://github.com/fatzebra/magento2-fatzebra/archive/master.zip
2.	Copy the archive to the server and extract – the files should be extracted into the Magento root folder, or copied over ensuring directories are included/preserved.
3.	Run enable the module by running the following commands:
```
php bin/magento module:enable FatZebra_Gateway --clear-static-content && php bin/magento setup:upgrade
```
4.	Once the setup:upgrade command completed the module will be available within the Store Admin to configure


Configuration
-------------
To configure the module the following steps should be taken:

1.	Login to the Magento Admin area (this is commonly at https://www.yoursite.com/admin, however it may be different)
2.	From the menu on the left hand side select Stores and then Configuration
3.  Under the Configuration menu select Sales and then Payment Methods
4.	Under the Fat Zebra payment method set the configuration details as required
5.	Once the configuration has been entered click Save Config – this will commit the changes to the database. The payment method can now be tested.

Notes on Fraud Shipping Maps
----------------------------
The Fraud Screening has a set of shipping type codes which need to be matched against the shipping methods used by the store – these codes are:

* low_cost
* same_day
* overnight
* express
* international
* pickup
* other

If nothing matches when mapping these values to the shipping methods used by your store we recommend using the closest available mapping (e.g. Flat Rate/Fixed would be mapped to low_cost, Click&Collect would be pickup), or choose other and inform our support team so that the appropriate rules, where applicable, can be updated.
