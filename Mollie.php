<?php
/**
 * WHMCS Sample Payment Gateway Module
 *
 * Payment Gateway modules allow you to integrate payment solutions with the
 * WHMCS platform.
 *
 * This sample file demonstrates how a payment gateway module for WHMCS should
 * be structured and all supported functionality it can contain.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "gatewaymodule" and therefore all functions
 * begin "gatewaymodule_".
 *
 * If your module or third party API does not support a given function, you
 * should not define that function within your module. Only the _config
 * function is required.
 *
 * For more information, please refer to the online documentation.
 *
 * @see http://docs.whmcs.com/Gateway_Module_Developer_Docs
 *
 * @copyright Copyright (c) WHMCS Limited 2015
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Tell WHMCS what data we need.
 * @return  array An array with all the required fields.
 */
function Mollie_config() {
	$configarray = array(
		"FriendlyName" => array(
			"Type" => "System",
			"Value"=>"Mollie"
			),
		"transactionDescription" => array(
			"FriendlyName" => "Transaction description",
			"Type" => "text",
			"Size" => "50",
			"Value" => "Your company name - Invoice #{invoiceID}",
			"Description" => "Example configuration: 'Your company name - Invoice #{invoiceID}'"
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
function Mollie_link($params) {
	// Check if the currency is set to euro, if not we can not process it.
	$currency = strtolower($params['currency']);
	if($currency != 'eur')
		return 'This payment option is only available for the currency EURO.';

	try{
		// Pre-generate the required data. We do this here to make sure all data is available for debugging purposes.
		$inputData = array(
			"amount"       => $params['amount'],
			"description"  => str_replace('{invoiceID}', $params['invoiceid'], $params['transactionDescription']),
			"redirectUrl"  => $params['systemurl']."/viewinvoice.php?id=".$params['invoiceid'],
			"webhookUrl"   => $params['systemurl']."/modules/gateways/Mollie/callback.php?invoiceId=".$params['invoiceid'],
			"metadata"     => array(
				"invoiceId" => $params['invoiceid'],
				),
			);

		// Include the Mollie library
		require_once dirname(__FILE__) . "/Mollie/Mollie/API/Autoloader.php";
		
		// Check if we are using the test mode.
		if($params['testmode'] == 'on')
			$apiKey = $params['MollieTestAPIKey'];
		else
			$apiKey = $params['MollieLiveAPIKey'];
		
		/*
		 * Initialize the Mollie API library with your API key.
		 *
		 * See: https://www.mollie.nl/beheer/account/profielen/
		 */
		$mollie = new Mollie_API_Client;
		$mollie->setApiKey($apiKey);

		/*
		 * Payment parameters:
		 *   amount        Amount in EUROs. This example creates a € 10,- payment.
		 *   description   Description of the payment.
		 *   redirectUrl   Redirect location. The customer will be redirected there after the payment.
		 *   metadata      Custom metadata that is stored with the payment.
		 */
		$payment = $mollie->payments->create($inputData);

		/*
		 * Send the customer off to complete the payment.
		 */
		$code = '<form method="post" action="'.$payment->getPaymentUrl().'">
			<input type="submit" value="'.$params['langpaynow'].' >>" />
		</form>';
	}
	catch (Mollie_API_Exception $e)
	{
		logModuleCall('Mollie', 'Mollie Link', $inputData, $e->getMessage(), '', '');
		$code = 'Something went wrong, please contact support.';
	}

	return $code;
}
/**
 * WHMCS Mollie refund function: Tells Mollie to refund the transaction.
 * @param array $params See http://docs.whmcs.com/Gateway_Module_Developer_Docs
 */
function Mollie_refund($params) {
	try{
		require_once dirname(__FILE__) . "/Mollie/Mollie/API/Autoloader.php";
		
		if($params['testmode'] == 'on')
			$apiKey = $params['MollieTestAPIKey'];
		else
			$apiKey = $params['MollieLiveAPIKey'];
		
		/*
		 * Initialize the Mollie API library with your API key.
		 *
		 * See: https://www.mollie.nl/beheer/account/profielen/
		 */
		$mollie = new Mollie_API_Client;
		$mollie->setApiKey($apiKey);

		$payment = $mollie->payments->get($params['transid']);
		$refund = $mollie->payments->refund($payment, $params['amount']);

		$results = array();
		$results["status"] = "success";
		$results["transid"] = $refund->id;

		return array( "status" => "success", "transid" => $refund->id, "rawdata" => $results);
	}
	catch (Mollie_API_Exception $e)
	{
		logModuleCall('Mollie', 'Mollie Refund action', $params['transid'], $e->getMessage(), '', '');
		return array("status" => "error", "rawdata" => htmlspecialchars($e->getMessage()));
	}
}

?>
