<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/6/2016
 * Time: 6:31 PM
 */

    $this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">MATERIAL ITEMS LIST</h2>
<table style="font-size: 13px" width="100%">
    <tr>
        <td style="width: 30%">
            <b>Date : </b><?= date('Y-m-d') ?>
        </td>
        <td style="width: 30%">
            <b>Nature : </b><?= isset($project_nature_name) ? $project_nature_name : 'ALL' ?>
        </td>
        <td style="width: 50%">
            <b>Category : </b><?= isset($category_name) ? $category_name : 'ALL' ?>
        </td>
    </tr>
</table>
<br/>
<table style="font-size: 11.5px" width="100%" cellspacing="0" border="1">
    <thead>
        <tr>
            <th>SN</th><th>Item Name</th><th>UOM</th><th>Category</th><th>Nature</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $sn = 0;
        foreach($material_items as $item){
            $sn++;
    ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $item->item_name ?></td>
                <td><?= $item->UOM ?></td>
                <td><?= $item->category_name ?></td>
                <td><?= $item->material_nature ?></td>
            </tr>
    <?php
        }
    ?>
    </tbody>
</table>
<br/>