<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/27/2018
 * Time: 10:30 PM
 */

$requirement_type_no = $tender_requirement_type->{$tender_requirement_type::DB_TABLE_PK};
?>

<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_requirement_type<?= $requirement_type_no ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_requirement_type<?= $requirement_type_no ?>" class="modal fade" role="dialog">
        <?php
        $data['tender_requirement_type'] = $tender_requirement_type;
        $this->load->view('tenders/settings/requirement_type_form');?>
    </div>

    <button class="btn btn-danger btn-xs delete_requirement_type" requirement_type_number = "<?= $requirement_type_no ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
