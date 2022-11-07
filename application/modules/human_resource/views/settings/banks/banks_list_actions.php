<?php
/**
 * Created by PhpStorm.
 * User: Munyaki
 * Date: 30-Jun-17
 * Time: 3:27 PM
 */
?>
<?php
$bank_Id = $banks_data->{$banks_data::DB_TABLE_PK};
//print_r([$banks_data]);

?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_bank_<?= $bank_Id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_bank_<?= $bank_Id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('bank_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_bank" delete_bank_id="<?= $bank_Id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
