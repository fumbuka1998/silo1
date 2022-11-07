<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 05/05/2019
 * Time: 06:59
 */

$jv_transaction_id = $jv_transaction->{$jv_transaction::DB_TABLE_PK};
$employee_id = $this->session->userdata('employee_id');
?>

<span>
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
                <a class="btn btn-default btn-xs" target="_blank"
                   href="<?= base_url('finance/preview_journal_voucher/'.$jv_transaction_id) ?>" role="button">
                    <i class="fa fa-clipboard"></i>Journal Voucher
                </a>
            </li>
            <li>
                <a data-toggle="modal" data-target="#jv_transaction_attachments_<?= $jv_transaction_id ?>" href="#">
                    <i class="fa fa-paperclip"></i> Attachments
                </a>
            </li>

    <?php
    $can_edit = $jv_transaction->created_by == $employee_id;
    if ($can_edit) {
        ?>
            <li>
                <a data-toggle="modal" data-target="#edit_jv_transaction<?= $jv_transaction_id ?>"
                   class="btn btn-default btn-xs">
                    <i class="fa fa-edit"></i>Edit
                </a>
            </li>
            <li>
                <a style="color: white" class="btn btn-danger btn-xs delete_jv_transaction" jv_transaction_id="<?= $jv_transaction_id ?>">
                    <i class="fa fa-trash"></i>Delete
                </a>
            </li>
          </ul>
    </div>

        <div id="edit_jv_transaction<?= $jv_transaction_id ?>" class="modal fade journal_voucher_entry_form" role="dialog">
            <?php
            $this->load->view('finance/journals/journal_voucher_entry_form');
            ?>
        </div>
    <?php } else { ?>
        </ul>
    </div>
    <?php } ?>
    <div id="jv_transaction_attachments_<?= $jv_transaction_id ?>" class="modal fade journal_voucher_attachments_modal" tabindex="-1" role="dialog">
        <?php
        $this->load->view('finance/journals/journal_voucher_attachments_modal');
        ?>
    </div>
</span>

