<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/1/2018
 * Time: 7:52 AM
 */
$sub_component_id = $tender_sub_component->{$tender_sub_component::DB_TABLE_PK};
?>

<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_tender_subcomponent_<?= $sub_component_id ?>"
            class="btn btn-xs btn-default">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_tender_subcomponent_<?= $sub_component_id?>" class="modal fade" role="dialog">
        <?php
            $data['tender_sub_component']= $tender_sub_component;
            $this->load->view('tenders/profile/components/tender_sub_component_form',$data); ?>
    </div>
    <button class="btn btn-danger btn-xs delete_sub_component" sub_component_id="<?= $sub_component_id ?>" >
        <i class="fa fa-trash"></i> Delete
    </button>
</span>

