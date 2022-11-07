
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">
                        <!--<button data-toggle="modal" data-target="#new_asset_item_form" class="btn btn-default btn-xs">
                            <i class="fa fa-plus"></i> New
                        </button>
                        <div id="new_asset_item_form" class="modal fade" role="dialog">
                            <?php /*$this->load->view('assets/settings/asset_items/asset_item_form');*/?>
                        </div>-->
                        <a href="<?= base_url('assets/download_asset_items_registration_excel_template') ?>" target="_blank" class="btn btn-default btn-xs">
                            <i class="fa fa-file-excel-o"></i> Excel Template
                        </a>
                        <button data-toggle="modal" data-target="#new_multiple_asset_item_form" class="btn btn-default btn-xs">
                            <i class="fa fa-plus"></i> Add Asset Items
                        </button>
                        <div id="new_multiple_asset_item_form" class="modal fade multiple_asset_form" role="dialog">
                            <?php $this->load->view('assets/settings/asset_items/multiple_asset_item_registration');?>
                        </div>
                    </div>
                    <form class="form-inline">
                        <div class="form-group">
                            <label for="activities_excel">Excel File:  </label>
                            <input type="file" name="asset_registration_excel" class="form-control">
                            <button type="button" excel_type="budget" class="btn btn-default btn-sm upload_asset_registration_excel">Upload Excel</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <table id="asset_items_lists" class="table table-bordered table-hover asset_items_list">
                            <thead>
                                <tr>
                                    <th>Name</th><th>Under</th><th>Part Number</th><th>Description</th><th></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
