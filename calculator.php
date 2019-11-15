<?php 
require_once 'PHPExcel/Classes/PHPExcel/Calculation/Financial.php';
$price = 293473.006325272;
$monthly_payment = 80000;
$max_deposite = 1500;
$rent = 0;

function first($price, $monthly_payment){
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
echo calculate_ROI($price, $monthly_payment, $max_deposite);
?>