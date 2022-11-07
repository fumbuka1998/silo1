<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/9/2018
 * Time: 10:15 AM
 */

?>

<table  <?php if($print){ ?> style="font-size: 11px" width="100%" border="1" cellspacing="0"  <?php } else { ?> class="table table-bordered table-hover" <?php } ?> >
    <thead>
    <tr>
        <th>SN</th><th>Asset Name</th><th>Location</th><?php if(!$filtered){ ?><th>Quantity</th><?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $sn = 0;
    foreach ($table_items as $item){
        $sn++;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $item['asset_name'] ?></td>
            <td><?= $item['location_name'] ?></td>
            <?php if(!$filtered){ ?>
            <td><?= $item['quantity'] ?></td>
            <?php } ?>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
