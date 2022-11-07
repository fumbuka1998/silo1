<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/12/2017
 * Time: 3:00 PM
 */

?>
<span class="pull-right">
    <?php if($transferable){ ?>
    <button data-toggle="modal" data-target="#transfer_order_<?= $requisition_id ?>" class="btn btn-default btn-xs">
        <i class="fa fa-mail-forward"></i> Transfer
    </button>
    <div id="transfer_order_<?= $requisition_id ?>" class="modal fade transfer_order_transfer_form" role="dialog">
        <?php $this->load->view('inventory/material/transfer_orders/transfer_order_transfer_form') ?>
    </div>
    <?php } ?>

    <a target="_blank" href="<?= base_url('inventory/preview_transfer_order/'.$requisition_approval_id.'/'.$location_id)?>" class="btn btn-xs btn-default"><i class="fa fa-eye"></i> Preview</a>
</span>