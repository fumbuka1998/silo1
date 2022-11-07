
<div class="row">
    <div class="col-xs-12">
        <div class="panel-group " id="accordion">
        <?php if(count($depreciation_rates)>0) {  ?>
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
                        </div>
                     </div>
                    <div id="collapse<?php echo $depreciation_rate->id ?>" class="panel-collapse collapse">
                        <div class="panel-body" id="panel<?php echo $depreciation_rate->id?>">
                            <div class="depreciation_rate_items_table" depreciation_rate_id="<?= $depreciation_rate->{$depreciation_rate::DB_TABLE_PK} ?> ">
                            </div>
                        </div>
                    </div>
                 </div>
            <?php } ?>

            <?php } else{ ?>
            
        <p><h4 class="text-center"> NO RATES FOUND</h4></p>

            <?php } ?>

        </div>
    </div>
</div>
