<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 17/12/2018
 * Time: 15:42
 */
if($invoiced){
    ?>
    <form method="post" target="_blank" action="<?= base_url('finance/preview_outgoing_invoice/'.$invoice_number) ?>">
        <button style="width: 55px" class="btn btn-yahoo btn-xs">
            Invoiced
        </button>
    </form>
    <?php
}else if($paid){
     ?>
    <form method="post" target="_blank" action="<?= base_url('finance/preview_receipt/'.$paid_invoice) ?>">
        <button style="width: 55px" class="btn btn-success btn-xs">
            Paid
        </button>
    </form>
    <?php
}else {

    ?>

    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">Actions</button>
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a target="_blank"
                   href="<?= base_url('projects/preview_services/' . $maintenance_service->{$maintenance_service::DB_TABLE_PK}) ?>"
                   id="preview_cervices">
                    <i class="fa fa-eye"></i> Preview
                </a>
                <?php if (($this->session->userdata('employee_id') == $maintenance_service->created_by)  || check_permission('Administrative Actions') ) { ?>
                    <a data-toggle="modal"
                       data-target="<?= '#service' . $maintenance_service->{$maintenance_service::DB_TABLE_PK} ?>"
                       href="#" class="edit_service">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <a href="#" class="btn btn-danger delete_services"
                       service_id="<?= $maintenance_service->{$maintenance_service::DB_TABLE_PK} ?>">
                        <i class="fa fa-trash"></i> Delete
                    </a>
                <?php } ?>
            </li>
        </ul>

        <div id="<?= 'service' . $maintenance_service->{$maintenance_service::DB_TABLE_PK} ?>"
             class="modal fade service_form" role="dialog">
            <?php $this->load->view('projects/services/service_form'); ?>
        </div>

    </div>
    <?php
}