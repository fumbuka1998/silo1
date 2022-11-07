<?php
$edit = isset($topic);
?>

<div class="modal-dialog" style="width: 50%">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $edit ? 'Edit For : '.$topic->name : 'Topic Registration Form' ?></h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-12">
                            <label for="category_name" class="control-label"> Topic</label>
                            <input type="text" class="form-control" required name="topic_name" value="<?= $edit ? $topic->name : '' ?>">
                            <input type="hidden" name="topic_id" value="<?= $edit ? $topic->{$topic::DB_TABLE_PK} : '' ?>">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $topic->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_topic">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
