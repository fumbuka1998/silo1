<?php
$edit = isset($deployment);
$answers_options = [
    '' => '',
    'YES' => 'YES',
    'NO' => 'NO'
];
?>
<?php $this->load->view('includes/header'); ?>
    <section class="content-header">
        <h1>
            <?= $form_title ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li  class="active"><a href="<?= base_url('hse/deployment')?>">Deployments</a></li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-horizontal">
                                <h4 style="padding-left: 20px;"><?= $edit ? 'Edit For Deployment '.$deployment->name : 'Deployments Registration Form'?></h4>
                                <br/>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label  class="col-sm-3 control-label">Trip Name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="name" class="form-control " value="<?= $edit ? $deployment->name : '' ?>">
                                        <input type="hidden" name="deployment_id" value="<?= $edit ? $deployment->{$deployment::DB_TABLE_PK} : '' ?>">
                                    </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                    <label  class="col-sm-3 control-label">Departure Time:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="departure" class="form-control datetime_picker " value="<?= $edit ? $deployment->departure_time : ''  ?>">
                                    </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                    <label  class="col-sm-3 control-label">Arrival Time:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="arrival_time" class="form-control datetime_picker " value="<?= $edit ? $deployment->arrival_time : ''  ?>">
                                    </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                    <label  class="col-sm-3 control-label">Station:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="relax_station" class="form-control " value="<?= $edit ? $deployment->relax_station : ''  ?>">
                                    </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                    <label  class="col-sm-3 control-label">Vehicle Reg No:</label>
                                    <div class="col-sm-9">
                                        <input type="email" name="registration_number" class="form-control " value="<?= $edit ? $deployment->registration_number : ''  ?>">
                                    </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                    <label  class="col-sm-3 control-label">Driver:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="driver" class="form-control " value="<?= $edit ? $deployment->driver : ''  ?>">
                                    </div>
                                </div>
                                <div class="form-group col-md-12 col-sm-12" id="category_parameter">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th style="width: 30%">Parameter</th><th style="width: 10%">Answer</th><th style="width: 40%">Description</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($category_parameters->num_rows() > 0) {
                                    foreach ($category_parameters->result() as $category_parameter) {
                                        ?>
                                        <tr>
                                            <td style="width: 30%">
                                            <input type="hidden" class="form-control" value="<?= $category_parameter->id ?>" name="category_parameter_id"/>
                                                <span><?= $category_parameter->name ?></span>
                                            </td>
                                            <td style="width: 10%">
                                                <?= form_dropdown('answer', $answers_options, '', ' class="form-control searchable" ') ?>
                                            </td>
                                            <td>
                                                <textarea class="form-control" rows="1" name="description"></textarea>
                                            </td>
                                        </tr>
                                        <?php }
                                    } else {
                                        ?>
                                        <td colspan="3">No Data Found, Please insert Parameter from settings</td>
                                        <?php
                                        }
                                    ?>
                                    </tbody>
                                </table>
                                </div>

                                <div class="col-xs-12" id="paserngers">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th >Persengers</th><th></th>
                                        </tr>
                                        <tr style="display: none" class="persenger_row_template">
                                            <td style="width: 50%">
                                                <input type="text" name="persenger" class="form-control" placeholder="Enter Persenger Name">
                                            </td>
                                            <td style="width: 10%">
                                                <button title="Remove Row" type="button" class="btn btn-sm btn-danger persenger_row_remover">
                                                    <i class="fa fa-close"></i></button>
                                            </td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if(!$edit){
                                        ?>
                                        <tr>
                                            <td style="width: 50%">
                                                <input type="text" name="persenger" class="form-control" placeholder="Enter Persenger Name">
                                            </td>
                                            <td style="width: 10%">

                                            </td>
                                        </tr>
                                        <?php } else {
                                            foreach ($deployment->deployment_persons() as $person){

                                            ?>
                                            <tr>
                                                <td style="width: 50%">
                                                    <input type="text" value="<?= $person->name ?>" name="persenger" class="form-control" placeholder="Enter Persenger Name">
                                                </td>
                                                <td style="width: 10%">
                                                    <button title="Remove Row" type="button" class="btn btn-sm btn-danger persenger_row_remover">
                                                        <i class="fa fa-close"></i></button>
                                                </td>
                                            </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="5">
                                                <button type="button" class="btn btn-default btn-xs persenger_row_adder pull-right">Add Persenger</button>
                                                <span class="pull-right">&nbsp;</span>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?php if($category_parameters->num_rows() > 0) { ?>
                <div class="box-footer">
                    <button type="button" class="btn btn-sm btn-primary pull-right save_deployments">Submit</button>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');