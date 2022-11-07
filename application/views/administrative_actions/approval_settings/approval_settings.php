<?php $this->load->view('includes/header');

$edit = isset($module);

?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Approval Settings
        <!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('administrative_actions') ?>"><i class="fa fa-support"></i>Administrative Actions</a></li>
        <li class="active">Approval Settings</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box" <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel-group" id="accordion">
                            <?php foreach ($approval_modules as $module) {
                                //echo  $row->greatest_chain_level()->level;
                            ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" target="#collapse" href="#collapse<?php echo $module->id ?>">
                                                <span style=" font-style: italic">
                                                    <small></small>
                                                </span>
                                                <span style="color: #3c8dbc; font-weight: bold"> <?php echo  $module->module_name; ?></span>&nbsp;&nbsp;&nbsp;


                                                <!--i class="fa fa-expand"></i-->
                                            </a>
                                        </div>
                                    </div>
                                    <div id="collapse<?= $module->id ?>" class="panel-collapse collapse">
                                        <div class="panel-body" id="panel<?php echo $module->id ?>">
                                            <!--next form-->
                                            <div class="col-xs-12 next_form">
                                                <form>
                                                    <div class="form-group col-sm-2" style="margin-top: 20px; margin-right: -40px">
                                                        <h4>Add Approval chain</h4>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <label for="after_level" class="control-label">Level Position</label>
                                                        <?= form_dropdown('after_level', [], '', ' class="form-control searchable" ') ?>
                                                    </div>
                                                    <div class="form-group  col-md-2">
                                                        <input type="hidden" name="approval_module_id" value="<?= $module->id; ?>" class="form-control input-sm" required>
                                                        <?= form_label('Level Name', 'level_name'); ?>
                                                        <input name="level_name" class="form-control">
                                                    </div>
                                                    <div class="form-group  col-md-2">
                                                        <?= form_label('Label', 'approval_label'); ?>
                                                        <select name="approval_label" class="form-control searchable">
                                                            <option value="Checked">Checked</option>
                                                            <option value="Verified">Verified</option>
                                                            <option value="Approved">Approved</option>
                                                            <option value="Certified">Certified</option>
                                                            <option value="Authorized">Authorized</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group  col-md-2">
                                                        <?= form_label('Change Source', 'change_source'); ?>
                                                        <select name="change_source" class="form-control searchable">
                                                            <option value="1">YES</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group  col-md-1" style="padding: 3rem 0.1rem 0.2rem;">
                                                        <input type="checkbox" name="is_special_level">
                                                        <?= form_label('Special Level', 'is_special_level'); ?>
                                                    </div>
                                                    <div class="form-group  col-md-1">
                                                        <button type="button" class="btn btn-default btn-sm save_approval_chain" style="margin-top: 25px">
                                                            Save
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                            <!--end -->
                                            <div approval_module_id="<?= $module->{$module::DB_TABLE_PK} ?>" class="chain_levels_table">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php $this->load->view('includes/footer');
