
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="col-xs-12">
                <div class="box-tools pull-right">
                    <button data-toggle="modal" data-target="#new_depreciation_rate_form" class="btn btn-default btn-xs">
                        <i class="fa fa-plus"></i> New
                    </button>
                    <div id="new_depreciation_rate_form" class="modal fade" role="dialog">
                        <?php $this->load->view('add_depreciation_rate_form');?>
                    </div>
                </div>
            </div>
            <!--start Depreciation Rate Display-->
            <div class="row">
                <div class="col-lg-12" id="myrates">
                    <?php if(count($depreciation_rates)>0){?>

                        <div class="panel-group" id="accordion">

                            <?php foreach($depreciation_rates as $depreciation_rate){?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion"  onClick="depreciation_rate_items(this.id)"
                                               href="#collapse<?php echo $depreciation_rate['id']?>" id="<?php echo $depreciation_rate['id']?>">
                                               <span style=" font-style: italic">
                                                   Start Date:
                                               </span>
                                               <span style="color: #3c8dbc; font-weight: bold"> <?php echo  $depreciation_rate['start_date'];?></span>&nbsp;&nbsp;&nbsp;
                                                <span style=" font-style: italic">
                                                   Start Date:
                                               </span>
                                                <span style="color: #3c8dbc; font-weight: bold"> <?php echo  $depreciation_rate['created_at'];?></span>&nbsp;&nbsp;&nbsp;
                                                <span style=" font-style: italic">
                                                   By:
                                               </span>
                                                <span style="color: #3c8dbc;font-weight: bold"> <?php echo  $depreciation_rate['created_by'];?></span>&nbsp;&nbsp;&nbsp;

                                                <!--i class="fa fa-expand"></i-->
                                            </a>
                                        </div>
                                    </div>
                                    <div id="collapse<?php echo $depreciation_rate['id']?>" class="panel-collapse collapse">
                                        <div class="panel-body" id="panel<?php echo $depreciation_rate['id']?>">
                                            anything here: <?php echo $depreciation_rate['id']?>

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else{?>

                        <?php echo "NO DEPRECIATION RATES FOUND";} ?>
                </div>
            </div>

            <!-- end Depreciation Rate Display-->


        </div>
    </div>
</div>
