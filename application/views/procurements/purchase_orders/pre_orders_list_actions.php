<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 8/2/2016
 * Time: 4:17 PM
 */
?>
<span class="pull-right">
    <?php
    if(check_privilege('Orders Approval') && !$ordered){
        ?>
    <button data-toggle="modal"
            data-target="#pre_ordered_purchase_order_form_<?= $requisition_id . '_' . $stakeholder_id.'_'.$currency_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-shopping-cart"></i> Order
    </button>
    <div id="pre_ordered_purchase_order_form_<?= $requisition_id . '_' . $stakeholder_id.'_'.$currency_id  ?>"
         class="modal fade pre_ordered_purchase_order_form" role="dialog">
        <?php $this->load->view('procurements/purchase_orders/pre_ordered_purchase_order_form'); ?>
    </div>
    <?php } ?>
</span>
