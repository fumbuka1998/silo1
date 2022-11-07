
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">
                        <button data-toggle="modal" data-target="#tax_table_form" class="btn btn-default btn-xs">
                            <i class="fa fa-plus-circle"></i> New Tax Rates
                        </button>
                        <div id="tax_table_form" class="modal fade tax_table_form" role="dialog">
                            <?php $this->load->view('settings/tax_tables/tax_table_form');?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel-group" id="taxtable_accordion">
                        </div>
                    </div>
                </div>
            </div>
        </div>

