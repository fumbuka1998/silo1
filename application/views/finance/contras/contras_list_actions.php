<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/22/2016
 * Time: 3:06 PM
 */

if($contra->employee_id == $this->session->userdata('employee_id') || check_permission('Administrative Actions')) {
    ?>
<span>
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
                    <a  target="_blank" href="<?= base_url('finance/preview_contra/'.$contra->{$contra::DB_TABLE_PK} )?>">
                        <i class="fa fa-file-pdf-o"></i> Contra Sheet
                    </a>
                </li>
                <li>
                    <a data-toggle="modal" data-target="#edit_contra_<?= $contra->{$contra::DB_TABLE_PK} ?>"
                            class="btn btn-default btn-xs">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </li>
                <li>
                    <a style="color: white" class="btn btn-danger btn-xs delete_contra" contra_id="<?= $contra->{$contra::DB_TABLE_PK} ?>">
                        <i class="fa fa-trash"></i> Delete
                    </a>
                </li>
            </ul>
        </div>
        <div id="edit_contra_<?= $contra->{$contra::DB_TABLE_PK} ?>" class="modal fade contra_form" role="dialog">
            <?php $this->load->view('finance/contras/contra_form'); ?>
        </div>
    </div>
</span>
    <?php
}




