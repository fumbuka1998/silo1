<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/14/2016
 * Time: 8:52 AM
 */
?>
<div class="box">
    <div class="box-header">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <?php
                $this->domain_name = $this->config->item('domain_name');
                if($this->domain_name == "epm_gnc"){ ?>
                <button data-toggle="modal" data-target="#site_grn_form" class="btn btn-xs btn-default">
                    <i class="fa fa-plus"></i><span data-toggle="tooltip" title="The Delivery of site material and equipments that comes from the client">Delivery</span>
                </button>
                <div id="site_grn_form" class="modal fade" role="dialog">
                    <?php $this->load->view('inventory/locations/site_grns/site_grn_form'); ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="location_grns_table" location_id="<?= $location->{$location::DB_TABLE_PK} ?>" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th><?= $is_site_location ? 'Delivery' : 'GRN' ?> Date</th><th><?= $is_site_location ? 'Delivery' : 'GRN' ?> No</th><th>From</th><th>Reference</th><th>Comments</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
