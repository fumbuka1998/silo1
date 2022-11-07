<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/12/2017
 * Time: 3:00 PM
 */

?>

<button data-toggle="modal" data-target="#transfer_order_<?= $requisition_id ?>" class="btn btn-default btn-xs">
    <i class="fa fa-mail-forward"></i> Transfer
</button>
<div id="transfer_order_<?= $requisition_id ?>" class="modal fade transfer_order_transfer_form" role="dialog">
    <?php $this->load->view('inventory/locations/transfer_order_transfer_form') ?>
</div>
