<?php
if(!empty($approved_items)){
?>
<table class="table table-bordered table-hover table-striped">
    <thead>
    <tr>
        <th></th><th>Approval Date</th><th>Nature</th><th>Request No</th><th style="width: 350px">Requested For</th><th>Approved By</th><th>Amount</th><th>Status</th>
    </tr>
    </thead>
    <tbody>
        <?php
        foreach ($approved_items as $approved_item){
            ?>
            <tr>
                <td>
                    <span><input class="to_be_paid" type="checkbox" value=""></span>
                    <input type="hidden" name="requisition_approval_id" value="<?= $approved_item['requisition_approval_id'] ?>">
                    <input type="hidden" name="request_type" value="<?= $approved_item['request_type'] ?>">
                    <input type="hidden" name="approved_invoice_item_id" value="<?= $approved_item['approved_invoice_item_id'] ?>">
                </td>
                <td><?= $approved_item['approval_date'] ?></td>
                <td><?= $approved_item['nature'] ?></td>
                <td><?= $approved_item['request_no'] ?></td>
                <td><?= $approved_item['requested_for'] ?></td>
                <td><?= $approved_item['approved_by'] ?></td>
                <td><input type="text" class="form-control" name="amount" value="<?= $approved_item['amount'] ?>" readonly></td>
                <td><?= $approved_item['status'] ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<?php } else { ?>
<div style="text-align: center" class="alert alert-info col-xs-12">
    No transaction
</div>
<?php } ?>
