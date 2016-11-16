# whmcs_mollie
[Mollie](https://www.mollie.com/) is a Dutch payment provider with a transaction cost and easy integration. This module adds the Mollie payment gateway to WHMCS.

## Installation
This module consists of two parts: A regular payment gateway, and a recurring payment gateway.  
Both modules can be installed at the same time.

**Regular Gateway**

1. Upload `modules/gateways/Mollie` to `whmcs/modules/gateways` (must be done for both versions)
2. Upload `modules/gateways/Mollie.php` to `whmcs/modules/gateways`
3. Upload `modules/gateways/callback/Mollie.php` to `whmcs/modules/gateways/callback`

**Recurring Gateway**

1. Upload `modules/gateways/Mollie` to `whmcs/modules/gateways` (must be done for both versions)
2. Upload `modules/gateways/MollieRecurring.php` to `whmcs/modules/gateways`
3. Upload `modules/gateways/callback/MollieRecurring_payment.php` to `whmcs/modules/gateways/callback`
4. Upload `modules/gateways/callback/MollieRecurring_register.php` to `whmcs/modules/gateways/callback`

## Settings
The modules allow you to define your own transaction fees - These will be replaced by automatic fees once this is implemented.

Other than that, you have the following fields:

| Name                     | Description                                                                   | Example                               |
|--------------------------|-------------------------------------------------------------------------------|---------------------------------------|
| Transaction Description  | Transaction description sent to Mollie, may contain {invoiceID}               | Invoice #{invoiceID}                  |
| Verification Description | Description used for verification payments. Only applies to Recurring version | Recurring payment authorization       |
| Mollie Live API Key      | API key used for Mollie.                                                      | live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx |
| Mollie Test API Key      | Test API key used for testing the module                                      | test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx |
| Test Mode                | Tick to enable test mode, useful for debugging                                | ✓                                     |

## Features

* Uses Mollie hosted payment screens, no need to mess with certificates
* Supports all Mollie payment methods
* Recurring module supports automatic capture for new invoices
