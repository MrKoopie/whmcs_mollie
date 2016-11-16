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
// We need some fancy database manager for this, see http://docs.whmcs.com/Interacting_With_The_Database
use Illuminate\Database\Capsule\Manager as Capsule;

# Required File Includes
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

$gatewaymodule = "MollieRecurring";

$GATEWAY = getGatewayVariables($gatewaymodule);
require_once __DIR__ . '/../Mollie/functions.php';

if (!$GATEWAY["type"])
die("Module Not Activated"); // Checks gateway module is active before accepting callback

try
{
	/*
	* Initialize the Mollie API library with your API key.
	*
	* See: https://www.mollie.nl/beheer/account/profielen/
	*/
	require_once dirname(__FILE__) . "/../Mollie/API/Autoloader.php";

	if($GATEWAY['testmode'] == 'on')
	$apiKey = $GATEWAY['MollieTestAPIKey'];
	else
	$apiKey = $GATEWAY['MollieLiveAPIKey'];

	/*
	* Initialize the Mollie API library with your API key.
	*
	* See: https://www.mollie.nl/beheer/account/profielen/
	*/
	$mollie = new Mollie_API_Client;
	$mollie->setApiKey($apiKey);
	$payment  = $mollie->payments->get($_POST['id']);

	$invoiceId = checkCbInvoiceID($payment->metadata->invoiceId, $GATEWAY["name"]); // Checks invoice ID is a valid invoice number or ends processing

	if(isset($_GET['createCustomer']) && $_GET['createCustomer'] == true) {
		// A new customer was created

		// Get WHMCS client ID
		$client_id = $payment->metadata->clientId;
		// $invoice = Capsule::table('tblinvoices')->where('id', $invoiceId)->first();
		// $client = Capsule::table('tblclients')->where('id', $invoice->userid)->first();

		// Get Mollie customer ID
		$customer = $mollie->customers->get($payment->customerId);

		// Update WHMCS client with new ID
		$newClient = Capsule::table('tblclients')->where('id', $client_id)->update(['gatewayid' => $customer->id, 'cardnum' => '']);

		$logData = array(
			"clientId" => $client_id,
			"customerId" => $customer->id,
			"customer" => serialize($customer)
		);

		logModuleCall($gatewaymodule, 'Link Customer', $_POST, $logData, '', '');
	}

	$fee = getFee($GATEWAY, $payment);

	checkCbTransID($payment->id); // Checks transaction number isn't already in the database and ends processing if it does

	$logData = Array(
		"id" => $payment->id,
		"customerId" => $payment->customerId,
		"mode" => $payment->mode,
		"createdDatetime" => $payment->createdDatetime,
		"status" => $payment->status,
		"expiryPeriod" => $payment->expiryPeriod,
		"amount" => $payment->amount,
		"metadata" => $payment->metadata
	);

	logModuleCall($gatewaymodule, 'callback', $_POST, $logData, '', '');

	if ($payment->isPaid() == TRUE)
	{
		// The payment was successful
		addInvoicePayment($invoiceId, $payment->id, $payment->amount, $fee, $gatewaymodule); # Apply Payment to Invoice: invoiceId, transactionid, amount paid, fees, modulename
		logTransaction($GATEWAY["name"], $_POST, "Successful"); # Save to Gateway Log: name, data array, status
	}
	elseif ($payment->isOpen() == FALSE)
	{
		// The payment was unsuccesful
		logTransaction($GATEWAY["name"], $_POST, "Unsuccessful"); # Save to Gateway Log: name, data array, status
	}

}
catch (Mollie_API_Exception $e)
{
	// Something went wrong and by not returning a 200 header we let Mollie try it again later.
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

	$responseData['id']						=	@$payment->id;
	$responseData['mode']					=	@$payment->mode;
	$responseData['createdDatetime']		=	@$payment->createdDatetimed;
	$responseData['status']					=	@$payment->status;
	$responseData['paidDatetime']			=	@$payment->paidDatetime;
	$responseData['cancelledDatetime']		=	@$payment->cancelledDatetime;
	$responseData['expiredDatetime']		=	@$payment->expiredDatetime;
	$responseData['expiryPeriod']			=	@$payment->expiryPeriod;
	$responseData['amount']					=	@$payment->amount;
	$responseData['description']			=	@$payment->description;
	$responseData['method']					=	@$payment->method;
	$responseData['metadata']				=	@$payment->metadata;
	$responseData['locale']					=	@$payment->locale;
	$responseData['details']				=	@$payment->details;
	$responseData['links']					=	@$payment->links;
	$responseData['locale']					=	@$payment->locale;
	$responseData['locale']					=	@$payment->locale;
	$responseData['exceptionMessage']		=	$e->getMessage();

	logModuleCall($gatewaymodule, 'Callback Error', $_POST['id'], serialize($responseData), '', '');
}
catch (Exception $e) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

	$responseData['id']						=	@$payment->id;
	$responseData['mode']					=	@$payment->mode;
	$responseData['createdDatetime']		=	@$payment->createdDatetimed;
	$responseData['status']					=	@$payment->status;
	$responseData['paidDatetime']			=	@$payment->paidDatetime;
	$responseData['cancelledDatetime']		=	@$payment->cancelledDatetime;
	$responseData['expiredDatetime']		=	@$payment->expiredDatetime;
	$responseData['expiryPeriod']			=	@$payment->expiryPeriod;
	$responseData['amount']					=	@$payment->amount;
	$responseData['description']			=	@$payment->description;
	$responseData['method']					=	@$payment->method;
	$responseData['metadata']				=	@$payment->metadata;
	$responseData['locale']					=	@$payment->locale;
	$responseData['details']				=	@$payment->details;
	$responseData['links']					=	@$payment->links;
	$responseData['locale']					=	@$payment->locale;
	$responseData['locale']					=	@$payment->locale;
	$responseData['exceptionMessage']		=	$e->getMessage();

	logModuleCall($gatewaymodule, 'Callback Error', $_POST['id'], serialize($responseData), '', '');
}
?>
