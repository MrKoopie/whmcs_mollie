<?php

function getFee($GATEWAY, $payment) {
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

	return $fee;
}

?>
