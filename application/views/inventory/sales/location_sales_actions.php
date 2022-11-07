
<?php
$sale_number = $sale->{$sale::DB_TABLE_PK};

?>

<span class="pull-right">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
            <i class="fa fa-eye"></i> Preview
        </button>
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a class="btn btn-default btn-xs" target="_blank" href="<?= base_url('inventory/preview_stock_sale/delivery_form/'.$sale_number) ?>">
                    Delivery Form
                </a>
            </li>
            <li>
                <a target="_blank" href="<?= base_url('inventory/preview_stock_sale/stock_sales_sheet/'.$sale_number)?>"
                   class="btn btn-xs btn-default">
                    Sale Sheet
                </a>
            </li>
        </ul>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
            <i class=""></i> Action
        </button>
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <?php if($sale->created_by == $this->session->userdata('employee_id') || check_permission('Administrative Actions')){ ?>
            <li>
                <a data-toggle="modal" data-target="#edit_sale_record_<?= $sale->{$sale::DB_TABLE_PK}?>" class="btn btn-default btn-xs">
                    <i class="fa fa-edit"></i> Edit
                </a>

            </li>
                <li>
                    <a style="color: white;"class="btn btn-danger btn-xs delete_sales_record" sale_id="<?= $sale->{$sale::DB_TABLE_PK}?>">
                        <i class="fa fa-trash"></i>Delete
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>

    <div id="edit_sale_record_<?= $sale->{$sale::DB_TABLE_PK}?>"  class="modal fade location_sales_form" role="dialog">
        <?php
        $data['sales'] = $sale_number;
        $this->load->view('inventory/sales/stock_sales_form',$data);
        ?>
    </div>
</span>
