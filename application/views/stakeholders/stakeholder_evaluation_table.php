<?php

$print = isset($print);

?>

<h3><strong>Project:  </strong><?= $project->project_name ?></h3>

<table class="table table-bordered table-responsive multiple_evaluation_table table-hover" <?php if($print){ ?> style="font-size: 12px" width="100%" border="1" cellspacing="0" <?php } ?> >
    <thead>
    <tr style="background: #8792ab">
        <td><?php if($print){ echo '<h4><strong> DESCRIPTIONS:</strong></h4>';}else{ echo '<h5><strong> DESCRIPTIONS:</strong></h5>';}  ?></td>
        <?php
        foreach ($contractors_data as $data) {
            ?>
            <td><strong><?= $data[6] ?></strong></td>
            <?
        }
        ?>


    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="col-md-8 col-xs-12">General experience of the company in the field at least 3 years minimum: <strong>(15 points)</strong></td>
        <?php
        foreach ($contractors_data as $data) {
            ?>
            <td style="text-align: right"><h5><?= $data[0].' %' ?></h5></td>
            <?
        }
        ?>
    </tr>
    <tr>
        <td>At least two (2) certificates of completion issued by the recognized institutions: <strong>(20 points)</strong></td>
        <?php
        foreach ($contractors_data as $data) {
            ?>
            <td style="text-align: right"><h5><?= $data[1].' %' ?></h5></td>
            <?
        }
        ?>
    </tr>
    <tr>
        <td>Two (2) team supervisors with at least a bachelor's degree in management or any other related field: <strong>(30 points)</strong></td>
        <?php
        foreach ($contractors_data as $data) {
            ?>
            <td style="text-align: right"><h5><?= $data[2].' %' ?></h5></td>
            <?
        }
        ?>
    </tr>
    <tr>
        <td>Financial capacity of at least payment of workers for 1 month salary: <strong>(5 points)</strong></td>
        <?php
        foreach ($contractors_data as $data) {
            ?>
            <td style="text-align: right"><h5><?= $data[3].' %' ?></h5></td>
            <?
        }
        ?>
    </tr>
    <tr>
        <td>Proof of traning of the casual labourers in constructuin related fields/traning in water infrastructure as an added advantage: <strong>(30 points)</strong></td>
        <?php
        foreach ($contractors_data as $data) {
            ?>
            <td style="text-align: right"><h5><?= $data[4].' %' ?></h5></td>
            <?
        }
        ?>
    </tr>

    <tr style="background: #aabad9">
        <td><strong>TOTAL POINTS (100%)</strong></td>
        <?php
        foreach ($contractors_data as $data) {
            ?>
            <td style="text-align: right"><h5><strong><?= $data[5].' %' ?></strong></h5></td>
            <?
        }
        ?>
    </tr>
    </tbody>
</table>