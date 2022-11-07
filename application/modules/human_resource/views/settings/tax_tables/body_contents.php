<div class="row">
    <div class="col-xs-12">
         <div class="panel-group" id="accordion">
             <?php foreach($tax_table_rates as $tax_table_rate){?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion"  target="#collapse" href="#collapse<?php echo $tax_table_rate->id ?>">
                        <span style=" font-weight: bold">
                            <small>Tax Rates&nbsp;&nbsp;&nbsp;From: &nbsp;&nbsp;&nbsp;&nbsp;</small>
                        </span>
                        <span style="color: #3c8dbc; font-weight: bold">
                            <?php echo  strftime(" %d - %b - %Y",strtotime($tax_table_rate->start_date)); ?>
                        </span>&nbsp;
                        <span style="  font-weight: bold">
                            <small>&nbsp;&nbsp;&nbsp;&nbsp;To &nbsp;&nbsp;&nbsp;&nbsp;</small>
                        </span>
                        <span style="color: #3c8dbc; font-weight: bold">
                            <?php echo  strftime(" %d - %b - %Y",strtotime($tax_table_rate->end_date)); ?>
                        </span>&nbsp;&nbsp;&nbsp;
                    </a>
                </div>
            </div>
            <div id="collapse<?php echo $tax_table_rate->id ?>" class="panel-collapse collapse">

                <div class="panel-body" id="panel<?php echo $tax_table_rate->id ?>">

                    <span class="pull-right" style="margin: 0 0 10px 0 ">

                        <button data-toggle="modal" data-target="#edit_tax_table<?php echo $tax_table_rate->id ?>" class="btn btn-default btn-xs">
                            <i class="fa fa-edit"></i> Edit
                       </button>
                        <div id="edit_tax_table<?php echo $tax_table_rate->id ?>" class="modal fade edit_tax_table_" role="dialog">
                            <?php $data['tax_table_rate']=$tax_table_rate;?>
                            <?php $this->load->view('edit_tax_table_form',$data);?>
                       </div>

                        <button class="btn btn-danger btn-xs delete_tax_table" delete_tax_table_id="<?= $tax_table_rate->{$tax_table_rate::DB_TABLE_PK} ?>"  >
                            <i class="fa fa-trash"></i> Delete
                        </button>

                    </span>

                    <div tax_table_rate_id="<?= $tax_table_rate->{$tax_table_rate::DB_TABLE_PK} ?>" class="tax_rate_items_table">

                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        </div>
     </div>
</div>