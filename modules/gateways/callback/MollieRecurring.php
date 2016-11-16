<?php
// We need some fancy database manager for this, see http://docs.whmcs.com/Interacting_With_The_Database
use Illuminate\Database\Capsule\Manager as Capsule;

# Required File Includes
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';


$gatewaymodule = "MollieRecurring";

$GATEWAY = getGatewayVariables($gatewaymodule);
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
	} else {
		// Generate the fee based on the method.
		if($payment->method == 'ideal')
		{
			$fee 		= $GATEWAY['iDealFixedCost'];
		}
		elseif ($payment->method == 'creditcard')
		{
			$fee 		= $GATEWAY['creditcardFixedCost'] + round($payment->amount / 100 * $GATEWAY['creditcardVariableCost'], 2);
		}
		elseif ($payment->method == 'mistercash')
		{
			$fee 		= $GATEWAY['bancontactMrCashFixedCost'] + round($payment->amount / 100 * $GATEWAY['bancontactMrCashVariableCost'], 2);
		}
		elseif ($payment->method == 'sofort')
		{
			$fee 		= $GATEWAY['sofortBankingFixedCost'] + round($payment->amount / 100 * $GATEWAY['sofortBankingVariableCost'], 2);
		}
		elseif ($payment->method == 'banktransfer')
		{
			$fee 		= $GATEWAY['bankTransferFixedCost'];
		}
		elseif ($payment->method == 'bitcoin')
		{
			$fee 		= $GATEWAY['bitCoinFixedCost'];
		}
		elseif ($payment->method == 'paypal')
		{
			$fee 		= $GATEWAY['payPalFixedCostMollie'] + $GATEWAY['payPalFixedCostPaypal'] + round($payment->amount / 100 * $GATEWAY['payPalFixedVariablePaypal'], 2);
		}
		elseif ($payment->method == 'belfiusdirectnet')
		{
			$fee        = round($payment->amount / 100 * $GATEWAY['BelfiusDirectNetVariableCost'], 2) + $GATEWAY['BelfiusDirectNetFixedCost'];
		}
		else
		{
			$fee 		= '0.00';
		}

		checkCbTransID($payment->id); // Checks transaction number isn't already in the database and ends processing if it does

		$logData = Array(
      "id" => $payment->id,
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
