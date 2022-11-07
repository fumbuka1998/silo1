<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 8/14/2018
 * Time: 9:02 AM
 */

?>

<div style="width: 100%">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
            Actions
        </button>
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <?php
            $can_retire = $imprest_voucher->handler_id == $this->session->userdata('employee_id');
            if(($balance == $total_quantity && $total_received_quantity == 0 && $can_retire) || $balance != $total_quantity && $total_received_quantity > 0 && $total_received_quantity < $total_quantity && $can_retire){
                ?>
                <li>
                    <a href="#"  data-toggle="modal" data-target="#retire_imprest_<?= $imprest_voucher->{$imprest_voucher::DB_TABLE_PK} ?>"
                       class="btn btn-success btn-xs ">
                        <i class="fa fa-edit"></i>Retire
                    </a>
                </li>
            <?php } ?>
            <li>
                <a  class="btn btn-default btn-xs" target="_blank" href="<?= base_url('finance/preview_imprest_voucher/'.$imprest_voucher->{$imprest_voucher::DB_TABLE_PK} ) ?>">
                    <i class="fa fa-clipboard"></i>Sheet
                </a>
            </li>
            <li>
                <a data-toggle="modal" data-target="#imprest_documents_<?= $imprest_voucher->{$imprest_voucher::DB_TABLE_PK} ?>" href="#">
                    <i class="fa fa-paperclip"></i> Documents
                </a>
            </li>
        </ul>
    </div>
    <div id="retire_imprest_<?= $imprest_voucher->{$imprest_voucher::DB_TABLE_PK} ?>" class="modal fade imprest_retirement_form" role="dialog">
        <?php
            $this->load->view('finance/transactions/approved_cash_requisitions/imprest/imprest_retirement_form');
        ?>
    </div>
    <div id="imprest_documents_<?= $imprest_voucher->{$imprest_voucher::DB_TABLE_PK} ?>" class="modal fade imprest_ducuments" role="dialog">
        <?php
            $this->load->view('finance/transactions/approved_cash_requisitions/imprest/imprest_ducuments');
        ?>
    </div>
</div>