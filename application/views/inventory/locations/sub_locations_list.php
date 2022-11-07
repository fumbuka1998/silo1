<?php

/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/13/2016
 * Time: 2:25 PM
 */

if (!empty($sub_locations)) {
    foreach ($sub_locations as $sub_location) {
        $sub_location_id = $sub_location->{$sub_location::DB_TABLE_PK};
        $data['sub_location'] = $sub_location;
        ?>
        <div class="box collapsed-box ">
            <div class="box-header with-border bg-gray-light">
                <h3 class="box-title collapse-title" data-widget="collapse"><?= $sub_location->sub_location_name ?></h3>
                <div class="box-tools pull-right">
                    <?php if (check_privilege('Inventory Actions')) { ?>
                        <button data-toggle="modal" data-target="#edit_sub_location_<?= $sub_location_id ?>" class="btn btn-xs btn-default">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <div id="edit_sub_location_<?= $sub_location_id ?>" class="modal fade sub_location_form" tabindex="-1" role="dialog">
                            <?php $this->load->view('inventory/locations/sub_location_form', $data); ?>
                        </div>
                        <button class="btn btn-danger btn-xs" onclick="delete_sub_location(<?= $sub_location_id ?>)">
                            <i class="fa fa-trash-o"></i> Delete
                        </button>
                        <?php if (check_privilege('Inventory Settings')) { ?>
                            <button type="button" id="deactivate_sub_location" class="btn btn-danger btn-xs" onclick="deactivate_sub_location(<?= $sub_location_id ?>)">
                                <i class="fa fa-close"></i> Deactivate
                            </button>
                        <?php }
                    } ?>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <?php $this->load->view('inventory/locations/sub_location_profile', $data); ?>
                    </div>
                </div>
            </div><!-- /.box-body -->
        </div>
        <?php
    }
} else {
    ?>
    <div class="alert alert-info">No Sub-locations found for this location</div>
    <?php
}
?>
<script>
    $(".sub_location_form").each(function() {
        var modal = $(this);
        modal.on("shown.bs.modal", function(e) {
            modal.on(
                "change",
                'input[name="is_for_fuel_mgt"]',
                function(e) {
                    e.preventDefault();
                    if ($(this).is(":checked")) {
                        modal.find(".equipment-id-form-group").show();
                    } else {
                        modal
                            .find('select[name="equipment_id"]')
                            .val("")
                            .change();
                        modal.find(".equipment-id-form-group").hide();
                    }
                }
            );
        });
    });
</script>