<?php

class SystemGlAccount
{
    public $conn;
    public $account_code;
    public $income_account_code = 4;
    public $expenses_account_code = 5;
    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getByCode()
    {
        $binding_array = [];
        $query = ' SELECT * FROM public."system_gl_accounts" WHERE public."system_gl_accounts".account_code=:account_code ';
        $binding_array[':account_code'] = $this->account_code;
        $trail = $this->conn->prepare($query);
        $trail->execute($binding_array);
        return $trail->fetch(PDO::FETCH_ASSOC);
    }
}
