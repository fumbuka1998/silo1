<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/26/2018
 * Time: 11:01 PM
 */

$client = $tender->client();
?>

<div class="active tab-pane" id="client_details">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#edit_form"
                                    class="btn btn-default btn-xs">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <a class="btn btn-default btn-xs" target="_blank" href="<?= base_url('tenders/export_tender_to_excel/' . $tender->{$tender::DB_TABLE_PK}) ?>">
                                <i class="fa fa-file-excel-o"></i> Export To Excel
                            </a>
                            <div id="edit_form" class="modal fade" tabindex="-1" role="dialog">
                                <?php $this->load->view('tenders/tender_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-horizontal">

                            <div class="form-group col-md-4 col-sm-6">
                                <label  class="col-sm-5 control-label">Tender Name:</label>
                                <div class="form-control-static col-sm-7">
                                    <?= $tender->tender_name ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-6">
                                <label  class="col-sm-5 control-label">Project Category:</label>
                                <div class="form-control-static col-sm-7">
                                    <?= $tender->category()->category_name ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-6">
                                <label  class="col-sm-5 control-label">Client:</label>
                                <div class="form-control-static col-sm-7">
                                    <?= !empty($client) ? (check_permission('Clients') ? anchor(base_url('clients/profile/'.$client->{$client::DB_TABLE_PK}),$client->client_name) : $client->client_name) : 'N/A'; ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-6">
                                <label  class="col-sm-5 control-label">Date Announced:</label>
                                <div class="form-control-static col-sm-7">
                                    <?= custom_standard_date($tender->date_announced) ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-6">
                                <label  class="col-sm-5 control-label">Submission Deadline:</label>
                                <div class="form-control-static col-sm-7">
                                    <?= custom_standard_date($tender->submission_deadline) ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-6">
                                <label  class="col-sm-5 control-label">Date Procured:</label>
                                <div class="form-control-static col-sm-7">
                                    <?=custom_standard_date($tender->date_procured) ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-6">
                                <label  class="col-sm-5 control-label">Procurement Cost:</label>
                                <div class="form-control-static col-sm-7">
                                    <?= number_format($tender->procurement_cost,2) ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-6">
                                <label  class="col-sm-5 control-label">Currency:</label>
                                <div class="form-control-static col-sm-7">
                                    <?= $tender->procurement_currency()->symbol ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-6">
                                <label  class="col-sm-5 control-label">Supervisor:</label>
                                <div class="form-control-static col-sm-7">
                                    <?= $tender->supervisor()->full_name() ?>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
