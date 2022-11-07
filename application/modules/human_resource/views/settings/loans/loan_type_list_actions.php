<?php
/**
 * Created by PhpStorm.
 * User: Munyaki
 * Date: 30-Jun-17
 * Time: 3:27 PM
 */
?>
<?php
$loan_type_id = $loan_type_data->{$loan_type_data::DB_TABLE_PK};

?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_loan_type<?= $loan_type_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_loan_type<?= $loan_type_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('settings/loans/loan_type_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_loan_type" loan_type_id="<?= $loan_type_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
