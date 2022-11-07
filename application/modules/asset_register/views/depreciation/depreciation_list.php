<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/15/2016
 * Time: 5:47 PM
 */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">
                        <button data-toggle="modal" data-target="#depreciation_form" class="btn btn-default btn-xs">
                            <i class="fa fa-plus"></i> New
                        </button>
                        <div id="depreciation_form" class="modal fade" role="dialog">
                            <?php $this->load->view('depreciation/depreciation_rate_form');?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">

                        <div class="panel-group" id="accordion">

                            <?php foreach($depreciation_rates as $depreciation_rate){?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion"
                                               target="#collapse"
                                               href="#collapse<?php echo $depreciation_rate->id ?>">
                                               <span style=" font-style: italic">
                                                  <small>Rates from:</small>
                                               </span>
                                                <span style="color: #3c8dbc; font-weight: bold"> <?php echo  strftime(" %d - %b - %Y",strtotime($depreciation_rate->start_date)); ?></span>&nbsp;&nbsp;&nbsp;
                                            </a>

                                               <span  class="pull-right">

                                                   <button data-toggle="modal" data-target="#edit_depreciation_rate_<?php echo $depreciation_rate->id?>" class="btn btn-xs btn-default">
                                                           <i class="fa fa-edit"></i>Edit</button>
                                                   </button>
                                                   <div id="edit_depreciation_rate_<?php echo $depreciation_rate->id ?>" class="modal fade" role="dialog">
                                                       <?php $this->load->view('depreciation/depreciation_rate_form')?>
                                                   </div>

                                                   <button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i>Delete</button>
                                               </span>


                                        </div>
                                    </div>
                                    <div id="collapse<?php echo $depreciation_rate->id ?>" class="panel-collapse collapse">
                                    <div class="panel-body" id="panel<?php echo $depreciation_rate->id?>">

                                             <div class="chain_levels_table">

                                                <?php $data['depreciation_rate_items']=$depreciation_rate->depreciation_rate_items();?>

                                                        <?php $this->load->view('depreciation/depreciation_rate_items',$data);?>
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
