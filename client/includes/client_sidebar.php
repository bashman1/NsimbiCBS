<!--**********************************
            Sidebar start
        ***********************************-->
<div class="dlabnav">
    <div class="dlabnav-scroll">

        <ul class="metismenu" id="menu">
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Transfers</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="individual_clients.php">To Another Account</a></li>
                    <li><a href="institution_clients.php">To Account in Another SACCO</a></li>
                    <li><a href="group_clients.php">Mobile Money Transfer</a></li>
                    <li><a href="group_clients.php">To Bank Account</a></li>
                </ul>
            </li>
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-clone"></i>
                    <span class="nav-text">e-Statement</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="active_loans.php">Full A/C Statement</a></li>

                    <li><a href="loan_applications.php">Mini-Statement</a></li>
                    <li><a href="approved_loans.php">Fees Collection Statement</a></li>
                    <li><a href="declined_loans.php">Loan Statement</a></li>
                </ul>
            </li>
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-wallet"></i>
                    <span class="nav-text">Savings</span>
                </a>
                <ul aria-expanded="false">

                    <li><a href="search_client.php">Make Deposit</a></li>
                    <li><a href="withdraw_search_client.php">Enter Withdraw</a></li>
                    <li><a href="fixed_deposits.php">Fixed Deposits</a></li>
                </ul>
            </li>

            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-wallet"></i>
                    <span class="nav-text">Transaction History</span>
                </a>
                <ul aria-expanded="false">

                    <li><a href="search_client.php">Make Deposit</a></li>
                    <li><a href="withdraw_search_client.php">Enter Withdraw</a></li>
                    <li><a href="fixed_deposits.php">Fixed Deposits</a></li>
                </ul>
            </li>
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-wallet"></i>
                    <span class="nav-text">Bill Payments</span>
                </a>
                <ul aria-expanded="false">

                    <li><a href="search_client.php">Make Deposit</a></li>
                    <li><a href="withdraw_search_client.php">Enter Withdraw</a></li>
                    <li><a href="fixed_deposits.php">Fixed Deposits</a></li>
                </ul>
            </li>

            <li><a class=" " href="javascript:void()" aria-expanded="false">
                    <span class="nav-text">
                        <img src="<?php echo is_null($user[0]['photo']) ? 'images/account.png' : $user[0]['photo']; ?>" onerror="this.onerror=null; this.src='images/account.png'" alt="" height="50" width="50" class="rounded-circle" />
                    </span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="set_mpin_client.php?id=<?= $user[0]['userId'] ?>&cid=<?= $_GET['cid'] ?>">Change mPIN</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>


    </div>
</div>
<!--**********************************
            Sidebar end
        ***********************************-->