<?php
/*
* File name: ChartOfAccountsModel.php (model)
* Author : yasira muganga
* Website : yasiratech.com
* Email : admin@yasiratech.com
* Telephone: (+256) 707 251 554, (+256) 779 028 980
* Date: 03-Mar-2023 11:28 am
* Description: This controller manages all the accounting menu item
*/

class ChartOfAccountsModel
{

    public $today;
    public $bankid;
    private $conn;
	public $account_name;
	public $acc_descri;
	public function __construct($db)
	{
$this->conn = $db;
		$this->today = date('d-m-y h:i:s');
	}

	// --------------------------------------------------------------------

	// --------------------------------------------------------------------

	/**
	 * get_general_ledger_accounts method
	 *
	 * Generally this method gets all the general ledgr accounts in the system_gl_accounts table
	 *
	 */

	public function get_general_ledger_accounts()// used 1
	{

        $sqlQueryn = 'SELECT * FROM public."system_gl_accounts" WHERE deletion_status=0 AND status=:st ';
      $stmtn = $this->conn->prepare($sqlQueryn);
      $st = 'active';
      $stmtn->bindParam(':st', $st);
    //   $stmtn->bindParam(':bid', $this->bankid);

      $stmtn->execute();
      if($stmtn->rowCount()>0){
        return $stmtn;
      }
		return  0;

	}

	// --------------------------------------------------------------------

	/**
	 * get_account_details method
	 *
	 * Generally this method fetches the details of an account, it uses  the type passed in to determine if to get from the 
	 * sub account table or general ledger table
	 *
	 * @param $code the account code of an account
	 * @param $type the type of the account, (major or sub)
	 */

	public function get_account_details($code,$type)// used 2
	{
		//if the account is not a system account, we fetch from user accounts
		$result = ($type == 'sys_sub' || $type == 'sys_major') ? $this->get_system_sub_account_details($code,$type) : $this->get_user_sub_account_details($code);

		return $result;
	}
	//get user account details
	private function get_user_sub_account_details($code)// used 1
	{
        $sqlQueryn = 'SELECT * FROM public."company_sub_accounts" WHERE deletion_status=0 AND status=:st  AND id=:acode';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $st = 'active';
        $stmtn->bindParam(':st', $st);
        $stmtn->bindParam(':acode', $code);
  
        $stmtn->execute();
        if($stmtn->rowCount()>0){
          return $stmtn;
        }

return 0;
	}
	//get system account details
	private function get_system_sub_account_details($code,$type)// used 1
	{
		$table = $type == 'sys_major' ? 'system_gl_accounts' : ($type == 'sys_sub' ? 'company_sub_accounts' : '');

		if ($table == '') 
		{
			return 0;
		}
else if($table == 'company_sub_accounts'){
        $sqlQueryn = 'SELECT * FROM public."company_sub_accounts" WHERE status=:st  AND id=:acode';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $st = 'active';
        $stmtn->bindParam(':st', $st);
        $stmtn->bindParam(':acode', $code);
  
        $stmtn->execute();
        if($stmtn->rowCount()>0){
          return $stmtn;
        }

return 0;
    }else if($table == 'system_gl_accounts'){
        $sqlQueryn = 'SELECT * FROM public."system_gl_accounts" WHERE status=:st  AND account_code=:acode ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $st = 'active';
        $stmtn->bindParam(':st', $st);
        $stmtn->bindParam(':acode', $code);
  
        $stmtn->execute();
        if($stmtn->rowCount()>0){
          return $stmtn;
        }

return 0;
    }

		return 0;
	}
	

	// --------------------------------------------------------------------

	/**
	 * save_sub_account method
	 *
	 * Generally this method handles the saving of the sub account to the db
	 *
	 * @param $p_account the parent account code
	 * @param $type the type of the account, (major or sub)
	 */

	public function save_sub_account($p_account,$type)// used 1
	{
		$acc_code = str_pad(rand(0, pow(10, 4)-1), 4, '0', STR_PAD_LEFT);

//account_code column desc - sub accounts code to be with a prefix of (sys) such that the admin can add sub accounts later with out interfering with the clients account codes
// report_level column desc - NUll or 1 are for normal detailed reporting, while 2 is if you want an account to be reported at the summary level
// numeric_value column desc - store the numeric version of the account along side the account code for easier quering, when we need the maximum code of a parent account
		// $sqlQueryn = 'INSERT INTO public.system_sub_accounts(account_name, account_code, report_level, parent_id, account_descr, bankid) VALUES (:name,:acode,:rlevel,:pid,:descr,:bid)';
        // $stmtn = $this->conn->prepare($sqlQueryn);
		// $report_level = 1;
        // $stmtn->bindParam(':name', $this->account_name);
        // $stmtn->bindParam(':acode', $acc_code);
        // $stmtn->bindParam(':rlevel', $report_level);
        // $stmtn->bindParam(':pid', $p_account);
        // $stmtn->bindParam(':descr', $this->acc_descri);
        // $stmtn->bindParam(':bid', $this->bankid);
  
        // $stmtn->execute();

if($type=='sys'){
	$acc_code = 'sys-'.$acc_code;
}
		$sqlQueryn = 'INSERT INTO public.company_sub_accounts(account_name, account_code, report_level, parent_id, account_descr, bankid) VALUES (:name,:acode,:rlevel,:pid,:descr,:bid)';
        $stmtn = $this->conn->prepare($sqlQueryn);
		$report_level = 1;
        $stmtn->bindParam(':name', $this->account_name);
        $stmtn->bindParam(':acode', $acc_code);
        $stmtn->bindParam(':rlevel', $report_level);
        $stmtn->bindParam(':pid', $p_account);
        $stmtn->bindParam(':descr', $this->acc_descri);
        $stmtn->bindParam(':bid', $this->bankid);
  
        $stmtn->execute();

		return $stmtn ? 1 : 0;
	}

	// --------------------------------------------------------------------
	
	///end//
	// public function get_branch_prefix($branch_id)
	// {
	// 	$this->db->where('company_id',h_session('company_id'));
	// 	$this->db->where('branch_id',$branch_id);

	// 	$res = $this->db->get('company_branches');

	// 	return $res->num_rows() > 0 ? $res->row() : 0;
	// }
	// --------------------------------------------------------------------

	/**
	 * save_general_ledger_changes method
	 *
	 * Generally this method updates the genral ledger account
	 *
	 */

	public function save_sub_account_changes($a_account)// used 1
	{

		$sqlQueryn = 'UPDATE public.company_sub_accounts SET account_name=:name, account_descr=:descr WHERE id=:acode';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':name', $this->account_name);
        $stmtn->bindParam(':descr', $this->acc_descri);
        $stmtn->bindParam(':acode', $a_account);
  
        $stmtn->execute();

		return $stmtn ? 1 : 0;
	}

	// --------------------------------------------------------------------

	/**
	 * get_sub_accounts method
	 *
	 * Generally this function gets all sub accounts of an account, i use also to check if an account has sub accounts
	 *
	 * @param $account_code this is the parent account code 
	 */
	public function get_sub_accounts($account_code)// used 1
	{
		$sqlQueryn = 'SELECT * FROM public."company_sub_accounts" WHERE parent_id=:acode ';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':acode', $account_code);
  
        $stmtn->execute();

		if ($stmtn) 
		{
			return $stmtn->rowCount() > 0 ? $stmtn : 0;
		}

		return 0;

		
	}


	// --------------------------------------------------------------------
	/**
	 * delete_sub_account method
	 *
	 * Generally this method deletes a sub account, it achieves it by marking it as deleted, we can't delete an account because its connected to user transactions
	 *
	 */
	public function delete_sub_account($account_code)// used 1
	{
$del  = 1;
		$sqlQueryn = 'UPDATE public.company_sub_accounts SET deletion_status=:name WHERE id=:acode';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':name', $del);
        $stmtn->bindParam(':acode', $account_code);
  
        $stmtn->execute();




		return $stmtn ? 1 : 0;
	}
	//////end/////////

	// --------------------------------------------------------------------

	/**
	 * print_sub_account_data method
	 *
	 * Generally print sub accounts in arecursive way
	 *
	 * @param $account this is the account data object being displayed
	 */
	public function print_sub_account_data($account,$special_accounts = array())// used 1
	{

		//our results will be concatenated in a string then returned
		$display_string = '';

		//decrypt code for the urls
		$encrypted_code = $account['account_code'];

		//get sub accounts of passed in account code
		$sub_accounts = $this->get_sub_accounts($account['account_code']);

		//if it has sub accounts,then we loop through them to also print then
		if (!$sub_accounts) return $display_string ;

		$display_string = '<ul>';

		//loop through the sub accounts
		foreach ($sub_accounts as $sub) 
		{

			//get and also check if sub accounts exist for this account also
			$sub_sub_accounts = $this->get_sub_accounts($sub['id']);

			//decrypt code for the urls
			$encrypted_code = $sub['id'];

			$display_string.= '<li>'.ucwords(strtolower($sub['account_name'].' : '.$sub['account_code']));

			//Account options dropdown
			$display_string.= $this->accounts_options_dropdown($sub,$encrypted_code,$special_accounts);

			//if has subs make a recursive to form tree structure,in the collapse dropdown
			if ($sub_sub_accounts) 
				$display_string.= $this->print_sub_account_data($sub,$special_accounts);

			$display_string .= '</li>';
		}

		$display_string.= '</ul>';

		return $display_string;
	}


	// --------------------------------------------------------------------

	/**
	 * printCheckBoxSubAccounts method
	 *
	 * Generally print sub accounts in arecursive way
	 *
	 * @param $account this is the account data object being displayed
	 */
	public function printCheckBoxSubAccounts($account)// used 1
	{
		//our results will be concatenated in a string then returned
		$display_string = '';

		//get sub accounts of passed in account code
		$sub_accounts = $this->get_sub_accounts($account['id']);

		//if it has sub accounts,then we loop through them to also print then
		if ($sub_accounts) 
		{
			//loop through the sub accounts
			foreach ($sub_accounts as $sub) 
			{
				//get and also check if sub accounts exist for this account also
				$sub_sub_accounts = $this->get_sub_accounts($sub['id']);

				//if has subs
				if ($sub_sub_accounts) 
				{
					//heading
					$display_string.= '<li class="ty panel-heading" style="padding: 10px 15px;"><div class="panel-title">';

					$display_string.= '<span class="collapse_heading">';

					$div_id = str_replace(' ', '',$sub['id']);

					$display_string.= '<input type="checkbox" name="accounts[]" value="'.$sub['id'].'" attr-child="1" class="parentChartAccount" div_id="'.$div_id.'"> <a role="button" data-toggle="collapse in" href="#'.$div_id.'" aria-expanded="false" aria-controls="collapseExample">
					'.$sub->account_name.' - <small> '.$sub['id'].'</small> <i class="caret"></i></a>';

					$display_string.= '</span><div class="dropdown new_dropdown"></div>'; //end of heading header

					$display_string.= '</div></li>';

					//nested data dropdown,collapse dropdown
					$display_string.= '<div class="collapse in" id="'.$div_id.'">
					<div class="well3">';

					$display_string.= '<ul>';

					//make a recursive to form tree structure,in the collapse dropdown
					$display_string.= $this->printCheckBoxSubAccounts($sub);

					$display_string.= '</ul>';

					$display_string.= '</div>';
					$display_string.= '</div>';//end of collapse dropdown
					$display_string.= '</li>';
				}
				else
				{
					//heading
					$display_string.= '<li class="ty panel-heading"><div class="panel-title"><span class=" chart-accounts-list"><input type="checkbox" name="accounts[]" value="'.$sub['account_code'].'" attr-child="0"> '.$sub['account_name'].' - <small>'.$sub['account_code'].'</small></span><div class="dropdown new_dropdown"></div>'; 
					$display_string.= '</div></li>';
				}
			}//end of for each loop

			}//if ($sub_accounts) ends

			return $display_string;
	}


	// --------------------------------------------------------------------

	/**
	 * accounts_options_dropdown method
	 *
	 * Generally this method adds account action options to an account
	 *
	 * @param $sub_data this is the account data object
	 * @param $enc_code this is the encrypted account code, its passed on the urls
	 */
	private function accounts_options_dropdown($sub_data,$enc_code,$special_accounts)
	{
		// $exclude_sp_acc = array('sys-121','sys-113','sys-212','sys-21');
		$exclude_sp_acc = array();

		$display_string = '';

		$sub_data = (object) $sub_data;

		// $type_of_account = h_encrypt_decrypt('sub');

		if ($sub_data) 
		{
			$type = strpos($sub_data['account_code'], 'sys-') !== false ? 'sys_sub' : 'user_sub';

			$display_string .= '<div class="float-right">
                                    <div class="dropdown d-inline-block">
                                    <a class="dropdown-toggle arrow-none" id="dLabel1" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"><i class="las la-ellipsis-v font-24 text-muted"> </i> 
                                    </a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel1">';


											if (in_array($sub_data['account_code'], $exclude_sp_acc) || !in_array($sub_data['account_code'], $special_accounts)) 
											{
												$display_string .= '
												<a class="dropdown-item load_via_ajax" href="add_sub_account.php?c='.$enc_code.'"><i class="ti-plus"></i> Add Sub Account</a>

												<a class="dropdown-item load_via_ajax" href="view_account_details.php?c='.$enc_code.'"><i class="ti-eye"></i> View Details</a>';

												//this go only to accounts that are user defined
												if ($type == 'user_sub') 
												{
													$display_string .= '<hr class="hr-dashed">';

													$display_string .= '<a class="dropdown-item load_via_ajax" href="edit_sub_account_details.php?c='.$enc_code.'"><i class="ti-pencil-alt"></i> Edit Account</a>
														<a class="dropdown-item ajax_delete" href="delete_sub_account.php?c='.$enc_code.'"><i class="ti-trash"></i> Trash Account</a>';
												}
											}
											else
											{
												$display_string.= " <a href='javascript:void(0);' class='dropdown-item'><i class='ti-na'></i> Actions Disabled</a>";
											}
                                        $display_string.= '</div>
                                    </div>
                                </div>';
		}

		return $display_string;
	}
	//-----------------------------MAPPING ACCOUNTS--------------
	// public function getChartAccountRoles() //used 1
	// {
	// 	$query = 'SELECT r.*,
	// 	COALESCE(mp.account_code,r.default_map_account) as account_code 
	// 	from '.h_session('system_database').'.chart_account_roles as r 
	// 	LEFT JOIN mapped_chart_accounts as mp on r.id = mp.role_id and mp.branch_id = "'.h_session('branch_id').'" and mp.company_id = '.h_session('company_id');

	// 	$res = $this->db->query($query);

	// 	return $res->num_rows() > 0 ? $res : 0;
	// }

	// public function getChartAccountRoleDetails($id) //used 1
	// {
	// 	$query = 'SELECT r.*,
	// 	COALESCE(mp.account_code,r.default_map_account) as account_code 
	// 	from '.h_session('system_database').'.chart_account_roles as r 
	// 	LEFT JOIN mapped_chart_accounts as mp on r.id = mp.role_id and mp.branch_id = "'.h_session('branch_id').'" and mp.company_id = "'.h_session('company_id').'" 
	// 	where r.id = "'.$id.'"';

	// 	$res = $this->db->query($query);

	// 	return $res->num_rows() ? $res->row() : 0;
	// }
	//-----------------------------------------------------------
	/**
	 * saveMappingAccount method
	 *
	 * Generally this method handles the saving of the mapping of accounts
	 *
	 * @param (int) $role_id
	 */

	// public function saveMappingAccount($role_id)// used 1
	// {
	// 	$selected_acc = h_post('select_account');

	// 	$data = 
	// 		array(
	// 			'role_id' => $role_id,
	// 			'account_code' => $selected_acc,
	// 			'branch_id'    => h_session('branch_id'),
	// 			'company_id'   => h_session('company_id'),
	// 			'added_by'	   => h_session('user_id'),
	// 			);
	// 	//insert data
	// 	$result = $this->db->insert("mapped_chart_accounts",$data);

	// 	return $result ? 1 : 0;
	// }

	//-----------------------------------------------------------
	/**
	 * createMappingId method
	 *
	 *
	 * @param (string) $account_code
	 */

	// public function createMappingId($account_code,$descr = 'none')
	// {
	// 	$data = 
	// 		array(
	// 			'chart_account' => $account_code,
	// 			'descr'    => $descr,
	// 			'company_id'   => h_session('company_id'),
	// 			);
	// 	//insert data
	// 	$result = $this->db->insert("chart_accounts_mapping",$data);
	// 	return $result ?  $this->db->insert_id() : 0;
	// }

	//-----------------------------------------------------------
	/**
	 * createMappingId method
	 *
	 *
	 * @param (string) $account_code
	 */

	// public function updateMappingId($mapping_id,$account_code)
	// {
	// 	$data = 
	// 		array(
	// 			'chart_account' => $account_code,
	// 			);
	// 	//update data
	// 	$this->db->where('company_id',h_session('company_id'));
	// 	$this->db->where('mapping_id',$mapping_id);
	// 	$result = $this->db->update("chart_accounts_mapping",$data);
	// 	return $result ?  $mapping_id : 0;
	// }

	//-----------------------------------------------------------
	/**
	 * deleteMappingId method
	 *
	 *
	 * @param (string) $account_code
	 */

	// public function deleteMappingId($mapping_id)
	// {
	// 	if (!$mapping_id) return 0;
		
	// 	//update data
	// 	$this->db->where('company_id',h_session('company_id'));
	// 	$this->db->where('mapping_id',$mapping_id);
	// 	$result = $this->db->delete("chart_accounts_mapping");
	// 	return $result ?  1 : 0;
	// }

	//-----------------------------------------------------------
	/**
	 * getMappingId method
	 *
	 *
	 * @param (string) $account_code
	 */

	// public function getMappingId($mapping_id)
	// {
	// 	if (!$mapping_id) return 0;

	// 	//update data
	// 	$this->db->where('company_id',h_session('company_id'));
	// 	$this->db->where('mapping_id',$mapping_id);
	// 	$res = $this->db->get("chart_accounts_mapping");
	// 	return $res->num_rows() > 0 ?  $res->row() : 0;
	// }

	//-----------------------------------------------------------
	/**
	 * toggleAccountMapping method
	 *
	 *
	 * @param (string) $account_code
	 */

	// public function toggleAccountMapping($fromAccountCode,$toAccountCode)
	// {
	// 	$data = array('chart_account'=>$toAccountCode);
	// 	//update data
	// 	$this->db->where('company_id',h_session('company_id'));
	// 	$this->db->where('chart_account',$fromAccountCode);
	// 	$res = $this->db->update("chart_accounts_mapping",$data);
	// 	return $res ?  1: 0;
	// }
}