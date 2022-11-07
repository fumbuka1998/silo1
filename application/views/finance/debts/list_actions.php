<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 17/12/2018
 * Time: 15:42
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
        <li>
            <?php
            if(check_privilege('Make Payment') && $item_balance > 0) {
                if ($debt_nature == "stock_sale") { ?>
                    <a style="color: white"  class="btn btn-block btn-success btn-xs" data-toggle="modal"
                       data-target="#raise_invoice_<?= $sale->{$sale::DB_TABLE_PK} ?>" class="btn btn-block btn-success btn-xs">
                        <i class="fa fa-send"></i> Raise Invoice
                    </a>
                <?php } else if ($debt_nature == "maintenance_service") { ?>
                    <a style="color: white"  class="btn btn-block btn-success btn-xs" data-toggle="modal"
                       data-target="#raise_invoice_<?= $maintenance_service->{$maintenance_service::DB_TABLE_PK} ?>" class="btn btn-block btn-success btn-xs">
                        <i class="fa fa-send"></i> Raise Invoice
                    </a>
                <?php } else if($debt_nature == "certificate") {
                    $client = $project_certificate->project()->client();
                    if ($client) {
                        ?>
                        <a style="color: white" class="btn btn-block btn-success btn-xs" data-toggle="modal"
                           data-target="#raise_invoice_<?= $project_certificate->{$project_certificate::DB_TABLE_PK} ?>"
                           class="btn btn-block btn-success btn-xs">
                            <i class="fa fa-send"></i> Raise Invoice
                        </a>
                    <?php } else { ?>
                        <a style="color: white" id="<?= $project_certificate->{$project_certificate::DB_TABLE_PK} ?>"
                           class="btn  btn-block btn-xs btn-danger ">
                            <i class="fa fa-warning"></i> No Client Registered
                        </a>
                        <?php
                    }
                }
            }
            ?>
        </li>
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

        <?php if($outgoing_invoice) { ?>
            <li>
                <a target="_blank"
                   href="<?= base_url('finance/preview_outgoing_invoice/' . $outgoing_invoice->{$outgoing_invoice::DB_TABLE_PK}) ?>"
                   id="preview_outgoing_invoices">
                    <i class="fa fa-eye"></i> Preview
                </a>
            </li>
            <?php
        }
        ?>
    </ul>

    <?php
    if(check_privilege('Make Payment')) {
        if ($debt_nature == "stock_sale") { ?>
            <div id="raise_invoice_<?= $sale->{$sale::DB_TABLE_PK} ?>"
                 class="modal fade outgoing_invoice_form" role="dialog">
                <?php $this->load->view('finance/debts/outgoing_invoice_form'); ?>
            </div>
        <?php } else if ($debt_nature == "maintenance_service") { ?>
            <div id="raise_invoice_<?= $maintenance_service->{$maintenance_service::DB_TABLE_PK} ?>"
                 class="modal fade outgoing_invoice_form" role="dialog">
                <?php $this->load->view('finance/debts/outgoing_invoice_form'); ?>
            </div>
        <?php } else if($debt_nature == "certificate") { ?>
            <div id="raise_invoice_<?= $project_certificate->{$project_certificate::DB_TABLE_PK} ?>"
                 class="modal fade outgoing_invoice_form" role="dialog">
                <?php $this->load->view('finance/debts/outgoing_invoice_form'); ?>
            </div>
        <?php }
    }
    ?>
</div>
<?php } ?>
