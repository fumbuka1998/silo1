<?php
/**
 * Created by PhpStorm.
 * User: stunna
 * Date: 25/10/2017
 * Time: 14:21
 */
?>
<div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Close Purchase Order</h4>
      </div>
        <form>
        <div class="modal-body">
              <div class="row">
                  <div class="col-xs-12">
                      <div class="form-group col-xs-12">
                          <label for="closing_date" class="control-label">Closing Date</label>
                          <input type="text" class="form-control datepicker" name="closing_date" value="<?= date('Y-m-d') ?>">
                      </div>
                      <div class="form-group col-xs-12">
                          <label for="remarks" class="control-label">Closing Remarks</label>
                          <textarea class="form-control" name="remarks" rows="4"></textarea>
                      </div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" order_id="<?= $order->{$order::DB_TABLE_PK} ?>" class="btn btn-sm btn-default close_purchase_order">Submit</button>
          </div>
        </div>
    </form>

  </div>