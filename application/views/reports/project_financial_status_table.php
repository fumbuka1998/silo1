<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 08/10/2018
 * Time: 23:07
 */
?>

<table <?php if(isset($print)){
    ?>
    font-size = "10px" width="100%" border="1" cellspacing="0"
    <?php
} else { ?> class="table table-bordered table-hover"
<?php } ?>
>
    <tbody>
    <?php
    $cost_types = [
        'material',
        'miscellaneous',
        'permanent_labour',
        'equipment',
        'sub_contract',
        'casual_labour',
        'imprest'
    ];
    $financial_record_types = [
        'Project',
        'Contract Sum(TSH)',
        'Budget(TSH)',
        'Actual Cost(TSH)',
        'Surplus(TSH)',
        'Certificates(TSH)'
    ];
    /** @var TYPE_NAME $table_items */
    $sn = 0;
    foreach($financial_record_types as $record_type){
        $sn++;
        ?>
        <tr>
            <td style="width: 20px"><?= $sn ?></td>
        <?php if($record_type == "Actual Cost(TSH)"){ ?>
            <td nowrap="true" style="width: 250px; font-weight: bold; <?= $sn == 1 ? "background-color: #dfdfdf;" : "" ?> text-align: left">
                <table <?= isset($print) ? 'font-size = "10px" width="100%" border="1" cellspacing="0"' : 'class="table table-hover"' ?> >
                    <tbody>
                    <tr>
                        <td  nowrap="true" style="font-weight: bold; text-align: left"><?= $record_type ?> :</td>
                    </tr>
                    <tr>
                        <td  nowrap="true" style="font-weight: bold; text-align: left"> - Used/Installed Material(TSH)</td>
                    </tr>
                    <tr>
                        <td  nowrap="true" style="font-weight: bold; text-align: left"> - Material On/Off Site(TSH)</td>
                    </tr>
                    <tr>
                        <td  nowrap="true" style="font-weight: bold; text-align: left"> - VAT(TSH)</td>
                    </tr>
                    <tr>
                        <td  nowrap="true" style="font-weight: bold; text-align: left"> - Unreceived Goods(TSH)</td>
                    </tr>
                    <tr>
                        <td  nowrap="true"  style="font-weight: bold; text-align: left"> - Permanent Labour(TSH)</td>
                    </tr>
                    <tr>
                        <td  nowrap="true"  style="font-weight: bold; text-align: left"> - Casual Labour(TSH)</td>
                    </tr>
                    <tr>
                        <td  nowrap="true"  style="font-weight: bold; text-align: left"> - Sub Contracts(TSH)</td>
                    </tr>
                    <tr>
                        <td  nowrap="true"  style="font-weight: bold; text-align: left"> - Other Overheads(TSH)</td>
                    </tr>
                    <tr>
                        <td  nowrap="true"  style="font-weight: bold; background-color: #dfdfdf;"> TOTAL(TSH)</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        <?php } else if($record_type == "Certificates(TSH)"){ ?>
            <td  nowrap="true"  style="width: 250px; font-weight: bold; text-align: left">
                <table <?= isset($print) ? 'font-size = "10px" width="100%" border="1" cellspacing="0"' : 'class="table table-hover"' ?> >
                <tbody>
                <tr>
                    <td  nowrap="true"  style="font-weight: bold; text-align: left"><?= $record_type ?> :</td>
                </tr>
                <tr>
                    <td  nowrap="true"  style="font-weight: bold; text-align: left"> - Certified Amount(TSH)</td>
                </tr>
                <tr>
                    <td  nowrap="true"  style="font-weight: bold; text-align: left"> - Paid Amount(TSH)</td>
                </tr>
                <tr>
                    <td  nowrap="true"  style="font-weight: bold; background-color: #dfdfdf;"> BALANCE(TSH)</td>
                </tr>
                </tbody>
            </table>
            </td>
        <?php } else { ?>
            <td nowrap="true" style=" font-weight: bold; <?= $sn == 1 ? "background-color: #dfdfdf;" : "" ?> text-align: left"><?= $record_type ?></td>
        <?php } ?>
            <?php
            $overall_per_row = 0;
            foreach ($projects as $project) {
                if(!is_array($table_items[$record_type][$project->project_name])){
                    if($record_type == "Project"){
                        ?>
                        <td style=" font-weight: bold; background-color: #dfdfdf; text-align: center;"><?= isset($print) ? $table_items[$record_type][$project->project_name] :  anchor(base_url('projects/profile/' . $project->{$project::DB_TABLE_PK}), $table_items[$record_type][$project->project_name], 'target="_blank"') ?></td>
                        <?php
                    } else {
                        $overall_per_row += $table_items[$record_type][$project->project_name]
                        ?>
                        <td style=" <?= $sn == 1 ? "font-weight: bold; background-color: #dfdfdf;" : "" ?> text-align: right"><?= number_format($table_items[$record_type][$project->project_name],2) ?></td>
                        <?php
                    }
                } else if(is_array($table_items[$record_type][$project->project_name]) && $record_type == "Actual Cost(TSH)") {
                    $total_actual_costs = $table_items[$record_type][$project->project_name]['actual_cost'] + $table_items[$record_type][$project->project_name]['material_on_site'][$project->project_name]['material_balance_value'] + $table_items[$record_type][$project->project_name]['all_items_vat_amount'];
                    $overall_per_row += $total_actual_costs;
                    ?>
                    <td>
                        <table <?= isset($print) ? 'font-size = "10px" width="100%" border="1" cellspacing="0"' : 'class="table table-hover"' ?> >
                            <tbody>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"><?= number_format($table_items[$record_type][$project->project_name]['material_installed'],2)   ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"><?= isset($print) ? number_format($table_items[$record_type][$project->project_name]['material_on_site'][$project->project_name]['material_balance_value'],2) : $table_items[$record_type][$project->project_name]['material_on_site'][$project->project_name]['pop_up']  ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"><?= number_format($table_items[$record_type][$project->project_name]['all_items_vat_amount'],2) ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"><?= isset($print) ? number_format($table_items[$record_type][$project->project_name]['unreceived_goods'][$project->project_name]['summation_base_currency'],2) : $table_items[$record_type][$project->project_name]['unreceived_goods'][$project->project_name]['pop_up']  ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"><?= number_format($table_items[$record_type][$project->project_name]['permanent_labour'],2)   ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"><?= number_format($table_items[$record_type][$project->project_name]['casual_labour'],2)   ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"><?= isset($print) ? number_format($table_items[$record_type][$project->project_name]['sub_contracts'][$project->project_name]['total_paid_amount'],2) : $table_items[$record_type][$project->project_name]['sub_contracts'][$project->project_name]['pop_up'] ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"><?= isset($print) ? number_format($table_items[$record_type][$project->project_name]['overheads'][$project->project_name]['total_paid_amount'],2) : $table_items[$record_type][$project->project_name]['overheads'][$project->project_name]['pop_up']  ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right; background-color: #dfdfdf;"><?= number_format($total_actual_costs,2)   ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <?php
                } else if(is_array($table_items[$record_type][$project->project_name]) && $record_type == "Certificates(TSH)") {
                    $overall_per_row += $table_items[$record_type][$project->project_name]['certified_amount'][$project->project_name]['total_certified_amount'];
                    ?>
                    <td>
                        <table <?= isset($print) ? 'font-size = "10px" width="100%" border="1" cellspacing="0"' : 'class="table table-hover"' ?> >
                            <tbody>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"><?= isset($print) ? number_format($table_items[$record_type][$project->project_name]['certified_amount'][$project->project_name]['total_certified_amount'],2) : $table_items[$record_type][$project->project_name]['certified_amount'][$project->project_name]['pop_up'] ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"><?= number_format($table_items[$record_type][$project->project_name]['paid_amount'],2)   ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right; background-color: #dfdfdf;"><?= number_format(($table_items[$record_type][$project->project_name]['certified_amount'][$project->project_name]['total_certified_amount'] - $table_items[$record_type][$project->project_name]['paid_amount']),2)   ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <?php
                }
            }
            ?>
            <td style=" <?= $sn == 1 ? "font-weight: bold; background-color: #dfdfdf; text-align: center;" : "text-align: right;" ?>"><?= $sn == 1 ? "Project(s) Financial Position" : number_format($overall_per_row,2) ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
<br/>

<div>
    <h5 style="<?= isset($print) ? 'font-size: 12px;' : ''?> width: 400px; font-weight: bold;  text-align: left">LIABILITIES(Confirmed Orders & Subcontracts)</h5>
</div>
<table <?php if(isset($print)){
    ?>
    font-size = "10px" border="1" cellspacing="0"
    <?php
} else { ?> class="table table-bordered table-hover"
<?php } ?>
>
    <tbody>
    <tr  style="width: 400px; font-weight: bold; background-color: #dfdfdf">
        <td style="font-weight: bold;">VENDOR(S)</td><?php
        foreach ($currencies as $currency) {
            ?>
            <td style="max-width: 180px; font-weight: bold; text-align: center"><?= $currency->symbol ?></td>
            <?php
        }?>
        <td  style="font-weight: bold; text-align: center">TOTAL in TSH</td>
    </tr>
    <?php
    $sub_total_arrs = [];
    $grand_total = 0;
    if (!empty($vendors_table_items)) {
        $n = 1;
        foreach($vendors_with_orders as $vendor){
            $invoices = true;
            if($vendors_table_items[$vendor->stakeholder_name]['vendor_balance'] != 0 && (round($vendors_table_items[$vendor->stakeholder_name]['vendor_balance'],2) != -0.00 || round($vendors_table_items[$vendor->stakeholder_name]['vendor_balance'],2) != 0.00)) {
                ?>
                <tr>
                    <td style="width: 400px; text-align: left"><?= isset($print) ? $vendors_table_items[$vendor->stakeholder_name][0] : anchor(base_url('procurements/vendor_profile/' . $vendor->{$vendor::DB_TABLE_PK} . '/' . $invoices), $vendors_table_items[$vendor->stakeholder_name][0],'target="_blank" ') ?></td>
                    <?php
                    $currency_index = 0;
                    foreach ($currencies as $currency) {
                        $n++;
                        $balance_per_currency = $vendors_table_items[$vendor->stakeholder_name][$currency->symbol]['balance_per_currency'];
                        $sub_total[$currency_index] = $balance_per_currency;
                        ?>
                        <td style="max-width: 180px; text-align: right;"><?= $balance_per_currency > 1 ? isset($print) ? $currency->symbol.' '.number_format($balance_per_currency, 2) : $vendors_table_items[$vendor->stakeholder_name][$currency->symbol]['pop_up'] : "" ?></td>
                    <?php
                        $currency_index++;
                    }
                    $grand_total += $vendors_table_items[$vendor->stakeholder_name]['vendor_balance'];
                    ?>
                    <td style="max-width: 180px; text-align: right;"><?= 'TSH '.number_format($vendors_table_items[$vendor->stakeholder_name]['vendor_balance'],2) ?></td>
                </tr>
                <?php
                $sub_total_arrs[] = $sub_total;
            }
        }
    }
    ?>
    <tr  style="font-weight: bold; background-color: #dfdfdf">
        <td style="width: 400px; text-align: left"><strong>SUB TOTAL VENDOR(S)</strong></td>
        <?php
        for($column_index = 0; $column_index < $currency_index; $column_index++){
            $sub_total_vendors = !empty($sub_total_arrs) ? array_sum(array_column($sub_total_arrs, $column_index)) : 0;
            ?>
            <td style="max-width: 180px;  font-weight: bold; text-align: right"><?= $currencies[$column_index]->symbol.' '.number_format($sub_total_vendors,2) ?></td>
            <?php
        }?>
        <td style="max-width: 180px;  font-weight: bold; text-align: right"><?= 'TSH'.'&nbsp;'.number_format($grand_total,2) ?></td>
    </tr>
    <tr>
        <td colspan="<?= $n + 1 ?>">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="<?= $n + 1 ?>" style="width: 400px; font-weight: bold; background-color: #dfdfdf; text-align: left">SUBCONTRACT(S)</td>
    </tr>
    <?php
    $sub_total_sub_contracts = 0;
    if(!empty($sub_contracts)) {
        foreach ($sub_contracts as $sub_contract) {
            if($sub_contracts_table_items[$sub_contract->contract_name]['amount'] != 0 && (round($sub_contracts_table_items[$sub_contract->contract_name]['amount'],2) != -0.00 || round($sub_contracts_table_items[$sub_contract->contract_name]['amount'],2) != 0.00)) {
                ?>
                <tr>
                    <td><?= isset($print) ? $sub_contracts_table_items[$sub_contract->contract_name][0] : anchor(base_url("contractors/profile/" . $sub_contracts_table_items[$sub_contract->contract_name]['sub_contractor_id']), $sub_contracts_table_items[$sub_contract->contract_name][0], 'target="_blank"') ?></td>
                    <?php
                    $total_sub_contract_amount = 0;
                    foreach ($currencies as $currency) {
                        if ($currency->symbol == 'TSH') {
                            $sub_contract_amount = $sub_contracts_table_items[$sub_contract->contract_name]['amount'];
                            $total_sub_contract_amount += $sub_contract_amount;
                            ?>
                            <td style="max-width: 180px; text-align: right"><?= 'TSH' . '&nbsp;' . number_format($sub_contract_amount, 2) ?></td>
                            <?php
                        } else {
                            ?>
                            <td style="max-width: 180px; text-align: right">&nbsp;</td>
                            <?php
                        }
                    }
                    ?>
                    <td style="text-align: right"><?= 'TSH' . '&nbsp;' . number_format($total_sub_contract_amount, 2) ?></td>
                </tr>
                <?php
                $grand_total += $total_sub_contract_amount;
                $sub_total_sub_contracts += $sub_contracts_table_items[$sub_contract->contract_name]['amount'];
            }
        }
    }
    ?>
    <tr  style="font-weight: bold; background-color: #dfdfdf">
        <td style="width: 400px; text-align: left"><strong>SUB TOTAL SUBCONTRACT(S)</strong></td>
        <?php
        for($column_index = 0; $column_index < $currency_index; $column_index++){
            ?>
            <td style="max-width: 180px;  font-weight: bold; text-align: right"><?= $column_index == 0 ? $currencies[$column_index]->symbol.' '.number_format($sub_total_sub_contracts,2) : $currencies[$column_index]->symbol.' '.number_format(0,2) ?></td>
            <?php
        } ?>
        <td style="max-width: 180px;  font-weight: bold; text-align: right"><?= 'TSH'.'&nbsp;'.number_format($sub_total_sub_contracts,2) ?></td>
    </tr>
    <tfoot>

    <tr  style="font-weight: bold; background-color: #dfdfdf">
        <td style="width: 400px; text-align: left"><strong>BALANCE</strong></td>
        <?php
        for($column_index = 0; $column_index < $currency_index; $column_index++){
            $sub_total_vendors = !empty($sub_total_arrs) ? array_sum(array_column($sub_total_arrs, $column_index)) : 0;
            if($column_index == 0){
                $sub_total_vendors += $sub_total_sub_contracts;
            }
            ?>
            <td style="max-width: 180px;  font-weight: bold; text-align: right"><?= $currencies[$column_index]->symbol.' '.number_format($sub_total_vendors,2) ?></td>
            <?php
        }?>
        <td style="max-width: 180px;  font-weight: bold; text-align: right"><?= 'TSH'.'&nbsp;'.number_format($grand_total,2) ?></td>
    </tr>
    </tfoot>
    </tbody>
</table>









