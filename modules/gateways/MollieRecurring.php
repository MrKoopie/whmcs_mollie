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

$GLOBALS['gatewaymodule'] = "MollieRecurring";

require_once __DIR__ . '/Mollie/functions.php';

/**
* Tell WHMCS what data we need.
* @return  array An array with all the required fields.
*/
function MollieRecurring_config() {
  $configarray = array(
    "FriendlyName" => array(
      "Type" => "System",
      "Value" => "Mollie Recurring"
    ),
    "transactionDescription" => array(
      "FriendlyName" => "Transaction description",
      "Type" => "text",
      "Size" => "50",
      "Value" => "Invoice #{invoiceID}",
      "Description" => "Example configuration: 'Invoice #{invoiceID}'"
    ),
    "verificationDescription" => array(
      "FriendlyName" => "Verification Description",
      "Type" => "text",
      "Size" => "50",
      "Value" => "Recurring payment authorization",
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
function MollieRecurring_capture($params) {
  $GATEWAY = getGatewayVariables($GLOBALS['gatewaymodule']);
  // Check if the currency is set to euro, if not we can not process it.
  logModuleCall($GLOBALS['gatewaymodule'], 'Capture Starting', $params, '', '', '');
  $currency = strtolower($params['currency']);
  if($currency != 'eur') return 'This payment option is only available for the currency EURO.';

  try{
    // Setup mollie API connection
    require_once dirname(__FILE__) . "/Mollie/API/Autoloader.php";
    require_once __DIR__ . '/../../includes/gatewayfunctions.php';

    $GATEWAY = getGatewayVariables($GLOBALS['gatewaymodule']);
    require_once __DIR__ . '/Mollie/functions.php';

    // Check if we are using the test mode.
    if($params['testmode'] == 'on')
    $apiKey = $params['MollieTestAPIKey'];
    else
    $apiKey = $params['MollieLiveAPIKey'];

    $mollie = new Mollie_API_Client;
    $mollie->setApiKey($apiKey);

    $mandates = $mollie->customers_mandates->withParentId($params['gatewayid'])->all();

    if($mandates->count <= 0 || !isset($mandates->count)) {
      $code = array(
        "status" => "error",
        "rawdata" => "Customer " . $params['gatewayid'] . " has no mandates"
      );

      logModuleCall($GLOBALS['gatewaymodule'], 'Capture Error - no mandates', $params, json_encode($mandates), '', '');
    }
    else {
      $validMandates = false;
      foreach($mandates as $id => $data) {
        if($data->status == 'valid') $validMandates = true;
        break;
      }

      if(!$validMandates) {
        $code = array(
          "status" => "error",
          "rawdata" => "Customer " . $params['gatewayid'] . " has no valid mandates"
        );

        logModuleCall($GLOBALS['gatewaymodule'], 'Capture Error - no valid mandates', $params, json_encode($mandates), '', '');
      }
      else {
        $inputData = array(
          "amount"        => $params['amount'],
          "customerId"    => $params['gatewayid'],
          "recurringType" => "recurring",
          "description"   => str_replace('{invoiceID}', $params['invoiceid'], $params['transactionDescription']),
          "redirectUrl"   => $params['systemurl']."/viewinvoice.php?id=".$params['invoiceid'],
          "webhookUrl"    => $params['systemurl']."/modules/gateways/callback/MollieRecurring_payment.php?invoiceId=".$params['invoiceid'],
          "metadata"      => array(
            "invoiceId"   => $params['invoiceid'],
            "clientId"    => $params['clientdetails']['userid']
          )
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

        logModuleCall($GLOBALS['gatewaymodule'], 'Capture', $inputData, $logData, '', '');

        $code = array(
          "status" => ($payment->status == "paid") ? "success" : "declined",
          "transid" => $payment->id,
          "rawdata" => serialize($payment),
          "fee" => getFee($GATEWAY, $payment)
        );
      }
    }
  }
  catch (Mollie_API_Exception $e)
  {
    logModuleCall($GLOBALS['gatewaymodule'], 'Capture Error', $params, $e->getMessage(), '', '');
    $code = array(
      "status" => "error",
      "rawdata" => "Something went wrong, please contact support."
    );
  }
  catch (Exception $e) {
    logModuleCall($GLOBALS['gatewaymodule'], 'Capture Error', $params, $e->getMessage(), '', '');
    $code = array(
      "status" => "error",
      "rawdata" => "Something went wrong, please contact support."
    );
  }

  return $code;
}

function MollieRecurring_remoteinput($params) {
  $GATEWAY = getGatewayVariables($GLOBALS['gatewaymodule']);
  logModuleCall($GLOBALS['gatewaymodule'], 'remote input', $params, '', '', '');
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

    $code = "Error";

    if(!isset($params['gatewayid']) || strlen($params['gatewayid']) < 5) {
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

      logModuleCall($GLOBALS['gatewaymodule'], 'Create Customer', $customerData, $customerResponse, '', '');

      $code = inputNew($mollie, $params, $customer);
    }
    else {
      $customer = $mollie->customers->get($params['gatewayid']);
      $code = inputExisting($mollie, $params, $customer);
    }



  }
  catch (Mollie_API_Exception $e)
  {
    logModuleCall($GLOBALS['gatewaymodule'], 'Capture Error', $inputData, json_encode(array("message" => $e->getMessage(), "trace" => $e->getTraceAsString(), "file" => $e->getFile, "line" => $e->getLine)), '', '');
    $code = 'Something went wrong, please contact support.';
  }

  return $code;
}

function inputNew($mollie, $params, $customer) {
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
    "description"   => $params['verificationDescription'],
    "recurringType" => "first",
    "customerId"    => $customer->id,
    "redirectUrl"   => $params['systemurl']."/viewinvoice.php?id=".$params['invoiceid'],
    "webhookUrl"    => $params['systemurl']."/modules/gateways/callback/MollieRecurring_register.php?invoiceId=".$params['invoiceid'],
    "metadata"      => array(
      "invoiceId"   => $params['invoiceid'],
      "clientId"    => $params['clientdetails']['userid']
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

  logModuleCall($GLOBALS['gatewaymodule'], 'Create Authorization Link', $inputData, $logData, '', '');

  /*
  * Send the customer off to complete the payment.
  */
  $code = '<form method="get" action="'.$payment->getPaymentUrl().'">
  <button><a href="'.$payment->getPaymentUrl().'" target="_blank">Authorize Us</a></button>
  <input type="hidden" value="'.$params['amount'].'" />
  <input type="submit" value="'.$params['langpaynow'].'!!" />
  </form>';

  return $code;
}

function inputExisting($mollie, $params, $customer) {
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
    "recurringType" => "recurring",
    "customerId"    => $customer->id,
    "redirectUrl"   => $params['systemurl']."/viewinvoice.php?id=".$params['invoiceid'],
    "webhookUrl"    => $params['systemurl']."/modules/gateways/callback/MollieRecurring_payment.php?invoiceId=".$params['invoiceid'],
    "metadata"      => array(
      "invoiceId"   => $params['invoiceid'],
      "clientId"    => $params['clientdetails']['userid']
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

  logModuleCall($GLOBALS['gatewaymodule'], 'Create Payment Request', $inputData, $logData, '', '');

  /*
  * Send the customer off to complete the payment.
  */
  $code = '<a class="btn btn-primary" href="viewinvoice.php?id='.$params['invoiceid'].'">' . $params['langpaynow'] . '</a>';

  return $code;
}

function MollieRecurring_remoteupdate($params) {
  return;
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

      logModuleCall($GLOBALS['gatewaymodule'], 'Refund', $params, $results, '', $secretValues);

      return array( "status" => "success", "transid" => $refund->id, "rawdata" => $results);

    }
    catch (Mollie_API_Exception $e)
    {
      logModuleCall($GLOBALS['gatewaymodule'], 'Refund Error', $params['transid'], $e->getMessage(), '', '');
      return array("status" => "error", "rawdata" => htmlspecialchars($e->getMessage()));
    }
  }

  ?>
