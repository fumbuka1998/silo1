<?php
class Tax_table_item extends MY_Model{

    const DB_TABLE = 'tax_table_items';
    const DB_TABLE_PK = 'id';

    public $minimum;
    public $maximum;
    public $rate;
    public $additional_amount;
    public $tax_table_id;


    public function tax_items(){
        $tax_items = $this->get();
        return $tax_items;
    }

    public function employee_taxable_salary_taxtable_details($taxable_amount, $tax_table_id)
    {
      $tax_table_groups = $this->get(0,0, ['tax_table_id' => $tax_table_id]);

        foreach ($tax_table_groups as $tax_table_group) {

            if($tax_table_group->maximum != 0){
                $condition = ($tax_table_group->minimum <= $taxable_amount) && ($taxable_amount <= $tax_table_group->maximum);
            }else{
                $condition = ($tax_table_group->minimum <= $taxable_amount);
            }

            if ($condition) {
              $data = [
                  'taxable_amount' => $taxable_amount,
                  'minimum_group_taxable_amount' => $tax_table_group->minimum - 1,
                  'group_rate' => $tax_table_group->rate,
                  'group_additional_amount' => $tax_table_group->additional_amount
              ];

              return $data;
            }
      }
    }


}