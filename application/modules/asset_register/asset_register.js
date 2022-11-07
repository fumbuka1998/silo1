/**
 * Created by STUNNA on 6/22/2017.
 */

/*****************
* ASSET REGISTER
*****************/
/*
function save_asset_group(){

    var group_id = $("input[name='group_id']").val();
    var group_name = $("input[name='group_name']").val();
    var description = $("textarea[name='description']").val();
    var parent_id=$("select[name='parent_id']").val();

    $.ajax(
        {
            url: base_url+'asset_register/Assets/save',
            type: "POST",
            data: {
                group_name:group_name,
                group_id:group_id,
                parent_id:parent_id,
                description:description},

            beforeSend:function(){

                start_spinner();

            },
            complete: function(){

            },
            success:function(data , textStatus ,jqXHR){

                stop_spinner();

            }

        });


}*/

$('#asset_group_lists').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "asset_register/Asset_settings/asset_group_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching asset groups found</div>",
        "emptyTable":     "<div class='alert alert-info'>No asset groups found</div>"
    },"drawCallback": function () {

        $('.save_asset_group').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var group_id = modal.find("input[name='group_id']").val();
                    var group_name = modal.find("input[name='group_name']").val();
                    var description = modal.find("textarea[name='description']").val();
                    var parent_id = modal.find("select[name='parent_id']").val();

                    if(group_name.trim() != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "asset_register/Asset_settings/save_asset_group",
                            {
                                group_name:group_name,
                                group_id: group_id,
                                parent_id:parent_id,
                                description:description
                            },function () {
                                stop_spinner();
                                $('#asset_group_lists').DataTable().draw('page');
                            }
                        );
                    } else {
                        toast('warning','Group Name Must be filled ');
                    }
                });
                button.attr('initialized','true');
            }
        });

        //Delete Asset Group
        $('.delete_asset_group').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "asset_register/Asset_settings/delete_asset_group",
                            {
                                group_id: button.attr('delete_asset_group_id')
                            }, function () {
                                $('#asset_group_lists').DataTable().draw('page');
                            }
                        ).complete(function(){
                            stop_spinner();
                        });
                    }
                });
                button.attr('active', 'true');
            }
        });


        $(this).find('tr').each(function () {
            $(this).find('td:last-child').attr('nowrap', 'nowrap');
        });

        initialize_common_js();
    }
});

$('#asset_list').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "asset_register/Assets/assets_list",
        type: 'POST',
         'data' :function ( d ) {
            d.asset_group_id = $('#filter_by_group').val();
        }
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching asset  found</div>",
        "emptyTable":     "<div class='alert alert-info'>No asset found</div>"
    },"drawCallback": function () {

          $('.save_asset_button').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {

                    var modal = button.closest('.modal');
                    var asset_id = modal.find("input[name='asset_id']").val();
                    var asset_name = modal.find("input[name='asset_name']").val();
                    var sub_location_id = modal.find("select[name='sub_location_id']").val();
                    var asset_group_id = modal.find("select[name='asset_group_id']").val();
                    var asset_code = modal.find("input[name='asset_code']").val();
                    var book_value = modal.find("input[name='book_value']").unmask();
                    var registration_date = modal.find("input[name='registration_date']").val();
                    var description = modal.find("textarea[name='description']").val();
                    var quantity = modal.find("input[name='quantity']").val();
                    

                    if(asset_name.trim() != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "asset_register/Assets/save_Asset",
                            {
                                asset_name:asset_name,
                                asset_id: asset_id,
                                sub_location_id:sub_location_id,
                                asset_code:asset_code,
                                asset_group_id:asset_group_id,
                                book_value:book_value,
                                registration_date:registration_date,
                                description:description,
                                quantity:quantity
                            },function () {
                                stop_spinner();
                                $('#asset_list').DataTable().draw('page');
                            }
                        );
                    } else {
                        toast('warning','Asset Name Must be filled ');
                    }
                });
                button.attr('initialized','true');
            }
        });

         //Delete Asset Group
         $('.delete_asset_button').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "asset_register/Assets/delete_asset",
                            {
                                asset_id: button.attr('asset_id')

                            }, function (data) {

                                if(data=='1'){

                                    toast('success','Deleted Successfully');
                                }else{

                                    toast('error','Cant Delete this Item');
                                }

                                $('#asset_list').DataTable().draw('page');
                            }
                        ).complete(function(){
                            stop_spinner();
                        });
                    }
                });

                button.attr('active', 'true');
            }
        });

        //Activate group filter
        $('#filter_by_group').each(function () {
            var select_field = $(this);
            if(select_field.attr('initialized') != 'true'){
                select_field.change(function () {
                    $('#asset_list').DataTable().draw();
                });
                select_field.attr('initialized','true');
            }
        });


        $(this).find('tr').each(function () {
            $(this).find('td:last-child').attr('nowrap', 'nowrap');
        });

        initialize_common_js();
    }
});

$('#filter_button').click(function () {

                   var issue_date=$("input[name='issue_date']").val();
                   var asset_group_id=$("select[name='asset_group_id']").val();
                   var sub_location_id=$("select[name='sub_location_id']").val();

                   $.ajax(
                          {
                           url: base_url+'asset_register/Asset_reports/asset_depreciation_report_filter',
                           type: "POST",
                           data: {

                            issue_date:issue_date,
                            asset_group_id:asset_group_id,
                            sub_location_id:sub_location_id

                           },
                          beforeSend:function(){

                            start_spinner();

                          },
                          success:function(data , textStatus ,jqXHR){

                                stop_spinner();
                                $('#depreciation_list').html(data);

                          }

                        });

     });

$('#schedule_filter_button').click(function () {

                   var from_date=$("input[name='from_date']").val();
                   var to_date=$("input[name='to_date']").val();
                   var asset_group_id=$("select[name='asset_group_id']").val();
                   var sub_location_id=$("select[name='sub_location_id']").val();

                   $.ajax(
                          {
                           url: base_url+'asset_register/Asset_reports/asset_schedule_report_filter',
                           type: "POST",
                           data: {

                            from_date:from_date,
                            to_date:to_date,
                            asset_group_id:asset_group_id,
                            sub_location_id:sub_location_id

                           },
                          beforeSend:function(){

                            start_spinner();

                          },
                          success:function(data , textStatus ,jqXHR){

                                stop_spinner();
                                $('#schedule_list').html(data);

                          }

                        });

     });


/*****************
 * ASSET TRANSFER
 *****************/

$('#asset_transfer_list').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "asset_register/Asset_transfers/asset_transfer_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching asset Transfer found</div>",
        "emptyTable":     "<div class='alert alert-info'>No asset Transfer found</div>"
    },
     "drawCallback": function () {

         $('.save_transfer').each(function () {

             var button = $(this);
             if(button.attr('initialized') != 'true'){
                 button.click(function () {
                     var modal = button.closest('.modal');
                     var transfer_id = modal.find("input[name='transfer_id']").val();
                     var assetName = modal.find("select[name='asset_id']").val();
                     var department = modal.find("select[name='department_id']").val();
                     var sub_location = modal.find("select[name='sub_location_id']").val();
                     var employee = modal.find("select[name='employee_id']").val();
                     var transferDate = modal.find("input[name='transfer_date']").val();
                     var description = modal.find("textarea[name='description']").val();

                  console.log(assetName,department,sub_location,employee,transferDate,transfer_id,description);
                     if(transferDate != ''){
                         start_spinner();
                         modal.modal('hide');
                         $.post(
                             base_url + "asset_register/Asset_transfers/save",
                             {
                                 transfer_id:transfer_id,
                                 asset_id: assetName,
                                 department_id: department,
                                 sub_location_id: sub_location,
                                 employee_id: employee,
                                 transfer_date: transferDate,
                                 description: description

                             },function () {
                                 stop_spinner();
                                 $('#asset_transfer_list').DataTable().draw('page');
                             }
                         );
                     } else {
                         toast('warning','Transfer Date Must be filled ');
                     }
                 });
                 button.attr('initialized','true');
             }
         });


         //Delete Asset Transfer
         $('.delete_asset_transfer').each(function(){
             var button = $(this);
             if(button.attr('active') != 'true') {
                 button.click(function () {
                     if(confirm('Are you sure?')){
                         start_spinner();
                         $.post(
                             base_url + "asset_register/Asset_transfers/delete_asset_transfer",
                             {
                                 trans_id: button.attr('trans_id')
                             }, function () {
                                 $('#asset_transfer_list').DataTable().draw('page');
                             }
                         ).complete(function(){
                             stop_spinner();
                         });
                     }
                 });
                 button.attr('active', 'true');
             }
         });


    $(this).find('tr').each(function () {
         $(this).find('td:last-child').attr('nowrap', 'nowrap');
    });

        initialize_common_js();
     }
});


/* * DEPRECIATION
 *************************************/

$('#depreciation_rate_table ').each(function () {
    var table = $(this);


    var load_depreciation_content = function () {
       start_spinner();
        $.post(
            base_url + "asset_register/Asset_settings/load_depreciation_content",
            {
            },
            function (data) {
                table.html(data.table);
                 //save dep. rate
                                $('.save_depreciation_rates').each(function () {
                                    var button = $(this);
                                    if(button.attr('initialized') != 'true'){
                                        button.click(function () {
                                            var modal = button.closest('.modal');
                                            var start_date = modal.find("input[name='start_date']").val();
                                            var depreciation_rate_id = modal.find("input[name='depreciation_rate_id']").val();
                                            var asset_group_ids=new Array();
                                            var asset_depreciation_rate_item_ids=new Array();
                                            var depreciation_rates=new Array();
                                            var i=0;

                                            var tbody=modal.find('tbody');

                                            tbody.find('input[name="asset_group_id"]').each(function(){

                                                var row =$(this).closest('tr');

                                                asset_group_ids[i]= row.find('input[name="asset_group_id"]').val();
                                                asset_depreciation_rate_item_ids[i]= row.find('input[name="asset_depreciation_rate_item_id"]').val();
                                                depreciation_rates[i]= row.find('input[name="depreciation_rate"]').val();

                                                i++;

                                            });

                                            //console.log(start_date,asset_group_ids,depreciation_rates,depreciation_rate_id,asset_depreciation_rate_item_ids);

                                            if(start_date != ''){
                                                start_spinner();
                                                modal.modal('hide');

                                                $.post(
                                                    base_url + "asset_register/Asset_settings/save_depreciation_rate",
                                                    {
                                                        depreciation_rate_id:depreciation_rate_id,
                                                        start_date:start_date,
                                                        asset_depreciation_rate_item_ids:asset_depreciation_rate_item_ids,
                                                        asset_group_ids:asset_group_ids,
                                                        depreciation_rates:depreciation_rates

                                                    },function (data) {
                                                        stop_spinner();
                                                        modal.find('form')[0].reset();
                                                        load_depreciation_content();

                                                    }
                                                );
                                            } else {

                                                toast('warning','Start Date Must be filled ');
                                            }
                                        });
                                        button.attr('initialized','true');
                                    }
                                });
                //load  items
                $('.depreciation_rate_items_table').each(function () {
                    var depreciation_rate_id = $(this).attr('depreciation_rate_id');
                    var table = $(this);
                    var panel_body = table.closest('.panel-body');

                    var load_depreciation_rate_items = function () {
                        start_spinner();
                        $.post(
                            base_url + "asset_register/Asset_settings/load_depreciation_rate_items",
                            {
                                depreciation_rate_id: depreciation_rate_id
                            },
                            function (data) {
                                table.html(data.table);

                                //save dep. rate
                                $('.save_depreciation_rates').each(function () {
                                    var button = $(this);
                                    if(button.attr('initialized') != 'true'){
                                        button.click(function () {
                                            var modal = button.closest('.modal');
                                            var start_date = modal.find("input[name='start_date']").val();
                                            var depreciation_rate_id = modal.find("input[name='depreciation_rate_id']").val();
                                            var asset_group_ids=new Array();
                                            var asset_depreciation_rate_item_ids=new Array();
                                            var depreciation_rates=new Array();
                                            var i=0;

                                            var tbody=modal.find('tbody');

                                            tbody.find('input[name="asset_group_id"]').each(function(){

                                                var row =$(this).closest('tr');

                                                asset_group_ids[i]= row.find('input[name="asset_group_id"]').val();
                                                asset_depreciation_rate_item_ids[i]= row.find('input[name="asset_depreciation_rate_item_id"]').val();
                                                depreciation_rates[i]= row.find('input[name="depreciation_rate"]').val();

                                                i++;

                                            });

                                            //console.log(start_date,asset_group_ids,depreciation_rates,depreciation_rate_id,asset_depreciation_rate_item_ids);

                                            if(start_date != ''){
                                                start_spinner();
                                                modal.modal('hide');

                                                $.post(
                                                    base_url + "asset_register/Asset_settings/save_depreciation_rate",
                                                    {
                                                        depreciation_rate_id:depreciation_rate_id,
                                                        start_date:start_date,
                                                        asset_depreciation_rate_item_ids:asset_depreciation_rate_item_ids,
                                                        asset_group_ids:asset_group_ids,
                                                        depreciation_rates:depreciation_rates

                                                    },function (data) {
                                                        stop_spinner();
                                                        modal.find('form')[0].reset();
                                                        load_depreciation_content();

                                                    }
                                                );
                                            } else {

                                                toast('warning','Start Date Must be filled ');
                                            }
                                        });
                                        button.attr('initialized','true');
                                    }
                                });

                                $('.delete_depreciation_rate').each(function(){
                                    var button = $(this);
                                    if(button.attr('initialized') != 'true') {
                                        button.click(function () {
                                            if(confirm('Are you sure?')){
                                                start_spinner();
                                                $.post(
                                                    base_url + "asset_register/Asset_settings/delete_depreciation_rate",
                                                    {
                                                        depreciation_rate_id: parseInt(button.attr('delete_depreciation_rate_id'))
                                                    }, function () {

                                                        load_depreciation_content();
                                                    }
                                                ).complete(function(){
                                                    stop_spinner();
                                                    initialize_common_js();
                                                });
                                            }
                                        });
                                        button.attr('initialized', 'true');
                                    }
                                });

                                stop_spinner();
                            },
                            'json'
                        ).complete();

                    }

                    load_depreciation_rate_items();
                });

               stop_spinner();
            },
            'json'
        ).complete();
    };

    load_depreciation_content();

});


/***************************************************
 * REQUISITIONS
 ***************************************************/

function save_equipment_requisition(button){

    var modal = button.closest('.modal');
    var requisition_id = modal.find('input[name="requisition_id"]').val();
    var approval_module_id = modal.find('select[name="approval_module_id"]').val();
    var requisition_cost_center_field = modal.find('select[name="requisition_cost_center_id"]');
    var requisition_cost_center_id = requisition_cost_center_field.val();
    var request_date = modal.find('input[name="request_date"]').val();
    var currency_id = modal.find('select[name="currency_id"]').val();
    var required_date = modal.find('input[name="required_date"]').val(), i = 0;
    var cost_center_ids = new Array(),expense_account_ids = new Array(),item_types = new Array(), vendor_or_unit_ids = new Array(), item_ids = new Array(), durations = new Array(),rate_modes = new Array(),quantities = new Array(), rates = new Array();
    var tbody = modal.find('tbody'), error = 0;

    tbody.find('input[name="quantity"]').each(function(){
        var item_id, vendor_or_unit_id;
        var quantity = $(this).val();
        var row = $(this).closest('tr');
        var rate = row.find('input[name="rate"]').unmask();
        var item_type = row.find('input[name="item_type"]').val();
        if(item_type == 'equipment'){
            item_id = row.find('select[name="asset_group_id"]').val();
            vendor_or_unit_id = row.find('.vendor_id').val();
        } else {
            item_id = row.find('input[name="description"]').val();
            vendor_or_unit_id = row.find('select[name="uom_id"]').val();
        }

      if(parseFloat(quantity) > 0 && parseFloat(rate) > 0 && item_id != '' && (item_type == 'equipment' || vendor_or_unit_id.trim() != '')) {
            quantities[i] = quantity;
            item_types[i] = item_type;
            cost_center_ids[i] = row.find('select[name="cost_center_id"]').val();
            expense_account_ids[i] = row.find('select[name="expense_account_id"]').val();
            vendor_or_unit_ids[i] = vendor_or_unit_id;
            rates[i] = rate;
            item_ids[i] = item_id;
            durations[i] = row.find('input[name="duration"]').val();
            rate_modes[i] = row.find('select[name="rate_mode"]').val();
            i++;
        } else {

            error++;
        }
    });

    //console.log(requisition_id,approval_module_id,requisition_cost_center_id,quantities,rate_modes,durations);

   if(error == 0 && request_date != '' && quantities.length > 0 && approval_module_id.trim() != '' && approval_module_id != '' && requisition_cost_center_id.trim() != '') {


        modal.modal('hide');
        var comments = modal.find('textarea[name="comments"]').val();
        start_spinner();
        $.post(
            base_url + "asset_register/Equipment_requisitions/save_equipment_requisition",
            {
                requisition_id : requisition_id,
                approval_module_id : approval_module_id,
                requisition_cost_center_id : requisition_cost_center_id,
                quantities: quantities,
                rates: rates,
                durations:durations,
                rate_modes:rate_modes,
                currency_id: currency_id,
                request_date: request_date,
                required_date: required_date,
                item_types : item_types,
                item_ids : item_ids,
                vendor_or_unit_ids: vendor_or_unit_ids,
                cost_center_ids: cost_center_ids,
                expense_account_ids: expense_account_ids,
                comments: comments
            }, function (data) {
                modal.find('form')[0].reset();
                tbody.find('.artificial_row').remove();
                requisition_cost_center_field.closest('form-group').hide();
                modal.find('.unit_display, .total_amount_display').html('');
                modal.closest('.box').find('.requisitions_table').DataTable().draw('page');
                initialize_common_js();
            }
        ).complete(function(){
            stop_spinner();
        });
     } else {
         toast('error','Please make sure all fields are correctly filled');
     }
}

$('#hired_equipments_table').DataTable({
    "processing": true,
    "serverSide": true,
     "ajax": {
                url: base_url + "asset_register/Hired_equipments/hired_equipments_list",
                type: 'POST'

            },
            "columns": [
                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": true}
            ],
            "language": {
                "zeroRecords": "<div class='alert alert-info'>No matching Equipment found</div>",
                "emptyTable": "<div class='alert alert-info'>No Equipment found</div>"
    },
     "drawCallback": function () {

     }

});


$('.hired_equipments_list_table').DataTable({
    "processing": true,
    "serverSide": true,
     "ajax": {
                url: base_url + "asset_register/Hired_equipments/hired_equipments_receipts",
                type: 'POST'

            },
            "columns": [

                {"orderable": true},
                {"orderable": true},
                {"orderable": true},
                {"orderable": false}

            ],
            "language": {
                "zeroRecords": "<div class='alert alert-info'>No matching Equipment found</div>",
                "emptyTable": "<div class='alert alert-info'>No Equipment found</div>"
    },
    "drawCallback": function () {

        $('.save_equipment_receipt').each(function(){

            var button = $(this);

            if(button.attr('active') != 'true') {
                button.click(function () {

                    save_equipment_receipt($(this));
                });

                button.attr('active', 'true');
            }
        });

        $('.row_adder').each(function () {

            var button=$(this);

            if (button.attr('initialized') != 'true') {
                button.click(function () {
                    var table = button.closest('table');
                    var tbody=table.find('tbody');
                    var new_row = tbody.closest('table').find('.row_template').clone().removeAttr('style')
                        .removeClass('row_template').addClass('artificial_row').appendTo(tbody);
                    new_row.find('.row_remover').click(function(){
                        $(this).closest('tr').remove();
                    });
                    initialize_common_js();
                    new_row.find('.number_format').priceFormat({centsLimit:2});
                });
            }

            $(this).attr('initialized','true');

        });

        $('.row_remover').each(function(){
            var button=$(this);

            if(button.attr('initialized') != 'true') {

                button.click(function(){
                    $(this).closest('tr').remove();
                });
            }

            $(this).attr('initialized','true');
        });


        $('.delete_hired_equipment_receipt').each(function(){
            var button = $(this);

            if(button.attr('active') != 'true') {
                button.click(function () {

                   var equipment_receipt_id = parseInt(button.attr('hired_equipment_receipt_id'));


                    if(confirm('Are you sure?')){
                        start_spinner();
                        console.log(equipment_receipt_id);
                        $.post(
                            base_url + "asset_register/Hired_equipments/delete_hired_equipment_receipt",
                            {
                                equipment_receipt_id:equipment_receipt_id
                            }, function () {
                                $('.hired_equipments_list_table').DataTable().draw('page');
                            }
                        ).complete(function(){
                            stop_spinner();
                        });
                    }
                });
                button.attr('active', 'true');
            }
        });

          $(this).find('tr').each(function () {
          $(this).find('td:last-child').attr('nowrap', 'nowrap');
});


        initialize_common_js();



}

});


function save_equipment_receipt(button){

    var modal = button.closest('.modal');
    var equipment_receipt_id = modal.find('input[name="equipment_receipt_id"]').val();
    var currency_id = modal.find('select[name="currency_id"]').val();
    var vendor_id= modal.find('select[name="vendor_id"]').val();
    var issue_date = modal.find('input[name="issue_date"]').val(), i = 0;
    var asset_group_ids = new Array(), equipment_codes = new Array(),rate_modes = new Array(),rates = new Array();
    var tbody = modal.find('tbody'), error = 0;

    tbody.find('input[name="equipment_code"]').each(function(){

        var equipment_code = $(this).val();
        var row = $(this).closest('tr');
        var rate = row.find('input[name="rate"]').unmask();
        var asset_group_id = row.find('select[name="asset_group_id"]').val();


      if((equipment_code != '' && asset_group_id!= '')) {
            rates[i] = rate;
            asset_group_ids[i] = row.find('select[name="asset_group_id"]').val();
            equipment_codes[i] = equipment_code;
            rate_modes[i] = row.find('select[name="rate_mode"]').val();
            i++;
        } else {

            error++;
        }
    });

   if(error == 0 && issue_date != '' && asset_group_ids.length > 0 ) {

        modal.modal('hide');
        var comments = modal.find('textarea[name="comments"]').val();
        start_spinner();
        $.post(
            base_url + "asset_register/Hired_equipments/save_equipment_receipt",
            {
                equipment_receipt_id : equipment_receipt_id,
                equipment_codes: equipment_codes,
                rates: rates,
                asset_group_ids:asset_group_ids,
                rate_modes:rate_modes,
                currency_id: currency_id,
                receipt_date: issue_date,
                vendor_id: vendor_id,
                comments: comments
            }, function (data) {
                modal.find('form')[0].reset();
                tbody.find('.artificial_row').remove();
                modal.closest('.box').find('.hired_equipments_table').DataTable().draw('page');
                initialize_common_js();
            }
        ).complete(function(){
            $('.hired_equipments_list_table').DataTable().draw('page');
            stop_spinner();
        });
     } else {
         toast('error','Please make sure all fields are correctly filled');
     }
}






