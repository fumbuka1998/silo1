<?php
/**
 * Created by PhpStorm.
 * User: genesis
 * Date: 6/11/2019 AD
 * Time: 12:14 PM
 */
?>

    <div id="edit_hired_asset<?= $hired_asset->id ?>" class="modal fade hired_asset_form"  role="dialog">
        <?php $this->load->view('assets/hired_assets/hired_asset_form'); ?>
    </div>

<span>
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
             Actions
        </button>
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a data-toggle="modal" data-target="#edit_hired_asset<?= $hired_asset->id ?>"
                        class="btn btn-default btn-xs">
                       <i class="fa fa-edit"></i> Edit
                 </a>
            </li>
            <?php
            if($status == "ACTIVE"){
                ?>
                <li>
                    <a id="#deactivate<?= $hired_asset->id ?>"
                       hired_asset_id="<?= $hired_asset->id ?>"
                       class="btn btn-default btn-xs deactivate_hired_asset">
                           <i class="fa fa-edit"></i> Deactivate
                     </a>
                </li>
                <?php
            } else {
                ?>
                <li>
                    <a id="#activate<?= $hired_asset->id ?>"
                       hired_asset_id="<?= $hired_asset->id ?>"
                       class="btn btn-default btn-xs activate_hired_asset">
                           <i class="fa fa-edit"></i> Activate
                     </a>
                </li>
                <?php
            }
            ?>
          </ul>
    </div>


</span>