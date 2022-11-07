<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/2/2016
 * Time: 12:27 PM
 */
?>
<span class="pull-right">
<?php
    if($transfer_type == 'INTERNAL') {
        ?>
        <a class="btn btn-xs btn-default" target="_blank" href="<?= base_url('inventory/preview_internal_material_transfer/'.$transfer->{$transfer::DB_TABLE_PK}) ?>">
            <i class="fa fa-eye"></i> Preview
        </a>
        <?php
    } else if($transfer_type == 'EXTERNAL'){
?>

    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
            <i class="fa fa-eye"></i>
        </button>
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a class="btn btn-default btn-xs" target="_blank" href="<?= base_url('inventory/preview_external_material_transfer_sheet/'.$transfer->{$transfer::DB_TABLE_PK}) ?>">
                    Transfer Sheet
                </a>
            </li>
            <li>
                <a class="btn btn-default btn-xs" target="_blank" href="<?= base_url('inventory/preview_external_material_transfer_delivery_form/'.$transfer->{$transfer::DB_TABLE_PK}) ?>">
                    Delivery Form
                </a>
            </li>
        </ul>
    </div>

<?php
    }
?>

<?php
    if($transfer_type == 'EXTERNAL' && $transfer->sender_id == $this->session->userdata('employee_id') && $location_id == $transfer->source_location_id){
        $data['location'] = $transfer->source();
        $data['sub_location_options'] = $data['location']->sub_location_options();


     if($transfer->status == 'ON TRANSIT'){ ?>

        <button data-toggle="modal"
                data-target="#edit_external_material_transfer_<?= $transfer->{$transfer::DB_TABLE_PK} ?>"
                class="btn btn-default btn-xs">
            <i class="fa fa-edit"></i>
        </button>
        <div location_id="<?= $transfer->source_location_id ?>" destination_id="<?= $transfer->destination_location_id ?>"
             id="edit_external_material_transfer_<?= $transfer->{$transfer::DB_TABLE_PK} ?>"
             class="modal fade external_material_transfer_form"
              role="dialog">
            <?php $this->load->view('inventory/material/transfers/external_material_transfer_form',$data); ?>
        </div>

         <button transfer_id="<?= $transfer->{$transfer::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs delete_external_material_transfer">
            <i class="fa fa-trash-o"></i>
        </button>
    <?php } ?>
<?php
    } else if($transfer_type == 'EXTERNAL' && $location_id == $transfer->destination_location_id && ($transfer->status == 'ON TRANSIT' || $receivable )){
        $data['location'] = $transfer->destination();
        $data['sub_location_options'] = $data['location']->sub_location_options();
?>
        <button data-toggle="modal"
                data-target="#receive_external_material_transfer_<?= $transfer->{$transfer::DB_TABLE_PK} ?>"
                class="btn btn-success btn-xs">
            <i class="fa fa-check-circle"></i> Receive
        </button>
        <div id="receive_external_material_transfer_<?= $transfer->{$transfer::DB_TABLE_PK} ?>" class="modal fade"
              role="dialog">
            <?php $this->load->view('inventory/material/transfers/receive_external_material_transfer_form',$data); ?>
        </div>
<?php
    }
?>
</span>
