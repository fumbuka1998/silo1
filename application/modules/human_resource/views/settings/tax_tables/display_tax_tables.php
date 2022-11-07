<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 21/03/2019
 * Time: 11:04
 */
?>

<?php foreach($tax_table_rates as $tax_table_rate){
    $data['tax_table_rate'] = $tax_table_rate;
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion"
                   target="#collapse"
                   href="#collapse<?php echo $tax_table_rate->id ?>">
                    <span style=" font-style: italic">  <small>Tax Rates from:</small> </span>
                    <span style="color: #3c8dbc; font-weight: bold">
                      <?php echo  strftime(" %d - %b - %Y",strtotime($tax_table_rate->start_date)); ?>
                    </span>&nbsp;&nbsp;&nbsp;
                </a>

                <span class="pull-right">
                    <button data-toggle="modal" data-target="#edit_tax_table<?php echo $tax_table_rate->id ?>"
                            class="btn btn-default btn-xs">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                    <div id="edit_tax_table<?php echo $tax_table_rate->id ?>" class="modal fade edit_tax_table_" role="dialog">
                         <?php $data['tax_rate_items']=$tax_table_rate->tax_rate_items();?>
                         <?php $this->load->view('edit_tax_table_form',$data);?>
                    </div>
                </span>

            </div>
        </div>
        <div id="collapse<?php echo $tax_table_rate->id ?>" class="panel-collapse collapse">
            <div class="panel-body" id="panel<?php echo $tax_table_rate->id?>">
                <div class="tax_table_items">
                    <?php $data['tax_rate_items']=$tax_table_rate->tax_rate_items();?>
                    <?php $this->load->view('settings/tax_tables/tax_rate_items_list',$data);?>
                </div>
            </div>
        </div>
    </div>
<?php }
?>
