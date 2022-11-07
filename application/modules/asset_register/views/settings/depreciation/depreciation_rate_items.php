
    <?php
        $depreciation_rate_id = $depreciation_rate->{$depreciation_rate::DB_TABLE_PK};
    ?>
    <span class="pull-right" style="margin: 0 0 10px 0 ">

            <button data-toggle="modal" data-target="#edit_depreciation_rate_<?= $depreciation_rate_id ?>" class="btn btn-default btn-xs">
                <i class="fa fa-edit"></i> Edit
           </button>
            <div id="edit_depreciation_rate_<?=$depreciation_rate_id?>" class="modal fade depreciation_rate_form " role="dialog">
                <?php $data['depreciation_rate']=$depreciation_rate;?>
                <?php $this->load->view('depreciation_rate_form',$data);?>
            </div>

            <button class="btn btn-danger btn-xs delete_depreciation_rate" delete_depreciation_rate_id="<?= $depreciation_rate_id ?>"  >
                <i class="fa fa-trash"></i> Delete
            </button>

    </span>
    <?php if(count($depreciation_rate_items)>0) {?>

     <table class="table table-bordered table-hover table-striped">
        <thead>
            <tr>
                <th>Asset Group</th>
                <th>Depreciation Rate(%)</th>
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

