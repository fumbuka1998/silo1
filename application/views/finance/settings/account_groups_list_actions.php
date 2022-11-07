<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 3/6/2017
 * Time: 2:27 PM
 */

if($account_group->level != '1'){
?>
<button data-toggle="modal" data-target="#edit_account_group_<?= $account_group->{$account_group::DB_TABLE_PK} ?>"
        class="btn btn-default btn-xs">
    <i class="fa fa-edit"></i> Edit
</button>
<div id="edit_account_group_<?= $account_group->{$account_group::DB_TABLE_PK} ?>" class="modal fade" tabindex="-1"
     role="dialog">
    <?php $this->load->view('finance/settings/account_group_form'); ?>
</div>
<?php } ?>
