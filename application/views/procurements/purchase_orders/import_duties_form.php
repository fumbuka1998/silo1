<?php

/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/12/2017
 * Time: 11:15 PM
 */
?>
<div class="form-group col-md-3">
    <label for="freight" class="control-label">Freight</label>
    <input type="text" class="form-control number_format" required name="freight" value="<?= $edit ? $grn->purchase_order_grn()->freight : $order->freight ?>">
</div>

<div class="form-group col-md-3">
    <label for="insurance" class="control-label">Insurance</label>
    <input type="text" class="form-control number_format" required name="insurance" value="<?= $edit ? $grn->purchase_order_grn()->insurance : 0 ?>">
</div>

<div class="form-group col-md-3">
    <label for="other_charges" class="control-label">Inspection & Other Charges</label>
    <input type="text" class="form-control number_format" required name="other_charges" value="<?= $edit ? $grn->purchase_order_grn()->other_charges : $order->inspection_and_other_charges ?>">
</div>

<div class="form-group col-md-3">
    <label for="import_duty" class="control-label">Import Duty (TSH)</label>
    <input type="text" class="form-control number_format" required name="import_duty" value="<?= $edit ? $grn->purchase_order_grn()->import_duty : 0 ?>">
</div>

<div class="form-group col-md-3">
    <label for="vat" class="control-label">VAT (TSH)</label>
    <input type="text" class="form-control number_format" required name="vat" value="<?= $edit ? $grn->purchase_order_grn()->vat : '' ?>">
</div>


<div class="form-group col-md-3">
    <label for="cpf" class="control-label">CPF (TSH)</label>
    <input type="text" class="form-control number_format" required name="cpf" value="<?= $edit ? $grn->purchase_order_grn()->cpf : 0 ?>">
</div>


<div class="form-group col-md-3">
    <label for="rdl" class="control-label">RDL (TSH)</label>
    <input type="text" class="form-control number_format" required name="rdl" value="<?= $edit ? $grn->purchase_order_grn()->rdl : 0 ?>">
</div>

<div class="form-group col-md-3">
    <label for="rdl" class="control-label">Wharfage (TSH)</label>
    <input type="text" class="form-control number_format" required name="wharfage" value="<?= $edit ? $grn->purchase_order_grn()->wharfage : 0 ?>">
</div>

<div class="form-group col-md-3">
    <label for="rdl" class="control-label">Service Fee (TSH)</label>
    <input type="text" class="form-control number_format" required name="service_fee" value="<?= $edit ? $grn->purchase_order_grn()->service_fee : 0 ?>">
</div>

<div class="form-group col-md-3">
    <label for="clearance_charges" class="control-label">Clearance Charges (TSH)</label>
    <input type="text" class="form-control number_format" required name="clearance_charges" value="<?= $edit ? $grn->purchase_order_grn()->clearance_charges : 0 ?>">
</div>

<div class="form-group col-md-3">
    <label for="clearance_vat" class="control-label">Clearance VAT (TSH)</label>
    <input type="text" class="form-control number_format" required name="clearance_vat" value="<?= $edit ? $grn->purchase_order_grn()->clearance_vat : 0 ?>">
</div>

<div class="form-group col-md-3">
    <label for="factor" class="control-label">Factor</label>
    <input readonly type="text" class="form-control" required name="factor" value="<?= $edit ? $grn->purchase_order_grn()->factor : 1 ?>">
</div>