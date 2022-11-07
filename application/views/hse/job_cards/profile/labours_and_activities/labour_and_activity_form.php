<?php
$edit = isset($job_card_labour);
if($edit){
    $job_card_services = $job_card_labour->job_card_services();
}

?>
<div class="modal-dialog " style="width: 40%">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $edit ? 'Edit For Job Card Labour and Activity' : 'Labour and Activity Registration Form' ?></h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group " style="width: 90%">
                            <label for="" class="control-label">Labours</label>
                            <?= form_dropdown('labour_id', $labours_options, $edit ? $job_card_labour->employee_id :'', ' class="form-control searchable" ') ?>
                            <input type="hidden" name="job_card_labour_id" value="<?= $edit ? $job_card_labour->{$job_card_labour::DB_TABLE_PK} : '' ?>">
                            <input type="hidden" name="job_card_id" value="<?= $edit ? $job_card_labour->job_card_id : $job_card->{$job_card::DB_TABLE_PK} ?>">
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th > Service</th><th ></th>
                            </tr>
                            <tr style="display: none" class="service_row_template">
                                <td style="width: 90%">
                                    <?= form_dropdown('activity_id', $activities_options, '', ' class="form-control" ') ?>
                                </td>
                                <td style="width: 10%">
                                    <button title="Remove Row" type="button" class="btn btn-sm btn-danger service_row_remover">
                                        <i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if($edit) {
                                foreach ($job_card_services as $job_card_service) {
                                    ?>
                                    <tr>
                                        <td style="width: 90%">
                                            <?= form_dropdown('activity_id', $activities_options, $edit ? $job_card_service->activity_id : '', ' class="form-control searchable" ') ?>
                                        </td>
                                        <td style="width: 10%">
                                            <button title="Remove Row" type="button" class="btn btn-sm btn-danger service_row_remover">
                                                <i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            else
                            {
                                ?>
                                <tr>
                                    <td style="width: 90%">
                                        <?= form_dropdown('activity_id', $activities_options, '', ' class="form-control searchable" ') ?>
                                    </td>
                                    <td style="width: 10%">
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-info service_row_adder">Add Row</button>
                <button type="button" class="btn btn-default btn-sm save_job_card_labour_and_activity">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>