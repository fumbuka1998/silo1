<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/1/2016
 * Time: 2:40 AM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">Material Transfer</button>
                    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a data-toggle="modal" data-target="#internal_material_transfer_form" href="#">Internal Transfer</a></li>
                        <li><a  data-toggle="modal" data-target="#external_material_transfer_form" href="#">External Transfer</a></li>
                    </ul>
                </div>

                <div id="internal_material_transfer_form" class="modal fade internal_material_transfer_form" role="dialog">
                    <?php
                            $data['sub_location_options'] = $location->sub_location_options();
                            $this->load->view('inventory/material/internal_material_transfer_form',$data);
                    ?>
                </div>

                <div location_id="<?= $location->{$location::DB_TABLE_PK} ?>" destination_id="" id="external_material_transfer_form" class="modal fade external_material_transfer_form" role="dialog">
                    <?php
                        $this->load->view('inventory/material/external_material_transfer_form',$data);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table id="location_material_transfers_table" location_id="<?= $location->{$location::DB_TABLE_PK} ?>" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Type</th><th>Transfer Date</th><th>Transfer No.</th><th>From</th><th>To</th><th>Status</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
