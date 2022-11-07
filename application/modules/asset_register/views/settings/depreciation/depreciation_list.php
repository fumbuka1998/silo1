
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#depreciation_rate_form" class="btn btn-default btn-xs">
                                <i class="fa fa-plus-circle"></i> &nbsp; New Rate
                            </button>
                            <div id="depreciation_rate_form" class="modal fade" role="dialog">
                                <?php $this->load->view('depreciation/depreciation_rate_form');?>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="box-body">

                    <div id="depreciation_rate_table">

                    </div>
                </div>
            </div>
        </div>
    </div>
