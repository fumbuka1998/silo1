<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/4/2016
 * Time: 6:51 PM
 */

if(check_privilege('Finance Actions')){
?>
<span class="pull-left">
    <button data-toggle="modal" data-target="#edit_account_<?= $account->{$account::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_account_<?= $account->{$account::DB_TABLE_PK} ?>" class="modal fade"  role="dialog">
        <?php $this->load->view('finance/account_form'); ?>
    </div>
    <?php if(!$account->has_transactions()){ ?>
    <button account_id="<?= $account->{$account::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs delete_account">
        <i class="fa fa-trash"></i> Delete
    </button>
    <?php }?>
</span>
<?php } ?>
