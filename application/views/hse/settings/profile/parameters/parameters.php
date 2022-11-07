<?php
if(!empty($parameter_types)){
    ?>
    <div class=" table-responsive col-xs-12" category_parameter_id="<?= $parameter->{$parameter::DB_TABLE_PK} ?>">
        <table class="table table-hover table-striped" style="table-layout: fixed">
            <thead>
            <tr>
                <th style="width: 8%">S/N</th><th>Parameter Type</th><th style="width: 50%">Description</th><th style="width: 15%"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $row = 1;
            foreach($parameter_types as $type){
                ?>
                <tr>
                    <td><?= $row++ ?></td>
                    <td><?= $type->name ?></td>
                    <td><?= $type->description ?></td>
                    <td>
                        <div style="font-size: 14px" class="pull-right">
                            <button type="button" title="Delete Parameter Type" class="btn btn-danger btn-xs delete_parameter_type" id="parameter_type_<?= $type->{$type::DB_TABLE_PK} ?>" category_parameter_id ="<?= $parameter->{$parameter::DB_TABLE_PK} ?>" parameter_type__id = "<?= $type->{$type::DB_TABLE_PK} ?>">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
} else {
    ?>
    <div class="alert alert-info col-xs-12">
        No Parameter type found!
    </div>
    <?php
}

