<form method="POST">
    <input type="hidden" name="lpid" value="<?= $details[0]['id'] ?>" />
    <div class="row">
        <div class="col-md-6">

            <div class="mb-3">
                <label class="text-label form-label required-field">Loan Product Name</label>
                <input type="text" class="form-control input-rounded" placeholder="" name="name" required value="<?= @$loan_product['name'] ?>">
            </div>
        </div>
        <div class="col-md-6">

            <div class="mb-3">
                <label class="text-label form-label required-field">Interest Rate per Annum</label>
                <input type="text" class="form-control input-rounded" placeholder="" name="intrate" value="<?= @$loan_product['interestrate'] ?>" required>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-md-6">

            <div class="mb-3">
                <label class="text-label form-label required-field">Frequency</label>
                <select class="me-sm-2 default-select form-control wide select-frequency" id="inlineFormCustomSelect2" name="freq" style="display: none;" required>
                    <option selected></option>
                    <option value="DAILY" data-frequency-singular="DAY" data-frequency-plural="DAYS" <?= @$loan_product['frequency'] == "DAILY" ? 'selected' : '' ?>>Daily</option>
                    <option value="WEEKLY" data-frequency-singular="WEEK" data-frequency-plural="WEEKS" <?= @$loan_product['frequency'] == "WEEKLY" ? 'selected' : '' ?>>Weekly</option>
                    <option value="MONTHLY" data-frequency-singular="MONTH" data-frequency-plural="MONTHS" <?= @$loan_product['frequency'] == "MONTHLY" ? 'selected' : '' ?>>Monthly</option>
                    <option value="YEARLY" data-frequency-singular="YEAR" data-frequency-plural="YEARS" <?= @$loan_product['frequency'] == "YEARLY" ? 'selected' : '' ?>>Yearly</option>
                   
                </select>
            </div>
        </div>

        <div class="col-md-6">

            <div class="mb-3">
                <label class="text-label form-label required-field">Interest Method</label>

                <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="interestMethod" style="display: none;" required>
                    <option selected></option>
                    <option value="FLAT" <?= @$loan_product['interestmethod'] == 'FLAT' ? 'selected' : '' ?>>Flat</option>
                    <option value="DECLINING_BALANCE" <?= @$loan_product['interestmethod'] == 'DECLINING_BALANCE' ? 'selected' : '' ?>>
                        Declining Balance
                    </option>

                </select>
            </div>
        </div>
    </div><br />
    <h4 class="card-title text-primary">Penalty Information - Late Payment Penalty
    </h4>
    <!-- <h6 class="card-title text-primary">
    </h6> -->
    <div class="mb-3">
        <label class="text-label form-label required-field">Enable Late Payment Penalty</label>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-check">
                <input class="form-check-input activate-sections" type="radio" name="enable_penalty" value="1" data-activate-sections="enable-penalty" id="enable_late_penalty" data-deactivate-sections="disable-penalty" required <?= @$loan_product['has_penalty'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="enable_late_penalty">
                    Yes
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-check">
                <input class="form-check-input activate-sections" type="radio" name="enable_penalty" value="0" data-activate-sections="disable-penalty" data-deactivate-sections="enable-penalty" required id="unenable_late_penalty">
                <label class="form-check-label" for="unenable_late_penalty" <?= @$loan_product && $loan_product['has_penalty'] ? '' : 'checked' ?>>
                    No
                </label>
            </div>
        </div>
    </div>
    <br />

    <div class="section-enable-penalty  <?= @$loan_product['has_penalty'] ? '' : 'hide' ?>">
        <div class="row">
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input activate-sections" type="radio" name="penalty_type" value="fixed_amount" data-activate-sections="fixed-amount" data-deactivate-sections="penalty-rate" required id="penalty_fixed_amount" <?= @$loan_product['penaltyfixedamount'] > 0 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="penalty_fixed_amount">
                        Penalty Fixed Amount
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input activate-sections" type="radio" name="penalty_type" value="rate" data-activate-sections="penalty-rate" data-deactivate-sections="fixed-amount" required id="penanlty_rate" <?= @$loan_product['penaltyinterestrate'] > 0 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="penanlty_rate">
                        Penalty Rate
                    </label>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6 section-fixed-amount hide">
                <div class="mb-3">
                    <label class="text-label form-label required-field">Penalty Fixed Amount Per <span class="frequency-singular"></span></label>
                    <input type="text" class="form-control input-rounded comma_separated" placeholder="" name="pfamount" value="<?= money_format(@$loan_product['penaltyfixedamount']); ?>" data-is-required="1">
                </div>
            </div>

            <div class="col-md-6 section-penalty-rate hide">
                <div class="mb-3">
                    <label class="text-label form-label required-field">Penalty Rate % Per <span class="frequency-singular"></span></label>
                    <input type="number" class="form-control input-rounded" placeholder="" name="prate" value="<?= money_format(@$loan_product['penaltyinterestrate'], 2); ?>" step=".01" data-is-required="1">
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="text-label form-label required-field">Calculate Penalty Based On <span class="frequency-plural"></span></label>
                    <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="penaltybased" data-is-required="1">
                        <option value="p">Principal in Arrears</option>
                        <option value="i">Interest in Arrears</option>
                        <option value="both" selected>Both Principal & Interest in Arrears</option>
                    </select>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="text-label form-label required-field">Number of Grace Period <span class="frequency-plural"></span></label>
                    <input type="number" class="form-control input-rounded" placeholder="" name="gracedays" value="0" data-is-required="1">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="text-label form-label required-field">Grace Period Type</label>
                    <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="gracetype" data-is-required="1">
                        <option value="pay_i">Pay Interest Only</option>
                        <option value="pay_p">Pay Principal Only</option>
                        <option value="pay_none" selected>Pay None (Client Pays doesn't
                            pay anything until Grace Period ends)</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="text-label form-label">Maximum Penalty <span class="frequency-plural"></span>*</label>
                    <input type="number" class="form-control input-rounded" placeholder="" name="maxdays" value="0">
                </div>
            </div>

        </div>

    </div>
    <br />


    <br />
    <h4 class="card-title text-primary">Loan Fees
    </h4>
    <div class="mb-3">
        <label class="text-label form-label required-field">Loan Fees</label>

        <select class="multi-select" multiple="multiple" name="fee[]">
            <option selected value="0">None</option>
            <?php
            foreach ($response->getAllBankFees($user[0]['bankId']) as $row) {
                echo '
                                                            <option value="' . $row['id'] . '">' . $row['name'] . '</option>
                                                            ';
            }
            ?>

        </select>
    </div>


    <br /><br /><br />

    <div class="mb-3">
        <div class="form-check custom-checkbox mb-3">
            <input type="checkbox" class="form-check-input" id="customCheckBox1" name="auto_repay" checked value="true">
            <label class="form-check-label" for="customCheckBox1">Activate Automatic
                Loan Payments</label>
            <p class="text-muted mb-3">Automatically deduct savings to pay due loans
            </p>
        </div>
    </div>
    <div class="mb-3">
        <div class="form-check custom-checkbox mb-3">
            <input type="checkbox" class="form-check-input" id="customCheckBox1" name="auto_penalty" checked value="true">
            <label class="form-check-label" for="customCheckBox1">Activate automatic
                penaly payments</label>
            <p class="text-muted mb-3">Automatically deduct savings to pay due
                penalty</p>
        </div>
    </div>
    <div class="mb-3">
        <div class="form-check custom-checkbox mb-3">
            <input type="checkbox" class="form-check-input" id="customCheckBox1" name="round_off" checked value="true">
            <label class="form-check-label" for="customCheckBox1">Round off
                installment decimals</label>
            <p class="text-muted mb-3">Round off and accumulate decimals in the loan
                schedule</p>
        </div>
    </div>



    <?php

    $accounts = $response->getSubAccounts2($_SESSION['user']['branchId'], $_SESSION['user']['bankId']);
    ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="pform" value="exist" onClick="setExist()" checked>
                <label class="form-check-label">
                    Select Existing Chart Account
                </label>
            </div>
        </div>
        <div class="col-md-6">

            <div class="form-check">
                <input class="form-check-input" type="radio" name="pform" value="create" onClick="setCreate()">
                <label class="form-check-label">
                    Create new Chart Accounts for this Product (System shall generate 3 sub-accounts for this product i.e. Principal a/c, interest income a/c, penalty income a/c )
                </label>
            </div>
        </div>
    </div>
    <br />

    <div class="mb-3" id="pc">
        <label class="text-label form-label">Select Chart Account for Loan Product Principal A/C *</label>

        <select id="oscategory" class="form-control" name="account_id" required>
            <option> Select </option>
            <?php foreach ($accounts as $account) { ?>
                <option value="<?= $account['id'] ?>">
                    <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3" id="pcn">
        <label class="text-label form-label">Select Chart Account for Interest Income A/C *</label>

        <select id="bank_acc" class="form-control" name="int_id" required>
            <option> Select </option>
            <?php foreach ($accounts as $account) { ?>
                <option value="<?= $account['id'] ?>">
                    <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                </option>
            <?php } ?>
        </select>
    </div>
    <div class="mb-3" id="pcm">
        <label class="text-label form-label">Select Chart Account for Penalty Income A/C *</label>

        <select id="payment_methods" class="form-control" name="p_id" required>
            <option> Select </option>
            <?php foreach ($accounts as $account) { ?>
                <option value="<?= $account['id'] ?>">
                    <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                </option>
            <?php } ?>
        </select>
    </div>
    <div class="mb-3" id="pc1" style="display: none;">
        <label class="text-label form-label">Select Parent Chart Account for Loan Product Principal A/C *</label>

        <select id="ocategory" class="form-control" name="parent_id" required>
            <option> Select </option>
            <?php foreach ($accounts as $account) { ?>
                <option value="<?= $account['id'] ?>">
                    <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3" id="pc2" style="display: none;">
        <label class="text-label form-label">Select Parent Chart Account for Interest Income A/C *</label>

        <select id="osector" class="form-control" name="interest_id" required>
            <option> Select </option>
            <?php foreach ($accounts as $account) { ?>
                <option value="<?= $account['id'] ?>">
                    <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3" id="pc3" style="display: none;">
        <label class="text-label form-label">Select Parent Chart Account for Penalty Income A/C *</label>

        <select id="exp_account" class="form-control" name="penalty_id" required>
            <option> Select </option>
            <?php foreach ($accounts as $account) { ?>
                <option value="<?= $account['id'] ?>">
                    <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <!-- <div class="mb-3"> -->
    <br /><br /><br />

    <button type="submit" name="submit" class="btn btn-primary">Create Loan
        Product</button>



    <!-- </div> -->

</form>