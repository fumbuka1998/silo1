<?php
//$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">INSPECTION SHEET</h2>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td style=" width: 30%">
            <strong>Inspection Date: </strong><?= custom_standard_date($inspections->inspection_date) ?>
        </td>
        <td style=" width: 30%">
            <strong>Inspection No.: </strong><?= add_leading_zeros($inspections->{$inspections::DB_TABLE_PK})?>
        </td>
    </tr>

</table>
<br/>
<table style="font-size: 10px" width="100%" cellspacing="0" border="0">
    <thead>
    <tr>

    </tr>
    </thead>
    <tbody>
    <?php
     foreach($inspections->inspection_categories() as $inspection) {
    ?>

    <tr>
        <td width="100%" colspan="5"><b><?= strtoupper($inspection->category()->name) ?></b></td>
    </tr>
   <?php
         $sn = 1;
   foreach($inspection->category_parameters() as $parameter) {
       ?>
    <tr>
        <td width="3%" style="vertical-align: top">&nbsp;</td>
        <td width="3%" style="vertical-align: top"><?= $sn++ ?></td>
        <td width="44%"><?= $parameter->name ?></td>
        <td width="15%">
            YES: <input type="checkbox" name="check2" style=""/>
            NO: <input type="checkbox" name="check2"/>
        </td>
        <td width="35%">

        </td>
    </tr>
 <?php
   }
   ?>
    <tr>
        <td width="100%" colspan="5">&nbsp;</td>
    </tr>
    <?php
     }
     ?>
    </tbody>

</table>
<br/>

<table style="font-size: 12px" width="100%">
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <tr>
        <td style=" vertical-align: top">
            <strong>Remarks: </strong><br/><?= $inspections->description != null ? $inspections->description : 'N/A' ?>
        </td>
    </tr>
</table>
