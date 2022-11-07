<?php
/**
 * Created by PhpStorm.
 * User: kihunakasobo
 * Date: 2019-07-19
 * Time: 12:25
 */

if(check_privilege('Finance Actions')){
?>
<div class="btn-group">
    <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">Actions</button>
    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <il>
            <?php if ($debt_nature == "stock_sale") { ?>
                <a class="btn btn-block btn-xs" data-toggle="modal"
                   data-target="#attachment_<?= $sale->{$sale::DB_TABLE_PK} ?>" >
                    <i class="fa fa-paperclip"></i> Attachnments
                </a>
            <?php } else if ($debt_nature == "maintenance_service") { ?>
                <a class="btn btn-block btn-xs" data-toggle="modal"
                   data-target="#attachment_<?= $maintenance_service->{$maintenance_service::DB_TABLE_PK} ?>" >
                    <i class="fa fa-paperclip"></i> Attachments
                </a>
            <?php } else if($debt_nature == "certificate") { ?>
                <a class="btn btn-block btn-xs" data-toggle="modal"
                   data-target="#attachment_<?= $project_certificate->{$project_certificate::DB_TABLE_PK} ?>" >
                    <i class="fa fa-paperclip"></i> Attachments
                </a>
            <?php } ?>
        </il>
        <li>
            <a target="_blank"
               href="<?= base_url('finance/preview_outgoing_invoice/' . $outgoing_invoice->{$outgoing_invoice::DB_TABLE_PK}) ?>"
               id="preview_outgoing_invoices">
                <i class="fa fa-eye"></i> Preview
            </a>
        </li>
        <?php
        if ($this->session->userdata('employee_id') == $outgoing_invoice->created_by && !$outgoing_invoice->has_receipt()) {
            ?>
            <li>
                <a class="btn btn-block btn-xs" data-toggle="modal"
                   data-target="#edit_outgoing_invoice_<?= $outgoing_invoice->{$outgoing_invoice::DB_TABLE_PK} ?>">
                    <i class="fa fa-edit"></i> Edit
                </a>
            </li>
            <li>
                <a style="color: white" outgoing_invoice_id="<?= $outgoing_invoice->{$outgoing_invoice::DB_TABLE_PK} ?>"
                   class="btn  btn-block btn-xs btn-danger delete_outgoing_invoice">
                    <i class="fa fa-trash"></i> Delete
                </a>
            </li>
        <?php }
        ?>
    </ul>
    <?php if($outgoing_invoice){ ?>
        <div id="edit_outgoing_invoice_<?= $outgoing_invoice->{$outgoing_invoice::DB_TABLE_PK} ?>"
             class="modal fade outgoing_invoice_form" role="dialog">
            <?php $this->load->view('finance/debts/edit_outgoing_invoice_form'); ?>
        </div>
    <?php } ?>
</div>
<?php } ?>
