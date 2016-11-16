<?php
/**
* WHMCS Mollie Recurring Payment Gateway Module
*
* Payment Gateway modules allow you to integrate payment solutions with the
* WHMCS platform.
*
* @see https://github.com/ducohosting/whmcs_mollie
*
* @copyright Copyright (c) Duco Hosting 2016
* @license https://github.com/ducohosting/whmcs_mollie/blob/master/LICENSE MIT
*/

if (!defined("WHMCS")) {
  die("This file cannot be accessed directly");
}

/**
* Tell WHMCS what data we need.
* @return  array An array with all the required fields.
*/
function MollieRecurring_config() {
  $configarray = array(
    "FriendlyName" => array(
      "Type" => "System",
      "Value"=>"Mollie Recurring"
    ),
    "transactionDescription" => array(
      "FriendlyName" => "Transaction description",
      "Type" => "text",
      "Size" => "50",
      "Value" => "Your company name - Invoice #{invoiceID}",
      "Description" => "Example configuration: 'Your company name - Invoice #{invoiceID}'"
    ),
    "verificationDescription" => array(
      "FriendlyName" => "Verification Description",
      "Type" => "text",
      "Size" => "50",
      "Value" => "Your company name - Recurring payment authorization",
      "Description" => "Transaction description for recurring payment authorizations"
    ),
    "MollieLiveAPIKey" => array(
      "FriendlyName" => "Mollie Live API Key",
      "Type" => "text",
      "Size" => "50",
      "Description" => "Go to <a href='https://www.mollie.com/beheer/account/profielen/' target='_blank'>Mollie</a> to obtain your Live API key."
    ),
    "MollieTestAPIKey" => array(
      "FriendlyName" => "Mollie Test API Key",
      "Type" => "text",
      "Size" => "50",
      "Description" => "Not required. Go to <a href='https://www.mollie.com/beheer/account/profielen/' target='_blank'>Mollie</a> to obtain your Test API key."
    ),
    "testmode" => array(
      "FriendlyName" => "Test Mode",
      "Type" => "yesno",
      "Description" => "Tick this to use the test gateway of Mollie."
    ),

    // Transaction cost settings
    "iDealFixedCost" => array(
      "FriendlyName" => "iDeal fixed cost",
      "Type" => "text",
      "Size" => "5",
      "Value" => "0.29",
      "Description" => "The fixed cost per transaction. NOTE: Use . as separator."
    ),
    "creditcardFixedCost" => array(
      "FriendlyName" => "Creditcard fixed cost",
      "Type" => "text",
      "Size" => "5",
      "Value" => "0.25",
      "Description" => "The fixed cost per transaction. NOTE: Use . as separator."
    ),
    "creditcardVariableCost" => array(
      "FriendlyName" => "Creditcard variable cost",
      "Type" => "text",
      "Size" => "5",
      "Value" => "2.8",
      "Description" => "The variable cost per transaction. NOTE: Use . as separator and do not use %."
    ),
    "bancontactMrCashFixedCost" => array(
      "FriendlyName" => "Bancontact/Mister Cash fixed cost",
      "Type" => "text",
      "Size" => "5",
      "Value" => "0.25",
      "Description" => "The fixed cost per transaction. NOTE: Use . as separator."
    ),
    "bancontactMrCashVariableCost" => array(
      "FriendlyName" => "Bancontact/Mister Cash variable cost",
      "Type" => "text",
      "Size" => "5",
      "Value" => "1.5",
      "Description" => "The variable cost per transaction. NOTE: Use . as separator and do not use %."
    ),
    "sofortBankingFixedCost" => array(
      "FriendlyName" => "SOFORT Banking fixed cost",
      "Type" => "text",
      "Size" => "5",
      "Value" => "0.25",
      "Description" => "The fixed cost per transaction. NOTE: Use . as separator."
    ),
    "sofortBankingVariableCost" => array(
      "FriendlyName" => "SOFORT Banking variable cost",
      "Type" => "text",
      "Size" => "5",
      "Value" => "0.9",
      "Description" => "The variable cost per transaction. NOTE: Use . as separator and do not use %."
    ),
    "bankTransferFixedCost" => array(
      "FriendlyName" => "Bank Transfer fixed cost",
      "Type" => "text",
      "Size" => "5",
      "Value" => "0.25",
      "Description" => "The fixed cost per transaction. NOTE: Use . as separator."
    ),
    "bitCoinFixedCost" => array(
      "FriendlyName" => "Bitcoin fixed cost",
      "Type" => "text",
      "Size" => "5",
      "Value" => "0.25",
      "Description" => "The fixed cost per transaction. NOTE: Use . as separator."
    ),
    "payPalFixedCostMollie" => array(
      "FriendlyName" => "Paypal fixed cost Mollie",
      "Type" => "text",
      "Size" => "5",
      "Value" => "0.10",
      "Description" => "The fixed cost per transaction billed by Mollie. NOTE: Use . as separator."
    ),
    "payPalFixedCostPaypal" => array(
      "FriendlyName" => "Paypal fixed cost Paypal",
      "Type" => "text",
      "Size" => "5",
      "Value" => "0.35",
      "Description" => "The fixed cost per transaction billed by Paypal. NOTE: Use . as separator."
    ),
    "payPalFixedVariablePaypal" => array(
      "FriendlyName" => "Paypal variable cost Paypal",
      "Type" => "text",
      "Size" => "5",
      "Value" => "3.4",
      "Description" => "The variable cost per transaction billed by Paypal. NOTE: Use . as separator and do not use %."
    ),
    "paySafeCardVariableCost" => array(
      "FriendlyName" => "Paysafecard variable cost",
      "Type" => "text",
      "Size" => "5",
      "Value" => "15",
      "Description" => "The variable cost per transaction. NOTE: Use . as separator and do not use %."
    ),
    "BelfiusDirectNetFixedCost" => array(
      "FriendlyName" => "Belfius Direct Net fixed cost",
      "Type" => "text",
      "Size" => "5",
      "Value" => "0.25",
      "Description" => "The fixed cost per transaction. NOTE: Use . as separator."
    ),
    "BelfiusDirectNetVariableCost" => array(
      "FriendlyName" => "Belfius Direct Net variable cost",
      "Type" => "text",
      "Size" => "5",
      "Value" => "0.9",
      "Description" => "The variable cost per transaction. NOTE: Use . as separator and do not use %."
    ),
  );
  return $configarray;
}

/**
* Generates a link for the WHMCS client area.
* @param Array $params See http://docs.whmcs.com/Gateway_Module_Developer_Docs
*/
function MollieRecurring_link($params) {
  // Check if the currency is set to euro, if not we can not process it.
  $currency = strtolower($params['currency']);
  if($currency != 'eur')
  return 'This payment option is only available for the currency EURO.';

  try{
    // Setup mollie API connection
    require_once dirname(__FILE__) . "/Mollie/API/Autoloader.php";

    // Check if we are using the test mode.
    if($params['testmode'] == 'on')
    $apiKey = $params['MollieTestAPIKey'];
    else
    $apiKey = $params['MollieLiveAPIKey'];

    $mollie = new Mollie_API_Client;
    $mollie->setApiKey($apiKey);

    // Create new customer
    $customerData = array(
      "name" => $params['clientdetails']['firstname'].' '.$params['clientdetails']['lastname'],
      "email" => $params['clientdetails']['email']
    );

    $customer = $mollie->customers->create($customerData);

    $customerResponse = array(
      "id" => $customer->id,
      "name" => $customer->name,
      "mode" => $customer->mode,
      "emai" => $customer->email,
      "metadata" => $customer->metadata,
      "createdDatetime" => $customer->createdDatetime
    );

    logModuleCall('Mollie Recurring', 'Create Customer', $customerData, $customerResponse, '', '');

    /*
    * Payment parameters:
    *   amount         Amount in EUROs.
    *   description    Description of the payment.
    *   redirectUrl    Redirect location. The customer will be redirected there after the payment.
    *   webhookUrl     Webhook callback URL. Called by Mollie on status changes
    *   metadata       Custom metadata that is stored with the payment.
    */
    $inputData = array(
      "amount"        => $params['amount'],
      "description"   => str_replace('{invoiceID}', $params['invoiceid'], $params['transactionDescription']),
      "recurringType" => "first",
      "customerId"    => $customer->id,
      "redirectUrl"   => $params['systemurl']."/viewinvoice.php?id=".$params['invoiceid'],
      "webhookUrl"    => $params['systemurl']."/modules/gateways/callback/MollieRecurring.php?invoiceId=".$params['invoiceid'],
      "metadata"      => array(
        "invoiceId"   => $params['invoiceid'],
      ),
    );

    $payment = $mollie->payments->create($inputData);

    $logData = Array(
      "id" => $payment->id,
      "mode" => $payment->mode,
      "method" => $payment->method,
      "customerId" => $payment->customerId,
      "recurringType" => $payment->recurringType,
      "createdDatetime" => $payment->createdDatetime,
      "status" => $payment->status,
      "expiryPeriod" => $payment->expiryPeriod,
      "amount" => $payment->amount,
      "metadata" => $payment->metadata
    );

    logModuleCall('Mollie Recurring', 'Link', $inputData, $logData, '', '');

    /*
    * Send the customer off to complete the payment.
    */
    $code = '<form method="post" action="'.$payment->getPaymentUrl().'">
    <input type="submit" value="'.$params['langpaynow'].'" />
    </form>';
  }
  catch (Mollie_API_Exception $e)
  {
    logModuleCall('Mollie Recurring', 'Link Error', $inputData, $e->getMessage(), '', '');
    $code = 'Something went wrong, please contact support.';
  }

  return $code;
}

function MollieRecurring_remoteinput($params) {
  try{
    // Setup mollie API connection
    require_once dirname(__FILE__) . "/Mollie/API/Autoloader.php";

    // Check if we are using the test mode.
    if($params['testmode'] == 'on')
    $apiKey = $params['MollieTestAPIKey'];
    else
    $apiKey = $params['MollieLiveAPIKey'];

    $mollie = new Mollie_API_Client;
    $mollie->setApiKey($apiKey);

    // Create new customer
    $customerData = array(
      "name" => $params['clientdetails']['firstname'].' '.$params['clientdetails']['lastname'],
      "email" => $params['clientdetails']['email']
    );

    $customer = $mollie->customers->create($customerData);

    $customerResponse = array(
      "id" => $customer->id,
      "name" => $customer->name,
      "mode" => $customer->mode,
      "emai" => $customer->email,
      "metadata" => $customer->metadata,
      "createdDatetime" => $customer->createdDatetime
    );

    logModuleCall('Mollie Recurring', 'Create Customer', $customerData, $customerResponse, '', '');

    /*
    * Payment parameters:
    *   amount         Amount in EUROs.
    *   description    Description of the payment.
    *   redirectUrl    Redirect location. The customer will be redirected there after the payment.
    *   webhookUrl     Webhook callback URL. Called by Mollie on status changes
    *   metadata       Custom metadata that is stored with the payment.
    */
    $inputData = array(
      "amount"        => 0.01,
      "description"   => $params['verificationDescription'],
      "recurringType" => "first",
      "customerId"    => $customer->id,
      "redirectUrl"   => $params['systemurl']."/viewinvoice.php?id=".$params['invoiceid'],
      "webhookUrl"    => $params['systemurl']."/modules/gateways/callback/MollieRecurring.php?createCustomer=true&invoiceId=".$params['invoiceid'],
      "metadata"      => array(
        "invoiceId"   => $params['invoiceid'],
      ),
    );

    $payment = $mollie->payments->create($inputData);

    $logData = Array(
      "id" => $payment->id,
      "mode" => $payment->mode,
      "method" => $payment->method,
      "customerId" => $payment->customerId,
      "recurringType" => $payment->recurringType,
      "createdDatetime" => $payment->createdDatetime,
      "status" => $payment->status,
      "expiryPeriod" => $payment->expiryPeriod,
      "amount" => $payment->amount,
      "metadata" => $payment->metadata
    );

    logModuleCall('Mollie Recurring', 'Create Customer', $inputData, $logData, '', '');

    /*
    * Send the customer off to complete the payment.
    */
    $code = '<form method="post" action="'.$payment->getPaymentUrl().'">
    <input type="submit" value="'.$params['langpaynow'].'" />
    </form>';
  }
  catch (Mollie_API_Exception $e)
  {
    logModuleCall('Mollie Recurring', 'Link Error', $inputData, $e->getMessage(), '', '');
    $code = 'Something went wrong, please contact support.';
  }

  return $code;
}

function MollieRecurring_remoteupdate($params) {
  return '<strong>Unfortunately, updating your payment information is not possible at this time.</strong>';
}

// Disable local CC storage
function MollieRecurring_nolocalcc() {}

/**
* WHMCS Mollie refund function: Tells Mollie to refund the transaction.
* @param array $params See http://docs.whmcs.com/Gateway_Module_Developer_Docs
*/
function MollieRecurring_refund($params) {
  try{
    require_once dirname(__FILE__) . "/Mollie/API/Autoloader.php";

    if($params['testmode'] == 'on')
    $apiKey = $params['MollieTestAPIKey'];
    else
    $apiKey = $params['MollieLiveAPIKey'];

    // These values should be removed from logging
    $secretValues = array($params['MollieTestAPIKey'], $params['MollieLiveAPIKey'], $params['clientdetails']['password']);

    /*
    * Initialize the Mollie API library with your API key.
    *
    * See: https://www.mollie.nl/beheer/account/profielen/
    */
    $mollie = new Mollie_API_Client;
    $mollie->setApiKey($apiKey);

    $payment = $mollie->payments->get($params['transid']);
    $refund = $mollie->payments->refund($payment, $params['amount']);

    $results = array(
      "status" => $refund->status,
      "transid" => $refund->id,
      "amount" => $refund->amount,
      "datetime" => $refund->refundedDateTime
    );

    logModuleCall('Mollie Recurring', 'Refund', $params, $results, '', $secretValues);

    return array( "status" => "success", "transid" => $refund->id, "rawdata" => $results);

  }
  catch (Mollie_API_Exception $e)
  {
    logModuleCall('Mollie Recurring', 'Refund Error', $params['transid'], $e->getMessage(), '', '');
    return array("status" => "error", "rawdata" => htmlspecialchars($e->getMessage()));
  }
}

?>
