 <?php if(count($depreciation_rate_items)>0){?>

 <table class="table table-bordered table-hover table-striped">
    <thead>
        <tr>
                <th>Asset Group</th>
                <th>Rate(%)</th>
        </tr>
    </thead>

      <?php foreach($depreciation_rate_items as $depreciation_rate_item){?>

             <tr>
                 
                 <td><?= $depreciation_rate_item->asset_group()->group_name;?></td>
                 <td><?= $depreciation_rate_item->rate;?></td>

             </tr>

        <?php } ?>

  </table>
  
  <?php }else{?>

     <p style="text-align:center;">NO ITEMS FOUND</p>

   <?php } ?>