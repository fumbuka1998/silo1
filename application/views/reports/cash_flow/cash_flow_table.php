
<?php
$grand_total_material_budget = 0;
$grand_total_labour_budget = 0;
$grand_total_other_budget = 0;
$grand_total_expected_income = 0;
$grand_total_actual_income = 0;
$grand_total_orders_commitments = 0;
$grand_total_sub_contract_commitments = 0;
$grand_total_commitments = 0;
$grand_total_other_commitments = 0;
$grand_total_expenditures = 0;
$grand_total_variance = 0;

    foreach ($currencies as $currency) {


        if(isset($table_data[$currency->{$currency::DB_TABLE_PK}])) {
            $table_items = $table_data[$currency->{$currency::DB_TABLE_PK}];
            $rate_to_native = $currency->rate_to_native();
            ?>
            <br/>
            <h3><?= $currency->currency_name ?></h3>
            <b>Rate To TSH: </b><?= $rate_to_native ?>
            <table
                <?php if ($print) {
                    ?> width="100%" border="1" cellspacing="0"

                    <?php
                } else {
                    ?>
                    class="table table-bordered table-hover table-striped"
                    <?php
                } ?> style="font-size: 12px" >

                <thead>
                <tr>
                    <th width="8%" rowspan="2">Project Name</th>
                    <th width="27%" colspan="3" style="text-align: center">Budget</th>
                    <th width="16%" colspan="2" style="text-align: center;">Income</th>
                    <th width="32%" colspan="4" style="text-align: center;">Commitments</th>
                    <th width="8%" rowspan="2">Expenditures</th>
                    <th width="10%" rowspan="2">Variance</th>
                </tr>
                <tr>
                    <th>Material</th>
                    <th>Labour</th>
                    <th>Total</th>
                    <th>Expected</th>
                    <th>Actual</th>
                    <th>Orders</th>
                    <th>Sub Contracts</th>
                    <th>Others</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $total_income = $total_expenditure = $total_material_budget = $total_labour_budget =
                    $total_expected_income = $total_paid_income = $total_orders_commitments = $total_other_commitments = $total_sub_contracts_commitments = 0;

                foreach ($table_items as $table_item){
                    $total_budget = $table_item['material_budget'] + $table_item['labour_budget'];
                    $total_sub_contracts_commitments += $table_item['sub_contracts_commitments'];
                    $total_orders_commitments += $table_item['purchase_orders_commitments'];
                    $total_commitments = $table_item['purchase_orders_commitments'] + $table_item['sub_contracts_commitments'] + $table_item['other_commitments_amount'];
                    $total_other_commitments += $table_item['other_commitments_amount'];
                    $total_expected_income += $table_item['expected_income'];
                    $total_paid_income += $table_item['paid_amount'];
                    $total_expenditure += $table_item['project_total_expenses'];
                    $total_material_budget += $table_item['material_budget'];
                    $total_labour_budget += $table_item['labour_budget'];
                    ?>
                    <tr>
                        <td width="8%"><?= $print ? $table_item['project_name'] : anchor(base_url('projects/profile/' . $table_item['project_id']), $table_item['project_name'], ' target="_blank" ') ?></td>
                        <td width="9%" nowrap="nowrap" style="text-align: right"><?= $currency->symbol .' '.number_format($table_item['material_budget']) ?></td>
                        <td width="9%" nowrap="nowrap" style="text-align: right"><?= $currency->symbol .' '.number_format($table_item['labour_budget']) ?></td>
                        <td width="9%" nowrap="nowrap" style="text-align: right"><?= $currency->symbol .' '.number_format($total_budget) ?></td>
                        <td width="8%" nowrap="nowrap" style="text-align: right"><?= $currency->symbol .' '.number_format($table_item['expected_income']) ?></td>
                        <td width="8%" nowrap="nowrap" style="text-align: right"><?= $currency->symbol .' '.number_format($table_item['paid_amount']) ?></td>
                        <td width="8%" nowrap="nowrap" style="text-align: right"><?= $print ? $currency->symbol .' '.number_format($table_item['purchase_orders_commitments']) :  $table_item['orders_pop_up']  ?></td>
                        <td width="8%" nowrap="nowrap" style="text-align: right"><?= $print ? $currency->symbol .' '.number_format($table_item['sub_contracts_commitments']) : $table_item['sub_contract_pop_up'] ?></td>
                        <td width="8%" nowrap="nowrap" style="text-align: right"><?= $print ? $currency->symbol .' '.number_format($table_item['other_commitments_amount']) : $table_item['other_commitments_pop_up'] ?></td>
                        <td width="8%" nowrap="nowrap" style="text-align: right"><?= $currency->symbol .' '.number_format($total_commitments) ?></td>
                        <td width="8%" nowrap="nowrap" style="text-align: right"><?= $print ? $currency->symbol .' '.number_format($table_item['project_total_expenses']) : $table_item['payments_pop_up'] ?></td>
                        <td width="10%" nowrap="nowrap" style="text-align: right"><?= $currency->symbol .' '.number_format(($total_budget - $table_item['project_total_expenses']) - $table_item['purchase_orders_commitments']) ?></td>
                    </tr>
                    <?php
                }


             ?>
                </tbody>
                <tfoot>
                <tr>
                    <td nowrap="nowrap" style="font-weight: bold; text-align: left">TOTAL</td>
                    <td nowrap="nowrap" style="font-weight: bold; text-align: right"><?= $currency->symbol.' ' . number_format($total_material_budget) ?></td>
                    <td nowrap="nowrap" style="font-weight: bold; text-align: right"><?= $currency->symbol.' ' . number_format($total_labour_budget) ?></td>
                    <td nowrap="nowrap" style="font-weight: bold; text-align: right"><?= $currency->symbol.' ' . number_format(($total_labour_budget+$total_material_budget)) ?></td>
                    <td nowrap="nowrap" style="font-weight: bold; text-align: right"><?= $currency->symbol.' ' . number_format($total_expected_income) ?></td>
                    <td nowrap="nowrap" style="font-weight: bold; text-align: right"><?= $currency->symbol.' ' . number_format($total_paid_income) ?></td>
                    <td nowrap="nowrap" style="font-weight: bold; text-align: right"><?= $currency->symbol.' ' . number_format($total_orders_commitments) ?></td>
                    <td nowrap="nowrap" style="font-weight: bold; text-align: right"><?= $currency->symbol.' ' . number_format($total_sub_contracts_commitments) ?></td>
                    <td nowrap="nowrap" style="font-weight: bold; text-align: right"><?= $currency->symbol.' ' . number_format($total_other_commitments) ?></td>
                    <td nowrap="nowrap" style="font-weight: bold; text-align: right"><?= $currency->symbol.' ' . number_format(($total_orders_commitments+$total_sub_contracts_commitments+$total_other_commitments)) ?></td>
                    <td nowrap="nowrap" style="font-weight: bold; text-align: right"><?= $currency->symbol.' ' . number_format($total_expenditure) ?></td>
                    <td nowrap="nowrap" style="font-weight: bold; text-align: right"><?= $currency->symbol.' ' . number_format(($total_labour_budget+$total_material_budget-$total_expenditure)) ?></td>
                </tr>

                <?php
                  $grand_total_material_budget += $total_material_budget*$rate_to_native;
                  $grand_total_labour_budget += $total_labour_budget*$rate_to_native;
                  $grand_total_other_budget += ($total_labour_budget+$total_material_budget)*$rate_to_native;
                  $grand_total_expected_income += $total_expected_income*$rate_to_native;
                  $grand_total_actual_income += $total_paid_income*$rate_to_native;
                  $grand_total_orders_commitments += $total_orders_commitments*$rate_to_native;
                  $grand_total_sub_contract_commitments += $total_sub_contracts_commitments*$rate_to_native;
                  $grand_total_other_commitments += $total_other_commitments*$rate_to_native;
                  $grand_total_commitments += ($total_other_commitments+$total_sub_contracts_commitments+$total_orders_commitments)*$rate_to_native;
                  $grand_total_expenditures += $total_expenditure*$rate_to_native;
                  $grand_total_variance += ($total_labour_budget+$total_material_budget-$total_expenditure-$total_orders_commitments)*$rate_to_native;
                ?>

                </tfoot>
            </table>

            <?php
        }

    }

  if($payroll_data){
      $grand_total = 0;
      ?>
      <h3>Payroll Expenses</h3>
      <b>Rate To TSH: </b>1.00

      <table
          <?php if ($print) {
              ?> width="100%" border="1" cellspacing="0"

              <?php
          } else {
              ?>
              class="table table-bordered table-hover table-striped"
              <?php
          } ?> style="font-size: 12px" >

          <thead>
          <tr>
              <th>Descriptions</th>
              <?php

                foreach ($payroll_data as $payroll){
                    ?>
                    <th style="text-align: center"><?php
                        if(!$print){
                            ?>
                            <form method="post" target="_blank" action="<?= base_url('human_resource/human_resources/payroll_list_display') ?>">
                                <input type="hidden" name="payroll_date" value="<?= $payroll['payroll_for'].'-1' ?>">
                                <input type="hidden" name="department_id" value="<?= $payroll['department_id'] ?>">
                                <input type="hidden" name="print" value="true">
                                <input type="hidden" name="payroll_id" value="<?= $payroll['payroll_id'] ?>">
                                <button style="color: #458bb9; font-weight: bold" data-toggle="modal" data-target="#print<?= $payroll['payroll_id'] ?>" class="btn btn-basic btn-xs">
                                    <?= strtoupper(DateTime::createFromFormat('!m',
                                        date('m', strtotime($payroll['payroll_for'])))->format('F')) . ' ' .
                                    date('Y', strtotime($payroll['payroll_for']))
                                    ?>
                                </button>
                            </form>
                            <?php
                        }else{
                            ?>
                            <?= strtoupper(DateTime::createFromFormat('!m',
                                date('m', strtotime($payroll['payroll_for'])))->format('F')) . ' ' .
                            date('Y', strtotime($payroll['payroll_for']))
                            ?>
                            <?php
                        }
                        ?>

                    </th>
              <?php
                }
              ?>
              <th style="text-align: center">TOTAL</th>
          </tr>
          </thead>
          <tbody>


          <tr>
              <td colspan="<?= $number_of_payrolls+2 ?>" style="font-weight: bold; font-style: italic; font-size: larger;  background: rgba(220,220,220,0.15)">Staff Cost</td>
          </tr>
          <tr>
              <td>
                  Staff Salary
              </td>
                  <?php
                  $total_of_total_salary = 0;
                    foreach ($payroll_data as $payroll){
                      $total_of_total_salary += $payroll['total_salary'];
                        ?>
                    <td style="text-align: right">TSH  <?= number_format($payroll['total_salary']) ?></td>
                  <?php
                    }

                  $grand_total += $total_of_total_salary;
                  ?>
              <td style="text-align: right; font-weight: bold">TSH  <?= number_format($total_of_total_salary) ?></td>
          </tr>


          <tr>
              <td colspan="<?= $number_of_payrolls+2 ?>" style="font-weight: bold; font-style: italic; font-size: larger;  background: rgba(220,220,220,0.15)">Allowances</td>
          </tr>
          <?php
           foreach ($all_allowances as $allowance){
               ?>
                <tr>
                    <td><?= $allowance->allowance_name?><?= strtoupper($allowance->allowance_name) == "COMMUNICATION" ? ' Expences' : ' Allowance'  ?></td>
                    <?php
                    $data['total_'.explode(' ',$allowance->allowance_name)[0]] = 0;
                      foreach ($payroll_data as $payroll){
                          $data['total_'.explode(' ',$allowance->allowance_name)[0]] += $payroll['payroll_allowances'][explode(' ',$allowance->allowance_name)[0]];
                          ?>
                          <td style="text-align: right"> TSH  <?= number_format($payroll['payroll_allowances'][explode(' ',$allowance->allowance_name)[0]]) ?></td>
                          <?php
                      }

                    $grand_total += $data['total_'.explode(' ',$allowance->allowance_name)[0]];
                    ?>
                    <td style="text-align: right; font-weight: bold">TSH <?= number_format($data['total_'.explode(' ',$allowance->allowance_name)[0]]) ?></td>
                </tr>
               <?php
           }
            ?>

          <tr>
              <td colspan="<?= $number_of_payrolls+2 ?>" style="font-weight: bold; font-style: italic; font-size: larger; background: rgba(220,220,220,0.15)">Statutory Deductions</td>
          </tr>
          <?php
           foreach ($all_deductions as $deduction){
               ?>
               <tr>
                   <td><?= strtoupper($deduction->deduction_name) ?></td>
                   <?php
                   $data['total_'.explode(' ', $deduction->deduction_name)[0]] = 0;
                   foreach ($payroll_data as $payroll){
                       $data['total_'.explode(' ', $deduction->deduction_name)[0]] += $payroll['payroll_deductions'][explode(' ', $deduction->deduction_name)[0]];
                       ?>
                       <td style="text-align: right"> TSH  <?= number_format($payroll['payroll_deductions'][explode(' ', $deduction->deduction_name)[0]]) ?></td>
                       <?php
                   }

                   $grand_total += $data['total_'.explode(' ', $deduction->deduction_name)[0]];

                   ?>
                   <td style="text-align: right; font-weight: bold">TSH  <?= number_format($data['total_'.explode(' ', $deduction->deduction_name)[0]]) ?></td>
               </tr>
              <?php
          }
          ?>

          </tbody>
          <tfoot>
          <tr>
              <td style="font-size: larger; font-weight: bold">Total Payroll Expenses</td>
              <?php
              foreach ($payroll_data as $payroll){
                  ?>
                  <td style="font-weight: bold; font-size: larger; text-align: right">TSH <?= number_format($payroll['total_payroll_cost']) ?></td>
                  <?php
              }
              ?>
              <td style="font-weight: bold; text-align: right; font-size: larger; background: #a4b2cb">TSH <?= number_format($grand_total) ?></td>
          </tr>
          </tfoot>
      </table>


<?php
  }

if(!empty($cost_center_payments)){
    ?>
    <h3>Administrative Costs</h3>
    <b>Rate To TSH: </b>1.00

    <table
        <?php if ($print) {
            ?> width="100%" border="1" cellspacing="0"

            <?php
        } else {
            ?>
            class="table table-bordered table-hover table-striped"
            <?php
        } ?> style="font-size: 12px" >

        <thead>
        <tr>
            <th>Descriptions</th><th>Budget</th><th>Income</th><th style="text-align: right">Commitments</th><th style="text-align: right">Expenditures</th><th>Variance</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total_oacc = 0;
        $total_approved = 0;
        foreach($cost_centers as $cost_center) {
            if (!empty($cc_with_approved_requests[$cost_center->cost_center_name])) {
                ?>
                <tr>
                    <td width="26%" style="font-weight: bold;"><?= strtoupper($cost_center->cost_center_name) ?></td>
                    <td width="9%" style="text-align: right">TSH 0</td>
                    <td width="16%" style="text-align: right">TSH 0</td>
                    <td width="32%" style="text-align: right"><?= $print ? 'TSH '. number_format(( $cost_center_approved_amount[$cost_center->cost_center_name] - $cost_center_amount[$cost_center->cost_center_name]),2) :  $approved_payments_pop_up[$cost_center->cost_center_name]  ?></td>
                    <td width="8%" style="text-align: right"><?= $print ? 'TSH '.number_format($cost_center_amount[$cost_center->cost_center_name],2) : $othr_admin_costs_pop_up_main[$cost_center->cost_center_name] ?></td>
                    <td width="9%" style="text-align: right">TSH 0</td>
                </tr>
        <?php
                $total_oacc += $cost_center_amount[$cost_center->cost_center_name];
                $total_approved += ($cost_center_approved_amount[$cost_center->cost_center_name] - $cost_center_amount[$cost_center->cost_center_name]);
            }
        }
        ?>

        </tbody>
        <tfoot>
            <tr>
                <td style="text-align: left; font-weight: bold;">TOTAL ADMINISTRATIVE COSTS</td>
                <td style="text-align: right; font-weight: bold; font-size: 10px">TSH 0</td>
                <td style="text-align: right; font-weight: bold; font-size: 10px">TSH 0</td>
                <td style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($total_approved,2) ?></td>
                <td style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($total_oacc,2) ?></td>
                <td style="text-align: right; font-weight: bold; font-size: 10px">TSH 0</td>
            </tr>

            <?php
            $grand_total_material_budget += 0;
            $grand_total_labour_budget += 0;
            $grand_total_other_budget += 0;
            $grand_total_expected_income += 0;
            $grand_total_actual_income += 0;
            $grand_total_orders_commitments += 0;
            $grand_total_sub_contract_commitments += 0;
            $grand_total_other_commitments += $total_approved;
            $grand_total_commitments += $total_approved;
            $grand_total_expenditures += $total_oacc;
            $grand_total_variance += 0;
            ?>
        </tfoot>
    </table>

<br/>
    <br/>
    <table
        <?php if ($print) {
            ?> width="100%" border="1" cellspacing="0"

            <?php
        } else {
            ?>
            class="table table-bordered table-hover table-striped"
            <?php
        } ?> style="font-size: 12px" >

        <thead>
        <tr>
            <th width="8%" rowspan="2">&nbsp;</th>
            <th width="27%" colspan="3" style="text-align: center">Budget</th>
            <th width="16%" colspan="2" style="text-align: center;">Income</th>
            <th width="32%" colspan="4" style="text-align: center;">Commitments</th>
            <th width="8%" rowspan="2">Expenditures</th>
            <th width="10%" rowspan="2">Variance</th>
        </tr>
        <tr>
            <th>Material</th>
            <th>Labour</th>
            <th>Total</th>
            <th>Expected</th>
            <th>Actual</th>
            <th>Orders</th>
            <th>Sub Contracts</th>
            <th>Others</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
                <tr style="background: rgba(44,190,239,0.43)">
                    <td width="8%" style="font-weight: bold;">GRAND TOTAL </td>
                    <th width="8.2222222%" style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($grand_total_material_budget,2) ?></th>
                    <th width="8.2222222%" style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($grand_total_labour_budget,2) ?></th>
                    <th width="8.2222222%" style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($grand_total_other_budget,2) ?></th>
                    <th width="8.2222222%" style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($grand_total_expected_income,2) ?></th>
                    <th width="8.2222222%" style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($grand_total_actual_income,2) ?></th>
                    <th width="8.2222222%" style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($grand_total_orders_commitments,2) ?></th>
                    <th width="8.2222222%" style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($grand_total_sub_contract_commitments,2) ?></th>
                    <th width="8.2222222%" style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($grand_total_other_commitments,2) ?></th>
                    <th width="8.2222222%" style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($grand_total_commitments,2) ?></th>
                    <td width="9%" style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($grand_total_expenditures,2) ?></td>
                    <td width="9%" style="text-align: right; font-weight: bold; font-size: 10px"><?= 'TSH '.number_format($grand_total_variance,2) ?></td>
                </tr>
        </tbody>
    </table>

    <?php
}
?>







