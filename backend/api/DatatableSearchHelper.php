<?php
class DatatableSearchHelper
{
    public $search_string;
    public $binding_array;
    public $search_string_array;
    public $query;

    public function __construct()
    {
        $this->search_string = "";
        $this->query = "";
        $this->binding_array = [];
        $this->search_string_array = [];
    }

    public function search_client($search_string)
    {
        $this->search_string = trim($search_string);
        $this->query = "";
        /**
         * Split search string if found
         */
        if ($this->search_string) {
            $this->search_string_array = StringToArray($this->search_string);
        }


        if ($search_string) {
            $this->query .= ' AND (';
            foreach ($this->search_string_array as $_index => $string) {

                // $this->query .= ' (public."Bank".id=:bank_id AND public."User".id ILIKE :user_id' . $_index . ') ';
                // $this->binding_array[':user_id' . $_index] = '%' . @$string . '%';

                $this->query .= ' (public."Bank".id=:bank_id AND public."User"."firstName" ILIKE :first_name_' . $_index . ') ';
                $this->binding_array[':first_name_' . $_index] = '%' . @$string . '%';

                $this->query .= ' OR (public."Bank".id=:bank_id AND public."User"."lastName" ILIKE :last_name_' . $_index . ') ';
                $this->binding_array[':last_name_' . $_index] = '%' . @$string . '%';

                $this->query .= ' OR (public."User".shared_name ILIKE :shared_name_' . $_index . ') ';
                $this->binding_array[':shared_name_' . $_index] = '%' . @$string . '%';

                $this->query .= ' OR (public."Bank".id=:bank_id AND public."Client".membership_no ILIKE :membership_no' . $_index . ') ';
                $this->binding_array[':membership_no' . $_index] = '%' . @$string . '%';

                $this->query .= ' OR (public."Bank".id=:bank_id AND public."User"."primaryCellPhone" ILIKE :primary_phone_number' . $_index . ') ';
                $this->binding_array[':primary_phone_number' . $_index] = '%' . @$string . '%';

                $this->query .= ' OR (public."Bank".id=:bank_id AND public."User"."secondaryCellPhone" ILIKE :secondary_phone_number' . $_index . ') ';
                $this->binding_array[':secondary_phone_number' . $_index] = '%' . @$string . '%';

                if ($_index < count($this->search_string_array) - 1) {
                    $this->query .= ' OR ';
                }
                $_index++;
            }
            $this->query .= ')';
        }

        return array('query' => $this->query, 'binding_array' => $this->binding_array);

        // if ($search_string) {
        //     /**
        //      * if we have a search string and seach string has more than one substring with spaces
        //      */
        //     if (count($search_string_array) > 1) {

        //         $sqlQuery .= ' AND (
        //             (public."Bank".id=:bank_id AND public."User"."firstName" ILIKE :first_name_1)
        //             OR (public."Bank".id=:bank_id AND public."User"."firstName" ILIKE :first_name_2)
        //             OR (public."Bank".id=:bank_id AND public."User"."lastName" ILIKE :last_name_1)
        //             OR (public."Bank".id=:bank_id AND public."User"."lastName" ILIKE :last_name_2)
        //         )';

        //         $binding_array[':last_name_1'] = '%' . @$search_string_array[0] . '%';
        //         $binding_array[':first_name_2'] = '%' . @$search_string_array[1] . '%';
        //         $binding_array[':first_name_1'] = '%' . @$search_string_array[0] . '%';
        //         $binding_array[':last_name_2'] = '%' . @$search_string_array[1] . '%';
        //     } else {

        //         $sqlQuery .= ' AND (
        //             (public."Bank".id=:bank_id AND public."User"."firstName" ILIKE :first_name)
        //             OR (public."Bank".id=:bank_id AND public."User"."lastName" ILIKE :last_name)
        //             OR (public."Bank".id=:bank_id AND public."Client".membership_no ILIKE :membership_no)
        //             OR (public."Bank".id=:bank_id AND public."User"."primaryCellPhone" ILIKE :primary_phone_number)
        //             OR (public."Bank".id=:bank_id AND public."User"."secondaryCellPhone" ILIKE :secondary_phone_number)
        //         )';
        //         $binding_array[':first_name'] = '%' . $search_string . '%';
        //         $binding_array[':last_name'] = '%' . $search_string . '%';
        //         $binding_array[':membership_no'] = '%' . $search_string . '%';
        //         $binding_array[':primary_phone_number'] = '%' . $search_string . '%';
        //         $binding_array[':secondary_phone_number'] = '%' . $search_string . '%';
        //     }
        // }

        // return array('query' => $sqlQuery, 'binding_array' => $binding_array);
    }

    public function search_collateral()
    {
        $this->query = "";
        /**
         * Split search string if found
         */
        if ($this->search_string) {
            $this->search_string_array = StringToArray($this->search_string);
        }

        if ($this->search_string) {

            $this->query .= ' AND (';
            foreach ($this->search_string_array as $_index => $string) {
                $this->query .= ' (public."collateral_categories".bankid=:bank_id AND public."collaterals"._collateral ILIKE :_collateral_' . $_index . ') ';
                $this->binding_array[':_collateral_' . $_index] = '%' . @$string . '%';

                $this->query .= ' OR (public."collateral_categories".bankid=:bank_id AND public."collaterals"._location ILIKE :_location' . $_index . ') ';
                $this->binding_array[':_location' . $_index] = '%' . @$string . '%';

                $this->query .= ' OR (public."collateral_categories".bankid=:bank_id AND public."collateral_categories"._catname ILIKE :_catname' . $_index . ') ';
                $this->binding_array[':_catname' . $_index] = '%' . @$string . '%';

                if ($_index < count($this->search_string_array) - 1) {
                    $this->query .= ' OR ';
                }
                $_index++;
            }
            $this->query .= ')';
        }

        return array('query' => $this->query, 'binding_array' => $this->binding_array);
    }
}
