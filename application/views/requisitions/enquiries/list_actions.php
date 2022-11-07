<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 12/18/2018
 * Time: 6:05 PM
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
            <li>
                <a  class="btn btn-default btn-xs" target="_blank" href="<?= base_url('requisitions/preview_enquiry/'.$enquiry->{$enquiry::DB_TABLE_PK} ) ?>">
                    <i class="fa fa-clipboard"></i>Sheet
                </a>
            </li>
            <?php
            if(($enquiry->created_by == $this->session->userdata('employee_id')) || check_permission('Administrative Actions')){
                ?>
                <li>
                    <a class="btn btn-block btn-xs" data-toggle="modal" data-target="#make_requisition_<?= $enquiry->{$enquiry::DB_TABLE_PK} ?>">
                        <i class="fa fa-edit"></i> Make Requisition
                    </a>
                </li>
                <li>
                    <a class="btn btn-block btn-xs" data-toggle="modal" data-target="#edit_enquiry_<?= $enquiry->{$enquiry::DB_TABLE_PK} ?>">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </li>
                <li>
                    <a style="color: white" enquiry_id="<?= $enquiry->{$enquiry::DB_TABLE_PK} ?>" class="btn  btn-block btn-xs btn-danger delete_enquiry">
                        <i class="fa fa-trash"></i> Delete
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div id="make_requisition_<?= $enquiry->{$enquiry::DB_TABLE_PK} ?>" class="modal fade enquiry_to_requisition_form" role="dialog">
        <?php
        $this->load->view('requisitions/enquiries/enquiry_to_requisition_form');
        ?>
    </div>
    <div id="edit_enquiry_<?= $enquiry->{$enquiry::DB_TABLE_PK} ?>" class="modal fade enquiry_form" role="dialog">
        <?php
        $this->load->view('requisitions/enquiries/enquiry_form');
        ?>
    </div>
</div>