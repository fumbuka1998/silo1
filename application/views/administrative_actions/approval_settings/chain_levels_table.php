 <?php if(count($module_chain_levels)>0){?>

 <table class="table table-bordered table-hover table-striped">
    <thead>
        <tr>
            <th>Postion</th><th>Label</th>Type<th></th><th>Actions</th>
        </tr>
    </thead>

      <?php foreach($module_chain_levels as $module_chain_level){?>

             <tr>
                 
                 <td><?= $module_chain_level->level_name;?></td>
                 <td><?= $module_chain_level->label;?></td>
                 <td><?= $module_chain_level->special_level == 1 ? 'Special' : 'Normal'?></td>
                 <td>

                    <button class="btn btn-danger btn-xs delete_chain_level" approval_chain_level_id="<?= $module_chain_level->id;?>">
                      <i class="fa fa-trash"></i> Delete
                    </button>
                    <?php if($module_chain_level->status=='active'){?>
                    <button class="btn btn-danger btn-xs disable_chain_level" approval_chain_level_id="<?= $module_chain_level->id;?>">
                      <i class="fa fa-close"></i> Disable
                    </button>
                    <?php } else{?>
                     <button class="btn btn-success btn-xs enable_chain_level" approval_chain_level_id="<?= $module_chain_level->id;?>">
                      <i class="fa fa-check"></i> Enable
                    </button>
                    <?php } ?>

                 </td>

             </tr>

        <?php } ?>

  </table>
  <?php }else{?>

     <p style="text-align:center;">NO APPROVAL CHAIN DEFINED</p>

   <?php } ?>