<?php 
require_once 'PHPExcel/Classes/PHPExcel/Calculation/Financial.php';

function calPMT($apr, $term, $loan)
{
  $amount = $apr * -$loan * pow((1 + $apr), $term) / (1 - pow((1 + $apr), $term));
  return $amount;
}

$goal_seek_value = 293473.006325272;
$initial_payment = 80000;
$max_deposite = 4500;
$rent = 0;

$how_much_final = 0;

function DisplayHowMuchAffordable($goal_seek_value, $initial_payment, $max_deposite){
	$how_much_affordable =  round ( $goal_seek_value * pow( 1.0350, 3 ) ) * 0.065;
	$how_much_affordable = $how_much_affordable - $initial_payment;
	$how_much_affordable = $how_much_affordable / 36;

	if($how_much_affordable > 0)
	{
		$how_much_final = $goal_seek_value;
	}
	else
	{
		$how_much_affordable =  round ( $goal_seek_value * pow( 1.0350, 3 ) ) * 0.065;
		$how_much_affordable = $goal_seek_value + $how_much_affordable;
		$how_much_final = $how_much_affordable;
	}

	$howMuchAffordable = floor ( $how_much_final / 1000 ) * 1000; 	

	$homePurchasePriceRequiredEOT = $how_much_final * pow( 1.0350, 3 );
	$downPaymentRequiredEOT = $homePurchasePriceRequiredEOT * 0.065;
	$monthlyPurchasedCreditRequired = ( $downPaymentRequiredEOT - $initial_payment ) / 36;

	$purchaseCredit = ($monthlyPurchasedCreditRequired < 0) ? 0 : $monthlyPurchasedCreditRequired * 12;

	$rent = ( $max_deposite * 12 ) - $purchaseCredit;

	$annualPayment = $purchaseCredit + $rent;

	//Annual Operating Income
		//Annual Expense
		$propertyTaxes = $how_much_final * 0.0085087;
		$insurance = 1000;
	$annualOperatingNetIncome = $rent - $propertyTaxes - $insurance;

	$annualCapitalizationRate = $annualOperatingNetIncome / $how_much_final;

	//Annual Debt Information
	$downPayment = $how_much_final * 0.2;
	$loanAmount = $how_much_final - $downPayment;

	$lengthMortgage = 25;
	$annualInterestRate = 0.3;

	$rate = 0.0025;
	$nper = 25*12;
	$pv = $loanAmount;

	$monthlyMortgagePayment = calPMT($rate, $nper, $pv);

	$objPHPExcel = new PHPExcel_Calculation_Financial();

	$start_period = 1;
	$end_period = 12;
	$type = 0;

	$cumipmt = $objPHPExcel->CUMIPMT($rate, $nper, $pv, $start_period, $end_period, $type);
	$annualInterest = round( abs($cumipmt) , 2);
	$cumprintc = $objPHPExcel->CUMPRINC($rate, $nper, $pv, $start_period, $end_period, $type);
	$annualPrincipal = round( abs($cumprintc) , 2);

	$totalAnnualDebtService = $annualInterest + $annualPrincipal; 

	//Cash Flow & ROI
	$cashFlowandROI = $annualOperatingNetIncome - $totalAnnualDebtService; // 0	

	$initialInvesmentRequired = $downPayment + 5000;
	$saleOptionPrice = $homePurchasePriceRequiredEOT;
	$homeAppreciation = $saleOptionPrice - $how_much_final;
	$mortgagePaydown_1 = abs ( $objPHPExcel->CUMPRINC($rate, $nper, $pv, 1, 12, $type)  );
	$mortgagePaydown_2 = abs ( $objPHPExcel->CUMPRINC($rate, $nper, $pv, 13, 24, $type) );
	$mortgagePaydown_3 = abs ( $objPHPExcel->CUMPRINC($rate, $nper, $pv, 25, 36, $type) );

	$mortgagePaydown = $mortgagePaydown_1 + $mortgagePaydown_2 + $mortgagePaydown_3;

	$positiveCashFlow = ( $cashFlowandROI * 36 ) / 12;
	$positiveCashFlow = floor( $positiveCashFlow / 100 ) * 100;

	$closingCostsCarrying = 0;
	$closingCosts = 0;
	$creditCouncilling = 0;


	$totalProfit = ($homeAppreciation + $mortgagePaydown + $positiveCashFlow) - ($closingCosts + $creditCouncilling);
	$totalROI = round( ($totalProfit / $initialInvesmentRequired) , 2);

	$annualROI_1 = round ( ( $totalProfit + $initialInvesmentRequired ) / $initialInvesmentRequired, 2);
	$annualROI = round ( pow( $annualROI_1, 0.33 ) - 1, 2);
	//return $annualROI;
	return array("howMuchAffordable"=>$howMuchAffordable, "cashFlowandROI"=>round($cashFlowandROI));
}

//DisplayHowMuchAffordable($goal_seek_value, $initial_payment, $max_deposite);
//$price = 293473.006325272;

/*for($i = 0; ; $i+=1000)
{
	$calculate_roi = DisplayHowMuchAffordable($i, $initial_payment, $max_deposite);
	echo "VALUE : " . $i . "  => " .$calculate_roi;
	if($calculate_roi > 0.21 && $calculate_roi < 0.23){ break; }	
	echo "<br />";
}*/


for($i = 10; ; $i+=1000)
{
	$calculate_roi = DisplayHowMuchAffordable($i, $initial_payment, $max_deposite);
	if($calculate_roi['cashFlowandROI'] <= 0){
		echo $calculate_roi['howMuchAffordable'];
		break;
	}
}

//DisplayHowMuchAffordable($goal_seek_value, $initial_payment, $max_deposite);
exit;






/*function first($price, $monthly_payment){
	$pay_1_5 = $price * pow( 1.0350, 3 ) ;	
	$down_eot = round( $pay_1_5 * 0.065, 2 );		
	$final_return = ( $down_eot - $monthly_payment ) / 36;
	return round( $final_return , 2);
}

function second($price, $monthly_payment, $max_deposite){
	$monthly_credit = first($price, $monthly_payment);
	$annual_payment = $max_deposite * 12;
	$monthly_credit = round( $monthly_credit * 12 , 2);
	$rent = $annual_payment - $monthly_credit;
	$final_annual_payment = $rent + $monthly_credit;
	return $rent;		
}

function third($price, $monthly_payment, $max_deposite){
	$rent = second($price, $monthly_payment, $max_deposite);	
	$property_taxes = round( $price * 0.0085087, 2);
	$insurance = 1000;
	$annual_operating_net_income = $rent - $property_taxes - $insurance;
	return $annual_operating_net_income;
}

function calPMT($apr, $term, $loan)
{
  $term = $term * 12;
  $apr = $apr / 1200;
  $amount = $apr * -$loan * pow((1 + $apr), $term) / (1 - pow((1 + $apr), $term));
  return round($amount);
}

function four($price, $monthly_payment, $max_deposite){
	$down_payment = ( $price * 20 ) / 100;
	$loan_amount = $price - $down_payment;
	$length_mortgage = 25;
	$annual_interest_rate = 3;

	$pmt = calPMT($annual_interest_rate, 25, $loan_amount);

	$objPHPExcel = new PHPExcel_Calculation_Financial();
	$rate = 0.0025;
	$nper = 25*12;
	$pv = $loan_amount;
	$start_period = 1;
	$end_period = 12;
	$type = 0;

	$cumipmt = $objPHPExcel->CUMIPMT($rate, $nper, $pv, $start_period, $end_period, $type);
	$annual_interest = round( abs($cumipmt) , 2);
	$cumprintc = $objPHPExcel->CUMPRINC($rate, $nper, $pv, $start_period, $end_period, $type);
	$annual_principal = round( abs($cumprintc) , 2);

	$toal_annual_deb = $annual_interest + $annual_principal; 

	return $toal_annual_deb;
}

function calculate_ROI($price, $monthly_payment, $max_deposite)
{
	$annual_operating_net_income = third($price, $monthly_payment, $max_deposite);	
	$total_annual_debt_service = four($price, $monthly_payment, $max_deposite);

	$annual_roi = $annual_operating_net_income - $total_annual_debt_service;
	$annual_roi = round( abs($annual_roi) , 2);
	
	return $annual_roi;
}

function applicable_amount($price, $monthly_payment, $max_deposite){
	$final_amount = 0;
	for($i = $price; ; $i+=1000)
	{
		$calculate_roi = calculate_ROI($i, $monthly_payment, $max_deposite);		
		$final_amount = $i;
		if($calculate_roi > 2400 && $calculate_roi < 2500){ break; }			
	}
	return $final_amount;
}

/*for($i = $price; ; $i+=1000)
{
	$calculate_roi = calculate_ROI($i, $monthly_payment, $max_deposite);
	echo "VALUE : " . $i . "  => " .$calculate_roi;
	if($calculate_roi > 2400 && $calculate_roi < 2500){ break; }	
	echo "<br />";
}*/

//echo round( calculate_ROI($price, $monthly_payment, $max_deposite), -3);
//echo calculate_ROI($price, $monthly_payment, $max_deposite);
?>