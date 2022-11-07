<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/16/2017
 * Time: 4:48 PM
 */
?>
<div class="dropdown">
    <button class="btn btn-xs btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Actions
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li><a href="<?= base_url('finance/view_cash_requisition/'.$requisition_id)?>" target="_blank"><i class="fa fa-eye"></i> View</a></li>
<?php if($status != 'APPROVED' && $status != 'DECLINED' && $status != 'PAID'){ ?>
        <li><a href="#"  data-toggle="modal" data-target="#edit_cash_requisition_<?= $requisition_id ?>">Edit</a></li>
        <li><a href="#"  data-toggle="modal"  data-target="#approve_cash_requisition_<?= $requisition_id ?>">Approve</a></li>
        <li><a class="delete_cash_requisition" cash_requisition_id="<?= $requisition_id ?>" href="#">Delete</a></li>
        <li><a href="#">Decline</a></li>
<?php }
        if($status == 'APPROVED'){
            ?>
            <li><a href="#"  data-toggle="modal" data-target="#pay_cash_requisition_<?= $requisition_id ?>">
                    <i class="fa fa-money"></i> Cash Out
                </a></li>
            <?php
        }
?>
    </ul>
</div>

<?php if($status != 'APPROVED' && $status != 'DECLINED'){ ?>
    <div id="approve_cash_requisition_<?= $requisition_id ?>"
         class="modal fade cash_requisition_approval_form" role="dialog">
        <?php $this->load->view('finance/account_profile/cash_requisition_approval_form'); ?>
    </div>
<?php }

if($status == 'APPROVED'){ ?>
    <div id="pay_cash_requisition_<?= $requisition_id ?>"
         class="modal fade cash_requisition_form" role="dialog">
        <?php $this->load->view('finance/account_profile/expense_payment_voucher_form'); ?>
    </div>
<?php } ?>

