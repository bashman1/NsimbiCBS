<?php
/*
* File name: RepaymentScheduleLib.php (controller)
* Author : yasira mugume
* Website : yasiratech.com
* Email : myasira2020@gmail.com
* Telephone: (+256) 707 251 554, (+256) 779 028 980
* Description:
*/
class RepaymentScheduleLib
{
	public $refineSchedule;
	public function __construct()
	{
		$this->refineSchedule = 1;
	}

	/**
	 *  method generateLoanSchedule
	 *
	 */
	//----------GENERATE PAYMENT SCHEDULE----------
	public function generateLoanSchedule($loan_data)
	{
		$obj = new stdClass();
		$obj->int_rate = $loan_data->int_rate;
		$obj->period = $loan_data->loan_period;
		$obj->amount = $loan_data->loan_amount;
		$obj->date = $loan_data->record_date;
		$obj->int_method = $loan_data->int_method;
		$obj->grace_period = $loan_data->grace_period;
		$obj->period_type = $loan_data->period_type;
		$obj->frequency_type = $loan_data->frequency_type;
		$obj->frequency = $loan_data->frequency;
		$obj->grace_period_type = $loan_data->grace_period_type;
		$obj->refineSchedule = $loan_data->refineSchedule;

		$payments = $this->loanPayments($obj);

		$payment_number = 1;
		$loan_repayments = array();
		if (!empty($payments->repayments)) {
			foreach ($payments->repayments as $pay) {
				$loan_repayments['all_payments'][] =
					(object) array(
						'application_id' => $loan_data->id,
						'principal_expected' => $pay->principal,
						'interest_expected' => $pay->interest,
						'total_payment' => $pay->monthly_paid,
						'expected_date' => $pay->date,
						'payment_number' => $payment_number++,
						'brought_forward' => $pay->brought_forward,
						'begining_bal' => $pay->begining_bal,
						'company_id' => 100
					);
			}

			$loan_repayments['total_principal'] = $payments->total_principal;
			$loan_repayments['total_interest'] = $payments->total_interest;
			$loan_repayments['total_all_paid'] = $payments->total_monthly_paid;
			$loan_repayments['actual_interest_rate'] = $payments->actual_interest_rate;
		}

		return !empty($loan_repayments) ? (object) $loan_repayments : 0;
	}


	/**
	 * loanPayments method
	 */
	public function loanPayments($obj)
	{
		// --------------SAMPLE OBJECT----------
		// $obj = new stdClass();
		// $obj->int_rate =
		// $obj->period =
		// $obj->amount =
		// $obj->date =
		// $obj->int_method =
		// $obj->grace_period =
		// $obj->period_type =
		// $obj->frequency_type =
		// $obj->frequency =
		// $obj->grace_period_type =
		// --------------------END OD SAMPLE OBJECT--------------

		// --------- set default values incase they are not provided------------
		$obj->date = isset($obj->date) ? $obj->date : date('Y-m-d');
		$obj->int_method = isset($obj->int_method) ? $obj->int_method : "flat";
		$obj->grace_period = isset($obj->grace_period) ? $obj->grace_period : 0;
		$obj->period_type = isset($obj->period_type) ? $obj->period_type : "m";
		$obj->frequency_type = $obj->period_type;
		$obj->frequency = isset($obj->frequency) ? $obj->frequency : 1;
		$obj->int = $obj->int_rate;


		$obj->grace_period_type = isset($obj->grace_period_type) ? $obj->grace_period_type : "pay_i";

		// ---refine schedule such that amounts that dont reach 100, are pushed to the last installment
		$this->refineSchedule = isset($obj->refineSchedule) ? $obj->refineSchedule : $this->refineSchedule;



		if ($obj->int_method == 'flat') {
			return $this->intFlatSchedule($obj);
		} else if (in_array($obj->int_method, array("declining", "reducing"))) {
			return $this->intDecliningSchedule($obj);
		} else if ($obj->int_method == 'amortization') {
			return $this->intAmortizationSchedule($obj);
		}
	}

	public function h_add_value_to_date($loan_date, $frequency, $frequency_type)
	{
		if ($frequency_type == 'q') {
			$frequency_type = 'm';
			$frequency = $frequency / 3;
		}
		$next_date =   date('Y-m-d', strtotime($loan_date . ' + ' . $frequency . ' ' . $frequency_type));
		return $next_date;
	}

	/**
	 * intFlatSchedule method
	 * Generally this method is used to calculate the interest of something with the flat method
	 */
	private function intFlatSchedule($obj)
	{
		$response = array('repayments' => array(), 'total_principal' => 0, 'total_interest' => 0, 'total_monthly_paid' => 0, 'actual_interest_rate' => 0);

		$int = $obj->int;
		$period = $obj->period;
		$loan_amount = $obj->amount;
		$loan_date = $obj->date;
		$grace_period = $obj->grace_period;
		$period_type = $obj->period_type;
		$frequency_type = $obj->frequency_type;
		$frequency = $obj->frequency;
		$grace_period_type = $obj->grace_period_type;

		if ($period_type == 'q') {
			$period = round($period / 3);
		}

		// ---refinement variables--
		$refine_supplement_princ = 0;
		$refine_supplement_int = 0;

		$int_for_one = ($this->intForOne($period_type, $int)) / 100;
		$actual_interest_rate = $int_for_one * $period * 100;

		$full_interest = round(($loan_amount * $int_for_one) * $period);
		$full_payment = $loan_amount + $full_interest;

		$grace_paid_princ = 0;
		$grace_paid_int = 0;
		$t_principal_paid = $t_interest = $t_monthly_paid = 0;

		if ($grace_period > 0 && $grace_period < $period) {
			//compute the grace period payments
			if ($loan_amount > 0) {
				$begining_bal = $full_payment;

				$principal = $grace_period_type == 'pay_i' || $grace_period_type == 'pay_none' ? 0 : ($loan_amount / $period);
				$interest = $grace_period_type == 'pay_p' || $grace_period_type == 'pay_none' ? 0 : ($loan_amount * $int_for_one);

				for ($i = 0; $i < $grace_period; $i++) {
					// ---refine principle---
					$refinePrincipal = $this->refineAmount($principal);
					$principal = $refinePrincipal->amount;
					$refine_supplement_princ += $refinePrincipal->supplement;
					// ---refine interest---
					$refineInterest = $this->refineAmount($interest);
					$interest = $refineInterest->amount;
					$refine_supplement_int += $refineInterest->supplement;

					//generate next date
					$next_date = $this->h_add_value_to_date($loan_date, $frequency, $frequency_type);
					$monthly_paid = $principal + $interest;

					$brought_f = $begining_bal - $monthly_paid;

					$response['repayments'][] = (object) array(
						'date' => $next_date,
						'principal' => $principal,
						'monthly_paid' => $monthly_paid,
						'interest' => $interest,
						'brought_forward' => $brought_f,
						'begining_bal' => $begining_bal,
					);

					$grace_paid_princ += $principal;
					$grace_paid_int += $interest;

					$begining_bal = $brought_f;
					$loan_date = $next_date;

					$t_principal_paid += $principal;
					$t_interest += $interest;
					$t_monthly_paid += $monthly_paid;

					$period--;

					// ---reset principle--
					$principal = $refinePrincipal->old_amount;
					$interest = $refineInterest->old_amount;
				}
			}
		}

		if ($loan_amount > 0 && $period > 0) {
			//get balance on loan amount
			$loan_amount = $loan_amount - $grace_paid_princ;
			$full_interest = $full_interest - $grace_paid_int;

			$principal = $loan_amount / $period;
			$interest = $full_interest / $period;

			$full_payment = $loan_amount + $full_interest;

			$loan_amount = $full_payment;
			//------------------------------------

			for ($i = 1; $i <= $period; $i++) {
				$refinePrincipal = (object) array('old_amount' => $principal);
				$refineInterest = (object) array('old_amount' => $interest);

				if ($i == $period) {
					$principal += $refine_supplement_princ;
					$interest += $refine_supplement_int;
				} else {
					// ---refine principle---
					$refinePrincipal = $this->refineAmount($principal);
					$principal = $refinePrincipal->amount;
					$refine_supplement_princ += $refinePrincipal->supplement;
					// ---refine interest---
					$refineInterest = $this->refineAmount($interest);
					$interest = $refineInterest->amount;
					$refine_supplement_int += $refineInterest->supplement;
				}

				// ---we remove any decimal inprecisions,that have come due to divisions
				if ($i == $period) {
					$t_principal_paid = round($t_principal_paid);
					$principal = $obj->amount - $t_principal_paid;
					$interest = ($full_interest + $grace_paid_int) - $t_interest;

					$loan_amount = $principal + $interest;
				}

				//generate next date
				$next_date = $this->h_add_value_to_date($loan_date, $frequency, $frequency_type);
				$monthly_paid = $principal + $interest;

				//this adds begining balance to have principal + interest
				//----------------changes by jean----------------------------
				//$brought_f = $loan_amount - $principal;
				$brought_f = $loan_amount - $monthly_paid;
				//--------------------------------------------

				$response['repayments'][] = (object) array(
					'date' => $next_date,
					'principal' => $principal,
					'monthly_paid' => $monthly_paid,
					'interest' => $interest,
					'brought_forward' => $brought_f,
					'begining_bal' => $loan_amount,
				);

				$loan_amount = $brought_f;
				$loan_date = $next_date;

				$t_principal_paid += $principal;
				$t_interest += $interest;
				$t_monthly_paid += $monthly_paid;

				// ---reset principle--
				$principal = $refinePrincipal->old_amount;
				$interest = $refineInterest->old_amount;
			}

			$response['total_principal'] = $t_principal_paid;
			$response['total_interest'] = $t_interest;
			$response['total_monthly_paid'] = $t_monthly_paid;
			$response['actual_interest_rate'] = $actual_interest_rate;

			$response = $this->balanceOffRepayments($response, $obj->amount);
		}

		return (object) $response;
	}

	/**
	 * intDecliningSchedule method
	 * Generally this method is used to calculate the interest of something with the declining method
	 */
	private function intDecliningSchedule($obj)
	{
		$response = array('repayments' => array(), 'total_principal' => 0, 'total_interest' => 0, 'total_monthly_paid' => 0, 'actual_interest_rate' => 0);

		$int = $obj->int;
		$period = $obj->period;
		$loan_amount = $obj->amount;
		$loan_date = $obj->date;
		$grace_period = $obj->grace_period;
		$period_type = $obj->period_type;
		$frequency_type = $obj->frequency_type;
		$frequency = $obj->frequency;
		$grace_period_type = $obj->grace_period_type;
		if ($period_type == 'q') {
			$period = round($period / 3);
		}
		// ---refinement variables--
		$refine_supplement_princ = 0;
		$refine_supplement_int = 0;

		$out_standing_principal = $loan_amount;

		$int_for_one = ($this->intForOne($period_type, $int)) / 100;
		$actual_interest_rate = $int_for_one * $period * 100;

		//get total int
		$obj->int_for_one = $int_for_one;
		$full_interest = $this->decliningTotalInt($obj);
		$begining_bal = $loan_amount + $full_interest;

		$grace_paid_princ = 0;
		$grace_paid_int = 0;
		$skipped_interest = 0;
		$skipped_principle = 0;

		$t_principal_paid = $t_interest = $t_monthly_paid = 0;
		$monthly_principle = $loan_amount / $period;

		if ($grace_period > 0 && $grace_period < $period) {
			//compute the grace period payments
			if ($loan_amount > 0) {
				for ($i = 0; $i < $grace_period; $i++) {
					//generate next date
					$next_date = $this->h_add_value_to_date($loan_date, $frequency, $frequency_type);
					$interest = $out_standing_principal * $int_for_one;
					$principal = $monthly_principle;

					if ($grace_period_type == 'pay_p' || $grace_period_type == 'pay_none') {
						$skipped_interest += $interest;
						$interest = 0;
					}

					if ($grace_period_type == 'pay_i' || $grace_period_type == 'pay_none') {
						$skipped_principle += $principal;
						$principal = 0;
					}

					// ---refine principle---
					$refinePrincipal = $this->refineAmount($principal);
					$principal = $refinePrincipal->amount;
					$refine_supplement_princ += $refinePrincipal->supplement;
					// ---refine interest---
					$refineInterest = $this->refineAmount($interest);
					$interest = $refineInterest->amount;
					$refine_supplement_int += $refineInterest->supplement;

					$monthly_paid = $principal + $interest;

					//this adds begining balance to have principal + interest
					//----------------changes by jean----------------------------
					//$brought_f = $loan_amount - $principal;
					$brought_f = $begining_bal - $monthly_paid;
					//--------------------------------------------

					$response['repayments'][] = (object) array(
						'date' => $next_date,
						'principal' => $principal,
						'monthly_paid' => $monthly_paid,
						'interest' => $interest,
						'brought_forward' => abs($brought_f),
						'begining_bal' => $begining_bal,
					);

					$begining_bal = $brought_f;
					$loan_date = $next_date;

					$grace_paid_princ += $principal;
					$grace_paid_int += $interest;

					$out_standing_principal -= $monthly_principle;

					$t_principal_paid += $principal;
					$t_interest += $interest;
					$t_monthly_paid += $monthly_paid;

					$period--;
				}
			}
		}

		//get curried_forward_princ_portions
		$curried_forward_princ_portion = $skipped_principle / $period;
		$curried_forward_int_portion = $skipped_interest / $period;

		if ($loan_amount > 0 && $period > 0) {
			for ($i = 1; $i <= $period; $i++) {
				//generate next date
				$next_date = $this->h_add_value_to_date($loan_date, $frequency, $frequency_type);
				$interest = ($out_standing_principal * $int_for_one) + $curried_forward_int_portion;
				$principal = ($monthly_principle + $curried_forward_princ_portion);

				$refinePrincipal = (object) array('old_amount' => $principal);
				$refineInterest = (object) array('old_amount' => $interest);

				if ($i == $period) {
					$principal += $refine_supplement_princ;
					$interest += $refine_supplement_int;
				} else {
					// ---refine principle---
					$refinePrincipal = $this->refineAmount($principal);
					$principal = $refinePrincipal->amount;
					$refine_supplement_princ += $refinePrincipal->supplement;
					// ---refine interest---
					$refineInterest = $this->refineAmount($interest);
					$interest = $refineInterest->amount;
					$refine_supplement_int += $refineInterest->supplement;
				}

				// ---we remove any decimal inprecisions,that have come due to divisions
				if ($i == $period) {
					$t_principal_paid = round($t_principal_paid);
					$principal = $obj->amount - $t_principal_paid;
					$interest = $full_interest - $t_interest;

					$begining_bal = $principal + $interest;
				}

				$monthly_paid = $principal + $interest;

				//this adds begining balance to have principal + interest
				//----------------changes by jean----------------------------
				//$brought_f = $loan_amount - $principal;
				$brought_f = $begining_bal - $monthly_paid;
				//--------------------------------------------

				$response['repayments'][] = (object) array(
					'date' => $next_date,
					'principal' => $principal,
					'monthly_paid' => $monthly_paid,
					'interest' => $interest,
					'brought_forward' => $brought_f,
					'begining_bal' => $begining_bal,
				);

				$begining_bal = $brought_f;
				$loan_date = $next_date;

				$out_standing_principal -= $monthly_principle;

				$t_principal_paid += $principal;
				$t_interest += $interest;
				$t_monthly_paid += $monthly_paid;
			}

			$response['total_principal'] = $t_principal_paid;
			$response['total_interest'] = $t_interest;
			$response['total_monthly_paid'] = $t_monthly_paid;
			$response['actual_interest_rate'] = $actual_interest_rate;

			$response = $this->balanceOffRepayments($response, $obj->amount);
		}

		return (object) $response;
	}

	/**
	 * intAmortizationSchedule method
	 */
	private function intAmortizationSchedule($obj)
	{
		$response = array('repayments' => array(), 'total_principal' => 0, 'total_interest' => 0, 'total_monthly_paid' => 0, 'actual_interest_rate' => 0);

		$int = $obj->int;
		$period = $obj->period;
		$loan_amount = $obj->amount;
		$loan_date = $obj->date;
		$grace_period = $obj->grace_period;
		$period_type = $obj->period_type;
		$frequency_type = $obj->frequency_type;
		$frequency = $obj->frequency;
		$grace_period_type = $obj->grace_period_type;

		if ($period_type == 'q') {
			$period = round($period / 3);
		}

		$int_for_one = ($this->intForOne($period_type, $int)) / 100;
		$actual_interest_rate = $int_for_one * $period * 100;

		$obj->int_for_one = $int_for_one;
		$full_interest = $this->amotizationTotalInt($obj);

		if ($loan_amount > 0 && $period > 0) {
			//calculate the discount factor - i dont know what it is i just know that it is like that
			// $discount_f = (pow((1+$int_for_one), $period)-1)/($int_for_one*pow((1+$int_for_one),$period));
			//---------------------------------------------------------------------

			//calculate the discount factor - i dont know what it is i just know that it is like that
			$portion1 = (pow((1 + $int_for_one), $period) - 1);
			$portion2 = ($int_for_one * pow((1 + $int_for_one), $period));
			$discount_f = $portion1 > 0 && $portion2 > 0 ? ($portion1 / $portion2) : $period;
			//---------------------------------------------------------------------
			//
			$monthly_paid = $loan_amount / $discount_f;
			$out_standing_principal = $loan_amount;

			$t_principal_paid = $t_interest = $t_monthly_paid = 0;
			for ($i = 0; $i < $period; $i++) {
				//generate next date
				$next_date = $this->h_add_value_to_date($loan_date, $frequency, $frequency_type);
				$interest = $int_for_one * $out_standing_principal;
				$principal = $monthly_paid - $interest;

				// ---we remove any decimal inprecisions,that have come due to divisions
				if ($i == $period) {
					$t_principal_paid = round($t_principal_paid);
					$principal = $obj->amount - $t_principal_paid;
					$interest = $full_interest - $t_interest;

					$begining_bal = $principal + $interest;
				}

				$brought_f = $out_standing_principal - $principal;
				//--------------------------------------------

				$begining_bal = $out_standing_principal;

				$response['repayments'][] = (object) array(
					'date' => $next_date,
					'principal' => $principal,
					'monthly_paid' => $monthly_paid,
					'interest' => $interest,
					'brought_forward' => $brought_f,
					'begining_bal' => $loan_amount,
				);

				$loan_amount = $brought_f;
				$loan_date = $next_date;

				$out_standing_principal -= $principal;

				$t_principal_paid += $principal;
				$t_interest += $interest;
				$t_monthly_paid += $monthly_paid;
			}

			$response['total_principal'] = $t_principal_paid;
			$response['total_interest'] = $t_interest;
			$response['total_monthly_paid'] = $t_monthly_paid;
			$response['actual_interest_rate'] = $actual_interest_rate;

			$response = $this->balanceOffRepayments($response, $obj->amount);
		}

		return (object) $response;
	}

	/**
	 * decliningTotalInt method
	 */
	private function decliningTotalInt($obj)
	{
		$t_interest = 0;
		if ($obj->amount > 0 && $obj->period > 0) {
			//declining balance
			$principal = $obj->amount / $obj->period;
			$out_standing_principal = $obj->amount;

			for ($i = 0; $i < $obj->period; $i++) {
				$interest = $out_standing_principal * $obj->int_for_one;
				$t_interest += $interest;
				$out_standing_principal -= $principal;
			}

			return $t_interest;
		}
	}

	/**
	 * amotizationTotalInt method
	 */
	private function amotizationTotalInt($obj)
	{
		$t_interest = 0;
		if ($obj->amount > 0 && $obj->period > 0) {
			$int_for_one = $obj->int_for_one;
			$period = $obj->period;
			$loan_amount = $obj->amount;

			//calculate the discount factor - i dont know what it is i just know that it is like that
			$portion1 = (pow((1 + $int_for_one), $period) - 1);
			$portion2 = ($int_for_one * pow((1 + $int_for_one), $period));
			$discount_f = $portion1 > 0 && $portion2 > 0 ? ($portion1 / $portion2) : $period;
			//---------------------------------------------------------------------
			//
			$monthly_paid = $loan_amount / $discount_f;
			$out_standing_principal = $loan_amount;

			$t_principal_paid = $t_interest = $t_monthly_paid = 0;
			for ($i = 0; $i < $period; $i++) {
				$interest = $int_for_one * $out_standing_principal;
				$out_standing_principal -= ($monthly_paid - $interest);

				$t_interest += $interest;
			}
			return $t_interest;
		}
		return $t_interest;
	}

	/**
	 * intForOne method
	 * this method gets the int percentage of a loan of flat balance by providing the expected interest and loan amount an period , then it will give you the int age
	 */
	public function intForOne($period_type, $int)
	{
		switch ($period_type) {
			case 'w':
				$int_for_one = ($int / 12) / 4;
				break;

			case 'd':
				$int_for_one = ($int / 12) / 30;
				break;

			case 'y':
				$int_for_one = $int;
				break;

			case 'q':
				$int_for_one = $int / 4;
				break;

			default:
				$int_for_one = $int / 12;
				break;
		}

		return ($int_for_one);
	}

	/**
	 * refineAmount method
	 * this method makes a digit friendly by removing partial figures and returns a decent wholly payable amount eg: 8,333 becomes 8,300
	 */
	public function refineAmount($amount)
	{
		if (!$this->refineSchedule)
			return (object) array('old_amount' => $amount, 'amount' => $amount, 'supplement' => 0);

		if ($amount > 100) {
			$refine = (int) ($amount / 100) * 100;
			$supplement = $amount - $refine;
		} else if ($amount < 100 && $amount > 50) {
			$refine = (int) ($amount / 50) * 50;
			$supplement = $amount - $refine;
		} else {
			$refine = $amount;
			$supplement = 0;
		}

		return (object) array('old_amount' => $amount, 'amount' => $refine, 'supplement' => $supplement);
	}

	/**
	 * balanceOffRepayments method
	 */
	public function balanceOffRepayments($response, $loan_amount)
	{
		// ------balance off repayments--------
		$total_principal = $total_interest = 0;
		$repayments = array();
		if (!empty($response['repayments'])) {
			foreach ($response['repayments'] as $r) {
				$total_principal += $r->principal;
				$total_interest += $r->interest;

				if ($total_principal > $loan_amount)
					$r->principal = $loan_amount - ($total_principal - $r->principal);

				$r->monthly_paid = $r->interest + $r->principal;

				$repayments[] = $r;
			}
		}

		$response['repayments'] = $repayments;
		$response['total_interest'] = $total_interest;
		$response['total_principal'] = $loan_amount;
		$response['total_monthly_paid'] = $response['total_principal'] + $response['total_interest'];

		return $response;
	}
}
