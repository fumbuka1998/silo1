<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/9/2016
 * Time: 6:33 PM
 */

if(check_permission('Human Resources')) {
    ?>

    <button data-toggle="modal" data-target="#edit_contract_<?= $contract->{$contract::DB_TABLE_PK} ?>"
            class="btn btn-xs btn-default">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_contract_<?= $contract->{$contract::DB_TABLE_PK} ?>" class="modal fade" tabindex="-1" role="dialog">
        <?php $this->load->view('human_resources/employees/contract_form'); ?>
    </div>
    <?php
}
