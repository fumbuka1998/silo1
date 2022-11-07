<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/27/2018
 * Time: 12:07 AM
 */
if(!empty($tender_components)){
    foreach ($tender_components as $tender_component){
        $component_id = $tender_component->{$tender_component::DB_TABLE_PK};
?>
        <div class="box collapsed-box">
            <div class="box-header with-border bg-gray-light">
                <h3 class="box-title collapse-title"  data-widget="collapse"><?= $tender_component->component_name ?></h3>
                <?php if($tender_component->created_by == $this->session->userdata('employee_id')){ ?>
                <div class="box-tools pull-right">
                    <button data-toggle="modal" data-target="#edit_component_<?= $component_id ?>"
                            class="btn btn-xs btn-default">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                    <div id="edit_component_<?= $component_id ?>" class="modal fade" tabindex="-1"
                         role="dialog">
                        <?php
                            $this->load->view('tenders/profile/components/component_form',['tender_component'=>$tender_component]);
                        ?>
                    </div>
                    <button class="btn btn-danger btn-xs delete_tender_component" component_id="<?= $component_id ?>">
                        <i class="fa fa-trash-o"></i> Delete
                    </button>
                </div>
                <?php } ?>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <?php
                            $this->load->view('tenders/profile/components/components_profile',['component_id'=>$component_id]);
                        ?>
                    </div>
                </div>
            </div><!-- /.box-body -->
        </div>

<?php }
}else{?>
    <div style="text-align: center" class="alert alert-info">No Component found for this Tender</div>
<?php }?>

