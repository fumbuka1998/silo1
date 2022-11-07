<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= $contractor->contractor_name ?>
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('contractors')?>"><i class="fa fa-shopping-cart"></i>Contractors</a></li>
        <li><a href="<?= base_url('contractors/contractors_list')?>"><i class="fa fa-list"></i>Contractors List</a></li>
        <li class="active"><?= $contractor->contractor_name ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#contractors_details" data-toggle="tab">Contractor Details</a></li>
                    <li><a href="#sub_contracts" data-toggle="tab">Sub-Contracts</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="contractors_details">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <div class="col-xs-12">
                                            <div class="box-tools pull-right">
                                                <?php $contractor_id=$contractor->{$contractor::DB_TABLE_PK} ?>
                                                <button data-toggle="modal" data-target="#edit_form_<?= $contractor_id ?>"
                                                        class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <div id="edit_form_<?= $contractor_id ?>" class="modal fade" tabindex="-1" role="dialog">
                                                    <?php $data['Contractor']=$contractor; ?>
                                                    <?php $this->load->view('contractors/contractor_form',$data); ?>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="form-horizontal">

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Name:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $contractor->contractor_name ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Phone:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $contractor->phone ? $contractor->phone : 'N/A' ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Alt. Phone:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $contractor->alternative_phone ? $contractor->alternative_phone : 'N/A' ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Email:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $contractor->email ? $contractor->email : 'N/A' ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Address:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $contractor->address ? nl2br($contractor->address) : 'N/A' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="container-fluid pull-left col-xs-12">
                                        <div class="margin">
                                            <a href="#"  data-toggle="collapse" data-target="#evaluation_div"><strong>Contractors Evaluation</strong></a>
                                            <div id="evaluation_status" class="pull-right">
                                               <?php if($contractor_evaluation_factors){
                                                   ?>
                                                   <span style="color: #06d604"><strong>Evaluated</strong></span>
                                                   <?php
                                               }else{
                                                   ?>
                                                   <i style="color: red" class="fa fa-warning"></i><span style="color: slategrey"><strong> Not Evaluated</strong></span>
                                                   <?php
                                               } ?>
                                            </div>
                                        </div>

                                        <br/>

                                        <div id="evaluation_div" class="collapse col-xs-12">
                                            <table class="table table-bordered table-responsive contractor_evaluation">
                                                <tbody>
                                                <input type="hidden" name="contractor_id" value="<?= $contractor->id ?>">
                                                <tr>
                                                    <td>1</td>
                                                    <td class="col-md-8 col-xs-12">General experience of the company in the field at least 3 years minimum: <strong>(15 points)</strong></td>
                                                    <td style="">
                                                        <?= form_dropdown('general_experience', $enum_options->get_enum_values('general_experience'), $contractor_evaluation_factors != '' ? $contractor_evaluation_factors['evaluation_factor'][0] : '', "  class = ' form-control searchable' "); ?>
                                                    </td>
                                                    <td>
                                                        <h5 id="general_experience_points"><?= $contractor_evaluation_factors != '' ? $contractor_evaluation_factors['evaluation_point'][0] : 0?> %</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>At least two (2) certificates of completion issued by the recognized institutions: <strong>(20 points)</strong></td>
                                                    <td>
                                                        <?= form_dropdown('certificates_of_comletion', $enum_options->get_enum_values('certificate_of_completion'), $contractor_evaluation_factors != '' ? $contractor_evaluation_factors['evaluation_factor'][1] : '', "  class = ' form-control searchable' "); ?>
                                                    </td>
                                                    <td>
                                                        <h5 id="certificates_of_comletion_points"><?= $contractor_evaluation_factors != '' ? $contractor_evaluation_factors['evaluation_point'][1] : 0?> %</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>Two (2) team supervisors with at least a bachelor's degree in management or any other related field: <strong>(30 points)</strong></td>
                                                    <td>
                                                        <?= form_dropdown('team_supervisors', $enum_options->get_enum_values('two_team_supervisors_with_atleast_a_bachelor_degree'), $contractor_evaluation_factors != '' ? $contractor_evaluation_factors['evaluation_factor'][2] : '', "  class = ' form-control searchable' "); ?>
                                                    </td>
                                                    <td>
                                                        <h5 id="team_supervisors_points"><?= $contractor_evaluation_factors != '' ? $contractor_evaluation_factors['evaluation_point'][2] : 0?> %</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>Financial capacity of at least payment of workers for 1 month salary: <strong>(5 points)</strong></td>
                                                    <td>
                                                        <?= form_dropdown('financial_capacity', $enum_options->get_enum_values('financial_capacity_of_at_least_payment_of_workers_for_one_month'), $contractor_evaluation_factors != '' ? $contractor_evaluation_factors['evaluation_factor'][3] : '', "  class = ' form-control searchable' "); ?>
                                                    </td>
                                                    <td>
                                                        <h5 id="financial_capacity_points"><?= $contractor_evaluation_factors != '' ? $contractor_evaluation_factors['evaluation_point'][3] : 0?> %</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>5</td>
                                                    <td>Proof of traning of the casual labourers in constructuin related fields/traning in water infrastructure as an added advantage: <strong>(30 points)</strong></td>
                                                    <td>
                                                        <?= form_dropdown('casual_laborers', $enum_options->get_enum_values(), $contractor_evaluation_factors != '' ? $contractor_evaluation_factors['evaluation_factor'][4] : '', "  class = ' form-control searchable' "); ?>
                                                    </td>
                                                    <td>
                                                        <h5 id="casual_laborers_points"><?= $contractor_evaluation_factors != '' ? $contractor_evaluation_factors['evaluation_point'][4] : 0?> %</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"><strong>TOTAL POINTS (100%)</strong></td>
                                                    <td>
                                                        <h5 id="total_points"><strong><?= $contractor_evaluation_factors != '' ? $contractor_evaluation_factors['total_points'] : 0?> %</strong></h5>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="pull-right">
                                                <button class="button btn-default btn-xs save_subcontractor_evaluation">Submit</button>
                                            </div>
                                        </div>
                                    </div>





                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane"   id="sub_contracts">
                        <?php $this->load->view('contractors/sub_contracts_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');