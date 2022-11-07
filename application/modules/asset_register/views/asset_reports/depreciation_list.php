     <?php if(count($asset_list)){?>

      

                 <div class="row" <?php if(isset($print)){ ?>  style="display: none;" <?php } ?> >
                        <div class="col-md-12">
                            <a href="<?= base_url('asset_register/Asset_reports/asset_deprecition_report_print_preview/'.$issue_date.'/'.$asset_group_id.'/'.$sub_location_id);?>" target="_blank"> 

                              <button class="btn btn-xs btn-default pull-right">
                                  <i class="fa fa-print"></i> Print
                              </button>
                            </a>
                         </div>
                  </div>
                  <br>
      

      <div class="col-xs-12 table-responsive">
          <table <?php if(isset($print)){ ?> style="font-size: 12px" width="100%" border="1" cellspacing="0"  <?php } ?> class="table table-bordered table-hover table-striped">
              <thead>
                <tr>
                    <th>S/N</th>
                    <th>Asset Name/Code</th>
                    <th style="width: 20%">Asset Group</th>
                    <th>Registration Date</th>
                    <th>Acquisition Value</th>
                    <th>Days passed</th>
                    <th>Rate(%)</th>
                    <th>Depreciation</th>
                    <th>Book Value</th>
                </tr>
              </thead>


                 <?php $i=1; 

                  $total_book_value=0;
                  $total_depreciation=0;
                  $total_current_value=0;

                  foreach($asset_list as $result){?>
                    <tr>
                    <td><?php echo $i;$i++;?></td>
                    <td><?php echo $result['asset_name']?></td>
                    <td><?php echo $result['group_name']?></td>
                    <td><?php echo strftime(" %d - %b - %Y",strtotime($result['registration_date']));?></td>
              
                    <td style="text-align:right;"><?php 

                    $total_book_value=$total_book_value+$result['book_value'];

                    echo number_format($result['book_value'])?></td>

                    <td><?php  echo $result['days_passed'];?></td>
                    
                    <td><?php echo $result['depreciation_rate'];?></td>
                    
                    <td style="text-align:right;">

                   <?php 

                     $total_depreciation=$total_depreciation+$result['depreciation'];

                   echo number_format($result['depreciation']);?>
                      

                    </td>
                    <td style="text-align:right;"><?php echo number_format($result['current_value']);?></td>

                 <?php }?>

                  <tr>

                    <td></td>
                    <td>TOTAL</td>
                    <td></td>
                    <td></td>
                    <td style="text-align:right;"><?php echo number_format($total_book_value);?></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:right;"><?php echo number_format($total_depreciation);?></td>
                    <td style="text-align:right;"><?php echo number_format($total_book_value-$total_depreciation);?></td>
                  </tr>

             </table>
         </div>

         <?php }else{?>

              <p style="text-align: center;">NO RECORDS FOUND</p>

         <?php } ?>