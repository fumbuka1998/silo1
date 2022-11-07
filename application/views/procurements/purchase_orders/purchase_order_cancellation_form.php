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
        <h4 class="modal-title">Cancel Purchase Order</h4>
      </div>
        <form>
        <div class="modal-body">
              <div class="row">
                  <div class="col-xs-12">
                      <div class="form-group col-xs-12">
                          <label for="cancellation_date" class="control-label">Cancellation Date</label>
                          <input type="text" class="form-control datepicker" name="cancellation_date" value="<?= date('Y-m-d') ?>">
                      </div>
                      <div class="form-group col-xs-12">
                          <label for="reason" class="control-label">Reason For Cancellation</label>
                          <textarea class="form-control" name="reason" rows="4"></textarea>
                      </div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" order_id="<?= $order->{$order::DB_TABLE_PK} ?>" class="btn btn-sm btn-default cancel_purchase_order">Submit</button>
          </div>
        </div>
    </form>

  </div>