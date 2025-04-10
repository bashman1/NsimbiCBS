<form method="POST">

    <div class="row">
        <?php
        if (!$user[0]['branchId']) {
            $branches = $response->getBankBranches($user[0]['bankId']);

            echo '
                          <div class="form-group">
                         
                              <label class="text-label form-label">Branch *</label>
                              <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="branch" required>
                             
                                  ';
            if ($branches !== '') {
                foreach ($branches as $row) {
                    echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                }
            } else {
                echo '
                              <option readonly>No Branches Added yet</option>
                              ';
            }

            echo
            '
                          
                              </select>
                          </div>
                       
                          
                          ';
        } else {
            echo '

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >

                            
                            ';
        }
        ?>

        <div class="col-md-4">

            <div class="form-group">
                <label>Product Name :
                </label>
                <input id="name" type="text" value="" name="name" class="form-control">
            </div>

            <div class="form-group">
                <label>Maximum Over Draft Amount:</label>
                <input id="max_amt" type="number" value="" name="max_amt" class="form-control">
            </div>

            <div class="form-group">
                <label class="text-label form-label required-field">Period Type</label>
                <select class="form-control" id="cash_trans" name="freq" required>
                    <option selected></option>
                    <option value="DAILY">Daily</option>
                    <option value="WEEKLY">Weekly</option>
                    <option value="MONTHLY">Monthly</option>
                    <option value="YEARLY">Yearly</option>
                </select>
            </div>

            <div class="form-group">
                <label>Maximum Over Draft Period ( in <label id="dtypes"> </label>):</label>
                <input id="max_period" type="number" value="" name="max_period" class="form-control">
            </div>

            <div class="form-group account_no_insert">
                <label for="exampleInputEmail1" class="">Penalty Type: </label>

                <select id="penalty_type" name="penalty_type" class="form-control">
                    <option value="percent">Percentage</option>
                    <option value="flat">Flat</option>
                </select>
            </div>

            <div class="form-group">
                <label>Penalty <label id="ptype"> </label> ( Per <label id="dtype"> </label> ):</label>
                <input id="penalty_rate" step="any" type="number" value="" name="penalty_rate" class="form-control">
            </div>

        </div>
        <div class="col-md-4">



            <div class="form-group">
                <label>Penalty Grace Period ( in Days ):</label>
                <input id="p_grace_period" type="number" value="" name="p_grace_period" class="form-control">
            </div>

            <div class="form-group">
                <label>Penalty Income Account: </label>
                <select name="penalty_income_acc" class="form-control" id="journalacc">
                    <option value="">Select....</option>
                    <?php

                    if ($sub_accs) {

                        foreach ($sub_accs as $acc) {
                            if ($acc['type'] == 'INCOMES') {

                                echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ':   -  ' . $acc['branch'] . '  -  Balance: ' . number_format($acc['balance']) . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Interest Income Account: </label>
                <select name="interest_income_acc" class="form-control" id="reserveacc">
                    <option value="">Select....</option>
                    <?php
                    if ($sub_accs) {

                        foreach ($sub_accs as $acc) {
                            if ($acc['type'] == 'INCOMES') {

                                echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ':   -  ' . $acc['branch'] . '  -  Balance: ' . number_format($acc['balance']) . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group account_no_insert">
                <label for="exampleInputEmail1" class="">Interest / Charge Type: </label>

                <select id="charge_type" name="charge_type" class="form-control">
                    <option value="percent">Percentage</option>
                    <option value="flat">Fixed Amount</option>
                </select>
            </div>

            <div class="form-group">
                <label>Interest / Charge <label id="itype"> </label> ( Per <label id="dtype"> </label> ):</label>
                <input id="charge" step="any" type="number" value="" name="charge" class="form-control">
            </div>

            <div class="form-group">
                <div class="form-group">
                    <label>Withdraw Allowance period (in Days)</label>
                    <input id="withdraw_allowance_period" type="text" step="any" value="" name="withdraw_allowance_period" class="form-control" required="">
                </div>
            </div>

        </div>
        <div class="col-md-4">


            <button type="submit" name="submit" class="btn btn-primary">Save</button>
        </div>

    </div>

</form>