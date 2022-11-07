<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 9/13/2018
 * Time: 11:56 PM
 */
?>

<span>
    <?php if($retirement->is_examined == 0){ ?>
        <button type="button" data-toggle="modal" data-target="#imprest_retirement_examination_form_<?= $retirement->{$retirement::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
            <i class="fa fa-reorder"></i> Examine
        </button>
    <?php } ?>
    <div id="imprest_retirement_examination_form_<?= $retirement->{$retirement::DB_TABLE_PK} ?>" class="modal fade imprest_retirement_examination_form" role="dialog">
        <?php
        $this->load->view('finance/transactions/approved_cash_requisitions/imprest/imprest_retirement_examination_form');
        ?>
    </div>
</span>
