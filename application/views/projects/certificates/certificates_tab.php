<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 11/04/2018
 * Time: 08:44
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_certificate" class="btn btn-default btn-xs">
                    Add Certificate
                </button>
                <div id="new_certificate" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('projects/certificates/project_certificate_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="project_certificate_list" project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover ">
                    <thead>
                        <tr>
                            <th>Cert. No.</th><th>Cert. Date</th><th>Certified Amount</th><th>Paid Amount</th><th></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="2">TOTAL</th>
                            <th class="total_certified_amount" style="text-align: right"></th>
                            <th class="total_paid_amount" style="text-align: right"></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
