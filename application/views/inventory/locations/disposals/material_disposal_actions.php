
    <?php
        $disposal_id=$material_disposal->{$material_disposal::DB_TABLE_PK}
    ?>
    <span class="pull-right">
         <button data-toggle="modal" data-target="#material_disposal_details_<?= $disposal_id ?>"
                 class="btn btn-xs btn-default">
        <i class="fa fa-edit"></i> Details
    </button>

        <div id="material_disposal_details_<?= $disposal_id ?>" class="modal fade "  role="dialog">
            <?php $data['material_disposal']=$material_disposal;
            $this->load->view('inventory/locations/disposals/material_disposal_details',$data); ?>

        </div>

    </span>


