<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/24/2018
 * Time: 12:35 PM
 */

$last_approval_id =  $last_approval ? $last_approval->{$last_approval::DB_TABLE_PK} : 0;
?>
<div class="modal-dialog" style="width: 70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Sub Contract Payment Requisition Approval</h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-2">
                        <label for="approve_date" class="control-label">Approve Date</label>
                        <input type="hidden" name="sub_contract_requisition_id" value="<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                        <input type="hidden" name="approval_chain_level_id" value="<?=  $current_approval_level->{$current_approval_level::DB_TABLE_PK} ?>">
                        <input type="text" class="form-control datepicker" required name="approve_date" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="currency_id" class="control-label">Currency</label>
                        <?php
                        $currency = $requisition->currency();
                        echo form_dropdown('currency_id',[
                            $currency->{$currency::DB_TABLE_PK} => $currency->name_and_symbol()
                        ],$currency->{$currency::DB_TABLE_PK},
                            ' class=" form-control" readonly'
                        )
                        ?>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="control-label" for="email">Requisition No:</label>
                        <div>
                            <span class="form-control-static"><?= $requisition->sub_contract_requisition_number() ?></span>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="control-label" for="email">Requested For:</label>
                        <div>
                            <span class="form-control-static"><?= wordwrap($requisition->cost_center_name(),30,'<br/>') ?></span>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="control-label" for="email">Requested By:</label>
                        <div>
                            <span class="form-control-static"><?= $requisition->requester()->full_name() ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 table-responsive">
                    <?php
                    $sub_contract_requisition_items = $requisition->sub_contract_requisition_items();

                    ?>
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Sub Contract Information</th><th>Certificate No.</th><th>Amount</th>
                            </tr>
                            </thead>
                            <tbody class="major_table_tbody">
                            <?php
                            $total_amount = 0;
                            foreach ($sub_contract_requisition_items as $item){
                                $certificate = $item->certificate();
                                $certificate_options = $certificate->sub_contract()->certificates(true);
                                ?>
                                <tr>
                                    <td>
                                        <?= wordwrap($item->certificate()->sub_contract()->contract_name.' - '.$item->certificate()->sub_contract()->stakeholder()->stakeholder_name,50,'<br/>')  ?>
                                        <input type="hidden" name="sub_contract_payment_requisition_item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                    </td>
                                    <td>
                                        <?= form_dropdown('certificate_id',$certificate_options,$certificate->{$certificate::DB_TABLE_PK}, ' class="form-control searchable" ') ?>
                                    </td>
                                    <?php
                                    if ($last_approval) {

                                        $approved_item = $item->approved_item($last_approval_id);
                                        $amount = $approved_item->approved_amount;
                                        $total_amount += $amount;
                                        ?>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                <input style="text-align: right" type="text" name="amount" class="form-control" value="<?= number_format($amount, 2) ?>">
                                            </div>
                                        </td>

                                        <?php
                                    } else {

                                        $amount = $item->requested_amount;
                                        $total_amount += $amount;
                                        ?>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                <input style="text-align: right" type="text" name="amount" class="form-control" value="<?= number_format($amount, 2) ?>">
                                            </div>
                                        </td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                <?php
                            }

                            if($last_approval && $last_approval->vat_inclusive == 1){
                                $vat_amount = 0.01*$last_approval->vat_percentage*$total_amount;
                                ?>
                                <tr id="vat_amount_row">
                                    <td colspan="2" style="text-align: right"><input type="hidden" name="vat_amount" value="<?=  $last_approval->vat_inclusive = 1 ? $vat_amount : 0 ?>">VAT</td>
                                    <td id="vat_amount_display">
                                    </td>
                                </tr>
                                <?php
                            } else if($requisition->vat_inclusive == 1){
                                $vat_amount = 0.01*$requisition->vat_percentage*$total_amount;
                                ?>
                                <tr id="vat_amount_row">
                                    <td colspan="2" style="text-align: right"><input type="hidden" name="vat_amount" value="<?=  $requisition->vat_inclusive = 1 ? $vat_amount : 0 ?>">VAT</td>
                                    <td id="vat_amount_display">
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                                <tr>
                                    <td colspan="2"  style="text-align: right"><strong>TOTAL</strong></td>
                                    <td class="total_amount_display" style="text-align: right"></td>
                                </tr>
                            </tbody>
                        </table>
                </div>

                <div class="col-xs-12">
                    <div class="form-group">
                        <label for="comments" class="control-label">Approving Comments</label>
                        <textarea name="comments" class="form-control"><?= $last_approval ? $last_approval->approving_comments : '' ?></textarea>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm reject_sub_contract_requisition">Reject</button>
            <button type="button" class="btn btn-default btn-sm approve_sub_contract_requisition">Submit Approval</button>
        </div>
    </div>
</div>
