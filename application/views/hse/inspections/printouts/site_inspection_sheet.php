<?php
$this->load->view('includes/letterhead');
?>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td colspan="2" style="text-align: center">
            <strong><?= nl2br('FIELD LEVEL RISK ASSESSMENT
            (Tathmini ya Hatari mahala pa kazi)') ?></strong>
        </td>
    </tr>
    <tr>
        <td style="text-align: left">
            <strong>SITE ID.: </strong><?= add_leading_zeros($inspections->site()->generated_project_id())?>
        </td>
        <td style="text-align: right">
            <strong>SITE NAME.: </strong><?= $inspections->site()->project_name ?>
        </td>
    </tr>

</table>
<br/>
<br/><hr/>
<table style="font-size: 10px" width="100%" border="1" cellspacing="0" class="table table-bordered table-hover">
    <tr style="background-color: #00aced;">
        <th style="text-align: center"><?= nl2br('DATE AND TIME
        (Tarehe na Muda)')?></th>
        <th style="text-align: center"><?= nl2br('TASK LOCATION
        (Eneo La Kazi)') ?></th>
        <th style="text-align: center"><?= nl2br("SUPERVISOR'S NAME
        (Jina La Msimamizi)") ?></th>
    </tr>
    <tr>
        <td><?= $inspections->inspection_date ?></td>
        <td><?= $inspections->location ?></td>
        <td><?= $inspections->inspector()->full_name() ?></td>
    </tr>
</table>
<hr/>
<br/>
<br/>

<table style="font-size: 10px" width="100%" border="1" cellspacing="0" class="table table-bordered table-hover" style="table-layout: fixed">
    <tr>
        <td width="70%">
            <table style="font-size: 10px" width="100%" border="1" cellspacing="0" class="table table-bordered table-hover" style="table-layout: fixed">
                <tr style="background-color: #00aced; border: 1pt solid black">
                    <th style="width: 03%"></th>
                    <th style="width: 20%; text-align: center"><?= nl2br('SEQUENCE OF TASKS
                    (Mtiririko Wa Kazi)')?></th>
                    <th style="width: 25%; text-align: center"><?= nl2br('PRESENT POTENTIAL HAZARDS
                    (Viashiria Vya Hatari Vilivyopo)') ?></th>
                    <th style="width: 25%; text-align: center"><?= nl2br('CONTROL TO REDUCE/ELIMINATE RISKS
                    (Jinst Ya Kupunguza/Kuondoa Hatari)') ?></th>
                </tr>
                <tbody>
                <?php for($x = 1; $x <= 10 ; $x++) { ?>
                    <tr>
                        <td style="text-align: center"><?= $x ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </td>
        <td>
            <table cellspacing="0" class="table table-bordered table-hover">
                <tr>
                    <td><p>Examine each task to identify hazards and risks that could lead to injuries or damages.</p></td>
                </tr>
                <?php
                foreach ($inspections->inspection_category()->inspection_category_parameters() as $param) {
                    $parameter = $param->category_parameter();
                    ?>
                    <tr>
                        <td>
                            <table>
                                <tr style="background-color: #00aced">
                                    <th style="text-align: left"><?= $parameter->name ?></th>
                                </tr>
                                <tr>
                                    <td>
                                        <?php foreach ($param->inspection_category_parameter_type() as $param_type) {
                                            $type = $param_type->parameter_type();
                                            ?>
                                            <?= $type->name ?>
                                            <?= ($param_type->is_checked == 1) ? '<input type="checkbox"/>' : '<input type="checkbox" checked="true"/>' ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <?php
                }
                ?>

            </table>
        </td>
    </tr>

</table>

<br/>
<br/>
<table>
    <tr style="border: 1px solid black">
        <th style="width: 20%; text-align: left; background-color: #00aced;"><?= nl2br('PPE REQUIRED
        (Vifaa Kinga Vinavyohitajika)')?></th>
        <th style="width: 25%; text-align: left"><input type="checkbox"/> Ear Muffs/Plugs<br><input type="checkbox"/> Reflective Jacket<br/><input type="checkbox"/> Hardhat/Safety Helmet</th>
        <th style="width: 25%; text-align: left"><input type="checkbox"/> Wellington/gum footwear<br><input type="checkbox"/> Dust Mask<br/><input type="checkbox"/> Safety footwear</th>
        <th style="width: 25%; text-align: left"><input type="checkbox"/> Face/Welding shiled<br><input type="checkbox"/> Body Harness compl<br/><input type="checkbox"/> Safety glasses</th>
        <th style="width: 25%; text-align: left"><input type="checkbox"/> Working gloves<br></th>

    </tr>
</table>
<br/>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <br/>
    <br/>
    <tr>
        <td style=" vertical-align: top">
            <strong>Assessed by (Imethibitishwa na) : </strong> <?= $inspections->inspector() ? $inspections->inspector()->full_name() : 'N/A' ?>
        </td>
        <td style=" vertical-align: top">
            <strong><?= ' '.'..........................................................' ?> </strong>
        </td>
        <td style=" vertical-align: top">
            <strong>Date/Tarehe</strong><?= ' '.'....................................................' ?>
        </td>
    </tr>
</table>