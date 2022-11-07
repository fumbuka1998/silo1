<?php
?>

<div class="modal-dialog" style="width: 50%">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= wordwrap($parameter->name.' Parameter Type Registration',30,'<br/>') ?></h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-12">
                            <label for="parameter_name" class="control-label">Parameter Type Name</label>
                            <input type="text" class="form-control" required name="parameter_type_name" value="">
                            <input type="hidden" name="category_parameter_id" value="<?= $parameter->{$parameter::DB_TABLE_PK}?>">

                        </div>

                        <div class="form-group col-md-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-default btn-sm save_parameter_type">
                        Save
                    </button>
                </div>
                <div class="parameter_type_container">

                </div>
            </div>

            <div class="modal-footer">
            </div>
        </form>
    </div>
</div>