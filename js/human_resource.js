

/***************************
 *         BANKS
 ***************************/

$('#banks_list_table').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Settings/banks_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching Bank found</div>",
        "emptyTable":     "<div class='alert alert-info'>No bank found</div>"
    },

    "drawCallback": function () {


        //Save Bank

        $('.save_bank_button').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var bank_id = modal.find("input[name='bank_id']").val();
                    var bank_name = modal.find("input[name='bank_name']").val();
                    var description = modal.find("textarea[name='description']").val();

                    console.log(bank_id,bank_name,description);
                    if(bank_name != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Settings/save_bank",
                            {
                                bank_id: bank_id,
                                bank_name: bank_name,
                                description: description

                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#banks_list_table').DataTable().draw('page');
                                toast('success','Bank Added successful ');
                            }
                        );
                    } else {
                        display_form_fields_error();
                    }
                });
                button.attr('initialized','true');
            }
        });


        //Delete Bank
        $('.delete_bank').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Settings/delete_bank",
                            {
                                delete_bank_id: button.attr('delete_bank_id')
                            }, function () {
                                $('#banks_list_table').DataTable().draw('page');
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

/***************************
 *         ALLOWANCES
 ***************************/

$('#allowances_list_table').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Settings/allowances_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": false},
        {"orderable": true},
        {"orderable": false},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching allowance found</div>",
        "emptyTable":     "<div class='alert alert-info'>No allowance found</div>"
    },

    "drawCallback": function () {


        //Save Allowance

        $('.save_allowance').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var allowance_id = modal.find("input[name='allowance_id']").val();
                    var allowance_name = modal.find("input[name='allowance_name']").val();
                    var description = modal.find("textarea[name='description']").val();

                    if(allowance_name != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Settings/save_allowance",
                            {
                                allowance_id: allowance_id,
                                allowance_name: allowance_name,
                                description: description

                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#allowances_list_table').DataTable().draw('page');
                                toast('success','Allowance Added successful ');
                            }
                        );
                    } else {
                        display_form_fields_error();
                    }
                });
                button.attr('initialized','true');
            }
        });

        //Delete Allowance
        $('.delete_allowance').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Settings/delete_allowance",
                            {
                                allowance_id: button.attr('allowance_id')
                            }, function () {
                                $('#allowances_list_table').DataTable().draw('page');
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

/***************************
 *         LOANS
 ***************************/

$('.save_account_human_resource').each(function(){
        var button = $(this);
        var employee_id = button.attr('employee_id');
        if(button.attr('active') != 'true') {
            var modal = button.closest('.modal');
            var account_group_field = modal.find('select[name="account_group_id"]');
            var all_group_options = account_group_field.html();
            modal.find('select[name="account_for"]').on('change keyup',function () {
                var account_for = $(this).val();
                var related_to_field = modal.find('select[name="related_to"]');
                if(account_for == 'other'){
                    related_to_field.attr('disabled','disabled');
                    related_to_field.html('<option></option>').change();
                    account_group_field.html(all_group_options).change();
                } else {
                    related_to_field.removeAttr('disabled');
                    start_spinner();
                    var account_groups = [];
                    if (account_for == 'project'){
                        account_groups = ['BANK', 'CASH IN HAND'];
                        $.post(
                            base_url + "projects/load_project_dropdown_options",
                            {

                            }, function (data) {
                                related_to_field.html(data).change();
                            }
                        ).complete();
                    } else if(account_for == 'contractor'){
                        account_groups = ['ACCOUNT PAYABLE'];
                        $.post(
                            base_url + "contractors/load_contractor_dropdown_options",
                            {

                            }, function (data) {
                                related_to_field.html(data).change();
                            }
                        ).complete();
                    }

                    $.post(
                        base_url + "finance/load_account_group_options",
                        {
                            account_groups: account_groups
                        }, function (data) {
                            account_group_field.html(data).change();
                            stop_spinner();
                        }
                    ).complete();
                }
            });

            modal.find('select[name="account_group_id"]').on('change keyup',function () {
                var account_group_selector = $(this);
                if(account_group_selector.attr('initialized') != 'true'){
                    var selected_account_id = account_group_selector.val();
                    var bank_div = modal.find('#bank_options');
                    var bank_id = modal.find('select[name="bank_id"]');

                    start_spinner();
                    $.post(
                        base_url + "finance/bank_options",
                        {
                            selected_account_id: selected_account_id
                        }, function (data) {
                            data ? bank_div.show() : bank_div.hide();
                            bank_id.html(data).change();
                            var bank_details = modal.find('#bank_details');
                            bank_details.hide();
                        }
                    ).complete();
                    stop_spinner();
                }
            });

            modal.find('select[name="bank_id"]').on('change keyup',function () {
                var bank_details = modal.find('#bank_details');
                modal.find('#account_number').val('');
                modal.find('#branch').val('');
                modal.find('#swift_code').val('');

                if(modal.find('select[name="bank_id"]').val() > 0 ){
                    bank_details.show();
                }else{
                    bank_details.hide();
                }
            });


            button.click(function () {
                var modal = button.closest('.modal');
                var account_id = modal.find('input[name="account_id"]').val();
                var account_name = modal.find('input[name="account_name"]').val();
                var account_group_id = modal.find('select[name="account_group_id"]').val();
                var related_to = modal.find('select[name="related_to"]').val();
                var account_for = modal.find('select[name="account_for"]').val();
                var bank_id = modal.find('select[name="bank_id"]').val();
                var opening_balance = modal.find('input[name="opening_balance"]').unmask();
                var description = modal.find('textarea[name="description"]').val();
                var account_number = modal.find('#account_number').val();
                var branch = modal.find('#branch').val();
                var swift_code = modal.find('#swift_code').val();
                if(account_name != '' && account_group_id != '') {
                    modal.modal('hide');

                    $.post(
                        base_url + "finance/save_account/",
                        {
                            account_id: account_id,
                            account_name : account_name,
                            account_for : account_for,
                            related_to : related_to,
                            bank_id : bank_id,
                            account_group_id: account_group_id,
                            opening_balance: opening_balance,
                            description: description,
                            account_number: account_number,
                            branch: branch,
                            swift_code: swift_code,
                            loan: 'true',
                            employee_id: employee_id
                        }, function (data) {
                            modal.find('form')[0].reset();
                             toast('success', 'Account Created');
                            /////$('#accounts_list').DataTable().draw('page');

                        }
                    );
                }

            });
            button.attr('active', 'true');
        }
    });

$('#loan_type_list_table').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Settings/loan_type_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": false},
        {"orderable": true},
        {"orderable": false},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching loan type found</div>",
        "emptyTable":     "<div class='alert alert-info'>No loan type found</div>"
    },
    "drawCallback": function (){

        //Save loan types
        $('.save_loan_type').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var loan_type_id = modal.find("input[name='loan_type_id']").val();
                    var loan_type = modal.find("input[name='loan_type']").val();
                    var description = modal.find("textarea[name='description']").val();

                    if(loan_type != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Settings/save_loan_type",
                            {
                                loan_type_id: loan_type_id,
                                loan_type: loan_type,
                                description: description

                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#loan_type_list_table').DataTable().draw('page');
                                toast('success','Loan Type Added successful ');
                            }
                        );
                    } else {
                        display_form_fields_error();
                    }
                });
                button.attr('initialized','true');
            }
        });

        //Delete loan types
        $('.delete_loan_type').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Settings/delete_loan_type",
                            {
                                loan_type_id: button.attr('loan_type_id')
                            }, function () {
                                $('#loan_type_list_table').DataTable().draw('page');
                            }
                        ).complete(function(){
                            stop_spinner();
                        });
                    }
                });
                button.attr('active', 'true');
            }
        });

    }
});

$('.employee_loan_table').each(function(){
    var table = $(this);
    var employee_id = table.attr('employee_id');
    table.DataTable({
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Human_resources/employee_loans_list/"+employee_id,
            type: 'POST'
        },
        "columns": [
            {"orderable": false},
            {"orderable": false},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": false},
            {"orderable": false}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching loan found</div>",
            "emptyTable":     "<div class='alert alert-info'>No loan found</div>"
        },
        "drawCallback": function (){

            var error = 0;

            function find_employee_id(modal){
               var account_id = modal.find("select[name='dr_account']").val();
                    $.post(
                        base_url + "finance/check_employee_name/",
                        {
                            account_id: account_id
                        },function (data) {

                            if (data == ''){
                                toast('error', 'You cannot grant loan to this account');
                            }else{
                                 modal.find('input[name="employee_id"]').val(data).trigger('change');
                            }
                        }
                    );
            }


            //// saving employee loan
            $('.save_employee_loan').each(function () {
                var button = $(this);
                if(button.attr('initialized') != 'true'){
                    button.click(function () {
                        var modal = button.closest('.modal');
                        var loan_id = modal.find("select[name='loan_id']").val();
                        var dr_account = modal.find("select[name='dr_account']").val();
                        var cr_account = modal.find("select[name='cr_account']").val();
                        var approved_date = modal.find("input[name='approved_date']").val();
                        var deduction_start_date = modal.find("input[name='deduction_start_date']").val();
                        var total_loan_amount = modal.find("input[name='total_loan_amount']").unmask();
                        var monthly_deduction_rate = modal.find("input[name='monthly_deduction_rate']").unmask();
                        var reference = modal.find("input[name='reference']").val();
                        var application_letter = modal.find("file[name='application_letter']").val();
                        var description = modal.find("textarea[name='description']").val();

                        if(loan_id != '' && dr_account != '' && cr_account != ''
                            && approved_date != '' && deduction_start_date != '' && description != ''
                            && total_loan_amount >= 0 && monthly_deduction_rate >= 0 ){
                            start_spinner();
                            modal.modal('hide');
                            $.post(
                                base_url + "finance/save_employee_loans/",
                                {
                                    dr_account: dr_account,
                                    cr_account: cr_account,
                                    loan_id: loan_id,
                                    approved_date: approved_date,
                                    deduction_start_date: deduction_start_date,
                                    total_loan_amount: total_loan_amount,
                                    monthly_deduction_rate: monthly_deduction_rate,
                                    reference: reference,
                                    application_letter: application_letter,
                                    description: description

                                },function () {
                                    stop_spinner();
                                    modal.find('form')[0].reset();
                                    $('.employee_loan_table').DataTable().draw('page');
                                    toast('success','Loan Added successful ');
                                }
                            );
                        } else {
                            display_form_fields_error();
                        }
                    });
                    button.attr('initialized','true');
                }
            });





            ///// checking the loan type
            // $('.employee_loan_form').on('shown.bs.modal', function (e){
            //     var modal = $(this);
            //     find_employee_id(modal);
            //     var employee_id = modal.find("input[name='employee_id']").val();
            //
            //     modal.delegate(' input[name="total_loan_amount"] ','change keyup',function() {
            //       var loan_id = modal.find("select[name='loan_id']").val();
            //       var total_loan = modal.find("input[name='total_loan_amount']").unmask();
            //       var error_display = modal.find("label[name='monthly_deduction_error']");
            //
            //         $.post(
            //             base_url + "human_resource/Human_resources/verfy_employee_loan/",
            //             {
            //                 loan_id: loan_id,
            //                 employee_id: employee_id
            //             },function (data) {
            //                 var results = data.split(' ');
            //                 if (results[0] == 'advance'){
            //                     modal.find("input[name='monthly_deduction_rate']").val(total_loan).priceFormat().attr('readonly','true').trigger('cahange');
            //                     if(total_loan > results[1]){
            //                         toast('error', 'Advance salary should exceed '+results[1]);
            //                         error_display.attr('style', 'color: red;display: inline').trigger('change');
            //                         error = 1;
            //                     }else{
            //                         error_display.attr('style', 'color: red;display: none').trigger('change');
            //                         error = 0;
            //                     }
            //                     modal.find("input[name='monthly_deduction_rate']").val(total_loan).priceFormat().attr('readonly','true').trigger('cahange');
            //                 }else if (results[0] == 'heslb'){
            //                     modal.find("input[name='monthly_deduction_rate']").val(results[1]).priceFormat().attr('readonly','true').trigger('cahange');
            //                     error = 0;
            //                 } else{
            //                     error_display.attr('style', 'color: red;display: none').trigger('change');
            //                     modal.find("input[name='monthly_deduction_rate']").removeAttr('readonly').val('').trigger('cahange');
            //                     error = 0;
            //                 }
            //
            //             }
            //         );
            //     })
            //
            //     modal.find("select[name='loan_id']").change(function () {
            //         $.post(
            //             base_url + "human_resource/Human_resources/verfy_employee_loan/",
            //             {
            //                 loan_id: $(this).val(),
            //                 employee_id: employee_id
            //             },function (data) {
            //                 var results = data.split(' ');
            //                 if (results[0] == 'heslb'){
            //                     modal.find("input[name='monthly_deduction_rate']").val(results[1]).priceFormat().attr('readonly','true').trigger('cahange');
            //                     error = 0;
            //                 }else if(results[0] == 'advance'){
            //                     error_display.attr('style', 'color: red;display: none').trigger('change');
            //                     modal.find("input[name='monthly_deduction_rate']").val('').priceFormat().attr('readonly','true').trigger('cahange');
            //                     error = 1;
            //                 }else{
            //                     error_display.attr('style', 'color: red;display: none').trigger('change');
            //                     modal.find("input[name='monthly_deduction_rate']").removeAttr('readonly').val('').trigger('cahange');
            //                     error = 0;
            //                 }
            //             }
            //         );
            //     })
            // });

           //// checking if payment doesnt exceed the balance
            $('.loan_payment_form').on('shown.bs.modal', function (e){
                var modal = $(this);
                //
                // modal.delegate('input[name="paid_amount"]','change keyup',function() {
                //
                //     var paid_amount = modal.find("input[name='paid_amount']").unmask();
                //     var loan_balance = modal.find("input[name='loan_balance']").unmask();
                //     var error_display = modal.find("label[name='paid_ammout_error']");
                //
                //     if(paid_amount > loan_balance){
                //         toast('error', 'Paid Amount should exceed '+loan_balance);
                //         error_display.attr('style', 'color: red;display: inline').trigger('change');
                //     }else{
                //         error_display.attr('style', 'color: red;display: none').trigger('change');
                //     }
                //
                // });

                initialize_common_js();
            });

            /// delete Employee_loan
            $('.delete_employee_loan').click(function () {
                var button = $(this);
                var employee_loan_id = button.attr('employee_loan_id');
                if(button.attr('initialized') != 'true'){
                    start_spinner();
                    $.post(
                        base_url + "human_resource/Human_resources/delete_employee_loan/",
                        {
                            employee_loan_id: employee_loan_id
                        },function(data){
                            stop_spinner();
                            $('.employee_loan_table').DataTable().draw('page');
                            $('.employee_loan_history_table').DataTable().draw('page');
                            var results = data.split('-');
                            toast(results[0], results[1]);
                        }
                    )

                    button.attr('initialized','true');
                }
            })

            //// saving employee loan payments
            $('.save_employee_loan_payment').each(function () {
                var button = $(this);
                if(button.attr('initialized') != 'true'){
                    button.click(function () {
                        var modal = button.closest('.modal');
                        var employee_id = modal.find("input[name='employee_id']").val();
                        var dr_account = modal.find("select[name='dr_account']").val();
                        var paid_date = modal.find("input[name='paid_date']").val();
                        var paid_amount = modal.find("input[name='paid_amount']").unmask();
                        var attachments = modal.find("file[name='attachments']").val();
                        var description = modal.find("textarea[name='description']").val();
                        var cr_account = modal.find("input[name='cr_account']").val();
                        var employee_loan_id = modal.find("input[name='employee_loan_id']").val();

                        ////var error_display = modal.find("label[name='paid_ammout_error']");

                        if(paid_date != '' && paid_amount != '' && dr_account != '' && description != ''){

                            // if(paid_amount > loan_balance){
                            //     toast('error', 'Paid Amount should exceed '+loan_balance);
                            //     error_display.attr('style', 'color: red;display: inline').trigger('change');
                            // }else{
                            //     error_display.attr('style', 'color: red;display: none').trigger('change');
                                start_spinner();
                                modal.modal('hide');
                                $.post(
                                    base_url + "finance/save_employee_loan_repay/",
                                    {
                                        employee_id:employee_id,
                                        dr_account: dr_account,
                                        cr_account: cr_account,
                                        paid_date: paid_date,
                                        paid_amount: paid_amount,
                                        description: description,
                                        employee_loan_id: employee_loan_id

                                    },function () {
                                        stop_spinner();
                                        modal.find('form')[0].reset();
                                        $('.employee_loan_table').DataTable().draw('page');
                                        toast('success','Payments Added successful ');
                                    }
                                );
                            // }

                        } else {
                            display_form_fields_error();
                        }
                    });
                    button.attr('initialized','true');
                }
            });
            initialize_common_js();
        }
    });

});

$('.employee_loan_history_table').each(function(){
    var table = $(this);
    var employee_id = table.attr('employee_id');
    table.DataTable({
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Human_resources/loan_repay_list/"+employee_id,
            type: 'POST'
        },
        "columns": [
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching loan history found</div>",
            "emptyTable":     "<div class='alert alert-info'>No loan history found</div>"
        },
        "drawCallback": function (){

        }
    });

});

/***************************
 *         BRANCHES
 ***************************/

$('#branches_list_table').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Settings/branches_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching Branch found</div>",
        "emptyTable":     "<div class='alert alert-info'>No branch found</div>"
    },

    "drawCallback": function () {


        //Save branch

        $('.save_branch_button').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var branch_id = modal.find("input[name='branch_id']").val();
                    var branch_name = modal.find("input[name='branch_name']").val();

                    console.log(branch_id,branch_name);
                    if(branch_name != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Settings/save_branch",
                            {
                                branch_id: branch_id,
                                branch_name: branch_name,

                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#branches_list_table').DataTable().draw('page');
                                toast('success','Branch Added successful ');
                            }
                        );
                    } else {
                        display_form_fields_error();
                    }
                });
                button.attr('initialized','true');
            }
        });


        //Delete Branch
        $('.delete_branch').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Settings/delete_branch",
                            {
                                delete_branch_id: button.attr('delete_branch_id')
                            }, function () {
                                $('#branches_list_table').DataTable().draw('page');
                            }
                        ).complete(function(){
                            toast('success','Branch Deleted ');
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

/***************************
 *         SSFS
 ***************************/

$('#ssfs_list_table').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Settings/ssfs_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching SSF found</div>",
        "emptyTable":     "<div class='alert alert-info'>No SSF found</div>"
    },

    "drawCallback": function () {


        //Save SSF

        $('.save_ssf_button').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var ssf_id = button.attr('ssf_id');
                    var ssf_name = modal.find("input[name='ssf_name']").val();
                    var employer_deduction_percent = modal.find("input[name='employer_deduction_percent']").val();
                    var employee_deduction_percent = modal.find("input[name='employee_deduction_percent']").val();

                    if(ssf_name != '' && employer_deduction_percent != '' && employee_deduction_percent != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Settings/save_ssf",
                            {
                                 ssf_id: ssf_id,
                                 ssf_name: ssf_name,
                                 employer_deduction_percent: employer_deduction_percent,
                                 employee_deduction_percent: employee_deduction_percent

                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#ssfs_list_table').DataTable().draw('page');
                                toast('success','SSF Added successful ');
                            }
                        );
                    } else {
                        display_form_fields_error();
                    }
                });
                button.attr('initialized','true');
            }
        });


        //Delete SSF
        $('.delete_ssf').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Settings/delete_ssf",
                            {
                                ssf_id: button.attr('delete_ssf_id')
                            }, function () {
                                $('#ssfs_list_table').DataTable().draw('page');
                            }
                        ).complete(function(){
                            toast('success','SSF Deleted ');
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

/***************************
 *         HIFS
 ***************************/

$('#hifs_list_table').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Settings/hifs_list",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching HIF found</div>",
        "emptyTable":     "<div class='alert alert-info'>No HIF found</div>"
    },

    "drawCallback": function () {


        //Save SSF

        $('.save_hif_button').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var hif_id = button.attr('hif_id');
                    var hif_name = modal.find("input[name='hif_name']").val();
                    var employer_deduction_percent = modal.find("input[name='employer_deduction_percent']").val();
                    var employee_deduction_percent = modal.find("input[name='employee_deduction_percent']").val();

                    if(hif_name != '' && employer_deduction_percent != '' && employee_deduction_percent != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Settings/save_hif",
                            {
                                hif_id: hif_id,
                                hif_name: hif_name,
                                employer_deduction_percent: employer_deduction_percent,
                                employee_deduction_percent: employee_deduction_percent

                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#hifs_list_table').DataTable().draw('page');
                                toast('success','HIF Added successful ');
                            }
                        );
                    } else {
                        display_form_fields_error();
                    }
                });
                button.attr('initialized','true');
            }
        });


        //Delete HIF
        $('.delete_hif').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Settings/delete_hif",
                            {
                                hif_id: button.attr('delete_hif_id')
                            }, function () {
                                $('#hifs_list_table').DataTable().draw('page');
                            }
                        ).complete(function(){
                            toast('success','HIF Deleted ');
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


/***************************
 *        TAX TABLE
 ***************************/

//Save tax table rates

$('#taxtable_accordion').each(function () {
    var tax_table_div = $(this);

    function display_tax_table() {
        $.post(
            base_url + "human_resource/Human_resources/display_tax_tables/",
            {
                data_sent: true
            }, function (data) {
                tax_table_div.html(data);
            }
        ).complete();
    }
    display_tax_table();

    $('.save_tax_rate').each(function () {

        var button = $(this);
        if(button.attr('initialized') != 'true'){
            button.click(function () {
                var modal = button.closest('.modal');
                var start_date = modal.find("input[name='start_date']").val();
                var end_date = modal.find("input[name='end_date']").val();
                var tax_table_id = modal.find("input[name='tax_table_id']").val();
                //var tax_item_ids = modal.find("input[name='tax_table_id']").val();
                // var tax_item_ids = new Array();
                var minimums = new Array();
                var maximums = new Array();
                var rates = new Array();
                var additional_amounts = new Array();
                i = 0;
                var tbody = modal.find('tbody');

                tbody.find('input[name="minimum"]').each(function(){
                    var minimum = $(this).val();
                    var row = $(this).closest('tr');
                    // tax_item_ids = row.find('input[name="tax_item_id"]').val();
                    minimums[i] = row.find('input[name="minimum"]').unmask();
                    maximums[i] = row.find('input[name="maximum"]').unmask();
                    rates[i] = row.find('input[name="rate"]').val();
                    additional_amounts[i] = row.find('input[name="additional_amount"]').unmask();
                    i++;

                });

                console.log(start_date,end_date,minimums,maximums,rates,additional_amounts);
                if(start_date != ''){
                    start_spinner();
                    modal.modal('hide');
                    $.post(
                        base_url + "human_resource/Settings/save_tax_rates",
                        {
                            tax_table_id: tax_table_id,
                            //  tax_item_id: tax_item_ids,
                            start_date: start_date,
                            end_date: end_date,
                            rates: rates,
                            minimums:minimums,
                            maximums:maximums,
                            additional_amounts:additional_amounts,

                        },function (data) {
                            stop_spinner();
                            modal.find('form')[0].reset();
                            display_tax_table();
                            toast('success','Tax Rate Added successful ');
                        }
                    );
                } else {
                    display_form_fields_error();
                }
            });
            button.attr('initialized','true');
        }
    });
});

$('#tax_table_form').each(function () {
    var modal = $(this);
    modal.find('.row_adder').each(function () {
        $(this).click(function () {
            var tbody = $(this).closest('table').find('tbody');
            var new_row = tbody.closest('table').find('.row_template').clone().removeAttr('style')
                .removeClass('row_template').addClass('sales_artificial_row').appendTo(tbody);
            new_row.find('.number_format').priceFormat();

            var prev_row = new_row.prev();
            prev_row.find('.row_remover').attr('style', 'display:none');

            // var prev_row = new_row.prev();
            // prev_row.find('.row_remover').remove();
            // var last_td = prev_row.find('td:last');

            new_row.find('.row_remover').click(function () {
                prev_row.find('.row_remover').removeAttr('style');
                $(this).closest('tr').remove();

                initialize_common_js();
            });
        });
    });
});

/*$('.row_remover').each(function () {
    var button = $(this);
    button.click(function(){
          button.closest('tr').remove();
    });

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

});*/

/******************************
 * HUMAN RESOURCES
 ******************************/

$('#contract_employee_list').DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Human_resources/contract_employee_list/",
            type: 'POST'
        },
        "columns": [
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching employees found</div>",
            "emptyTable":     "<div class='alert alert-info'>No employees found</div>"
        }
});

$('#non_contract_employee_list').DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Human_resources/non_contract_employee_list/",
            type: 'POST'
        },
        "columns": [
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching employees found</div>",
            "emptyTable":     "<div class='alert alert-info'>No employees found</div>"
        }
});

$('#incomplete_contract_employee_list').DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Human_resources/incomplete_contract_employee_list/",
            type: 'POST'
        },
        "columns": [
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching employees found</div>",
            "emptyTable":     "<div class='alert alert-info'>No employees found</div>"
        }
});

$('#expired_contract_employee_list').DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Human_resources/expired_contract_employee_list/",
            type: 'POST'
        },
        "columns": [
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching employees found</div>",
            "emptyTable":     "<div class='alert alert-info'>No employees found</div>"
        }
});

$('#closed_contract_employee_list').DataTable({
        colReorder: true,
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Human_resources/closed_contract_employee_list/",
            type: 'POST'
        },
        "columns": [
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching employees found</div>",
            "emptyTable":     "<div class='alert alert-info'>No employees found</div>"
        }
});

function save_employee_credentials(){
    var user_account_details_tab = $('#user_account_details');

    var password = user_account_details_tab.find('  input[name="password"]').val();
    var confirm_password = user_account_details_tab.find('  input[name="confirm_password"]').val();
    if(password == confirm_password) {
        var username = user_account_details_tab.find('  input[name="username"]').val();
        var user_id = user_account_details_tab.find('  input[name="user_id"]').val();
        var employee_id = user_account_details_tab.find('  input[name="employee_id"]').val();
        var permissions_ids = Array();
        var active = user_account_details_tab.find('  input[name="active"]').is(":checked") ? 1 : 0;

        var i = 0;
        user_account_details_tab.find(' .employee_permissions').each(function () {
            if ($(this).is(":checked")) {
                permissions_ids[i] = $(this).val();
                i++;
            }
        });
        $.post(
            base_url + "human_resources/save_user/",
            {
                username: username,
                user_id: user_id,
                employee_id: employee_id,
                password: password,
                active: active,
                permission_ids: permissions_ids
            }, function (data) {
                user_account_details_tab.html(data);
            }
        ).complete();
    }
}

$('#departments_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resources/departments/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching departments found</div>",
        "emptyTable":     "<div class='alert alert-info'>No departments found</div>"
    },"drawCallback": function () {

        //Save department
        var save_department = function (button){
            var modal = button.closest('.modal');
            modal.modal('hide');
            var department_name = modal.find('input[name="department_name"]').val();
            var department_id = modal.find('input[name="department_id"]').val();
            var description = modal.find('textarea[name="description"]').val();

            $.post(
                base_url + "human_resources/save_department/",
                {
                    department_name: department_name,
                    department_id: department_id,
                    description:description
                }
            ).complete(function(){
                modal.find('form')[0].reset();
                $('#departments_list').DataTable().draw('page');
            });
        };

        $('.save_department_button').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function(){
                    save_department(button);
                });
                button.attr('active','true');
            }
        });

        //Delete Department
        $('.delete_department').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                var department_id = button.attr('department_id');
                button.click(function () {
                    if(confirm('Are you sure?')) {
                        $.post(
                            base_url + "human_resources/delete_department",
                            {
                                department_id: department_id
                            }
                        ).complete(function () {
                            $('#departments_list').DataTable().draw('page');
                        });
                    }
                });
                button.attr('initialized','true');
            }
        });

        initialize_common_js();
    }
});

$('#job_positions_list').DataTable({
    colReorder: true,
    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resources/job_positions/",
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching positions found</div>",
        "emptyTable":     "<div class='alert alert-info'>No positions found</div>"
    },"drawCallback": function () {

        //Save Job Position
        var save_job_position = function (button){
            var modal = button.closest('.modal');
            modal.modal('hide');
            var position_name = modal.find('input[name="position_name"]').val();
            var position_id = modal.find('input[name="position_id"]').val();
            var description = modal.find('textarea[name="description"]').val();

            $.post(
                base_url + "human_resources/save_job_position/",
                {
                    position_name: position_name,
                    position_id: position_id,
                    description:description
                }
            ).complete(function(){
                modal.find('form')[0].reset();
                $('#job_positions_list').DataTable().draw('page');
            });
        };

        $('.save_job_position_button').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function(){
                    save_job_position(button);
                });
                button.attr('active','true');
            }
        });

        //Delete Job Position
        $('.delete_job_position').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                var position_id = button.attr('position_id');
                button.click(function () {
                    if(confirm('Are you sure?')) {
                        $.post(
                            base_url + "human_resources/delete_job_position",
                            {
                                position_id: position_id
                            }
                        ).complete(function () {
                            $('#job_positions_list').DataTable().draw('page');
                        });
                    }
                });
                button.attr('initialized','true');
            }
        });

        initialize_common_js();
    }
});


$('a[href="#casual_labour_types"]').on('shown.bs.tab', function (e){
        $('#casual_labour_type_list').each(function () {
            var table = $(this);
            if(table.attr('initialized') != 'true'){

                table.DataTable({
                    colReorder: true,
                    "processing": true,
                    "serverSide": true,
                    "ajax" : {
                        url: base_url + "human_resources/casual_labour_types_list/",
                        type: 'POST'
                    },
                    "columns": [
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": false}
                    ],
                    "language": {
                        "zeroRecords":     "<div class='alert alert-info'>No matching types found</div>",
                        "emptyTable":     "<div class='alert alert-info'>No types found</div>"
                    },"drawCallback": function () {
                        //Save Category
                        $('.save_casual_labour_type_button').each(function(){
                            var button = $(this);
                            if(button.attr('active') != 'true') {
                                var modal = button.closest('.modal');
                                var type_id = modal.find('input[name="type_id"]').val();

                                button.click(function (){
                                    var name = modal.find('input[name="name"]').val();

                                    if(name != '') {
                                        modal.modal('hide');
                                        var description = modal.find('textarea[name="description"]').val();

                                        $.post(
                                            base_url + "human_resources/save_casual_labour_type",
                                            {
                                                type_id: type_id,
                                                name: name,
                                                description: description
                                            }, function () {
                                                modal.find('form')[0].reset();
                                                table.DataTable().draw('page');
                                            }
                                        );
                                    }
                                });
                                button.attr('active','true');
                            }
                        });

                        //Delete Category
                        $('.delete_casual_labour_type').each(function () {
                            var button = $(this);
                            var type_id = button.attr('type_id');
                            if(button.attr('initialized') != 'true'){
                                button.click(function () {
                                    if(confirm('Are you sure?')) {
                                        $.post(
                                            base_url + "human_resources/delete_casual_labour_type/",
                                            {
                                                type_id: type_id
                                            }
                                        ).complete(function () {
                                            table.DataTable().draw('page');
                                        });
                                    }
                                });
                            }
                        });

                        initialize_common_js();
                    }
                });

                table.attr('initialized','true');
            } else {
                table.DataTable().draw('page');
            }
        });
});

/***************************
 *         EMPLOYEE_CONTRACT
 ***************************/
$('#employee_contracts').each(function(){
    var employee_id = $(this).attr('employee_id');
$(this).DataTable({

    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Contracts/employee_contract_list/"+employee_id,
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching Employee Contract found</div>",
        "emptyTable":     "<div class='alert alert-info'>No  Employee Contract found</div>"
    },
    "drawCallback": function () {

     $('.contract_form').on('show.bs.modal', function(e){
         var modal = $(this);

         var initialize_row_remover = function(container){
             container.find('.row_remover').click(function () {
                 var button = $(this);
                 var employee_allowance_id = button.attr('employee_allowance_id');
                 if (button.attr('initialized') != 'true' && employee_allowance_id != '') {
                     $.confirm({
                             title: 'Delete This Allowance',
                             content: 'This action is irreversible! Are you sure?',
                             buttons: {
                                 confirm: {
                                     text: 'Confirm Delete',
                                     btnClass: 'btn btn-danger',
                                     action: function () {
                                         start_spinner();
                                         $.post(base_url + "human_resource/Contracts/clear_allowance",
                                         {
                                             employee_allowance_id: employee_allowance_id
                                         }, function (data) {
                                             button.closest('tr').remove();
                                             stop_spinner();
                                         });
                                     }
                                 },
                                 cancel: {text: "Cancel", btnClass: 'btn btn-default'}
                             }
                         });
                 } else {
                     button.closest('tr').remove();
                 }

                 initialize_common_js();
             });
         };

         initialize_row_remover(modal);
         modal.find('.row_adder').each(function () {
             $(this).click(function () {
                 var tbody = $(this).closest('table').find('tbody');
                 var new_row = tbody.closest('table').find('.row_template').clone().removeAttr('style')
                     .removeClass('row_template').addClass('artificial_row').appendTo(tbody);
                 new_row.find('.number_format').priceFormat();
                 new_row.find('select').select2({width: '100%'});

                 initialize_row_remover(new_row);
                 initialize_common_js();
             });
         });
     });

        //Save Employee Contract
       $('.save_contract_button').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');

                    /* employee_contract_id    payroll_no    salary    subsistence    responsibility   currency
                     payment_mode tax_details ssf_contribution start_date  end_date  created_at   created_by */

                    /* employee_contract_id  department_id  job_position_id  branch_id  start_date end_date created_at  created_by */

                    var employee_id = modal.find("input[name='employee_id']").val();
                    var employee_contract_id = modal.find("input[name='employee_contract_id']").val();
                    var employee_salary_id=  modal.find("input[name='employee_salary_id']").val();
                    var employee_designation_id= modal.find("input[name='employee_designation_id']").val();
                    var start_date = modal.find("input[name='start_date']").val();
                    var end_date = modal.find("input[name='end_date']").val();
                         //salary
                    var payroll_no = modal.find("input[name='payroll_no']").val();
                    var salary = modal.find("input[name='salary']").unmask();
                    var tax_details = modal.find("select[name='tax_details']").val();
                    var currency = modal.find("select[name='currency']").val();
                    var payment_mode = modal.find("select[name='payment_mode']").val();
                    var ssf_contribution = modal.find("select[name='ssf_contribution']").val();
                        //designation
                    var department_id = modal.find("select[name='department_id']").val();
                    var job_position_id = modal.find("select[name='job_position_id']").val();
                    var branch_id = modal.find("select[name='branch_id']").val();

                    var allowances_ids  = new Array(), allowances_amounts = new Array(), employee_allowance_ids = new Array(); i = 0;

                    var tbody = modal.find('tbody');
                    tbody.find('input[name="allowance_amount"]').each(function(){
                        var row = $(this).closest('tr');
                        var allowance_amount = $(this);
                        var allowance_id = row.find('select[name="allowance_id"]').val();
                        var allowance_amount = allowance_amount.unmask();
                        var employee_allowance_id = row.find("input[name='employee_allowance_id']").val();

                        if(allowance_id != '' && allowance_amount > 0){
                            allowances_ids[i] = allowance_id;
                            allowances_amounts[i] = allowance_amount;
                            employee_allowance_ids[i] = employee_allowance_id;
                            i++;
                        }
                    });

                    if(start_date != '' && end_date != '' && salary != '' && payment_mode != '' && job_position_id != '' && branch_id != ''

                    && tax_details != '' && currency != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/contracts/save_employee_contract",
                            {
                                employee_id:employee_id,
                                employee_contract_id:employee_contract_id,
                                employee_salary_id:employee_salary_id,
                                employee_designation_id:employee_designation_id,
                                start_date:start_date,
                                end_date:end_date,
                                payroll_no:payroll_no,
                                salary:salary,
                                tax_details:tax_details,
                                currency:currency,
                                payment_mode:payment_mode,
                                ssf_contribution:ssf_contribution,
                                department_id:department_id,
                                job_position_id:job_position_id,
                                branch_id:branch_id,
                                allowances_ids:allowances_ids,
                                allowances_amounts:allowances_amounts,
                                employee_allowance_ids:employee_allowance_ids

                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#employee_contracts').DataTable().draw('page');
                                toast('success','Contract Added successful ');
                            }
                        );
                    } else {
                        display_form_fields_error();
                    }
                });
                button.attr('initialized','true');
            }
        });

        $('.delete_employee_contract').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Contracts/delete_employee_contract",
                            {
                                contract_id: button.attr('contract_selected_id')

                            }, function () {
                                $('#employee_contracts').DataTable().draw('page');
                            }
                        ).complete(function(){
                            toast('success','Contract Deleted ');
                            stop_spinner();
                        });
                    }
                });
                button.attr('active', 'true');
            }
        });

     $('.close_contract_button').each(function () {
            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var employee_contract_id = modal.find("input[name='employee_contract_id']").val();
                    var close_date = modal.find("input[name='close_date']").val();
                    var reason = modal.find("textarea[name='reason']").val();
                    var attachment = modal.find("input[name='attachment']").val();

                    console.log(employee_contract_id,close_date,reason,attachment);
                    if(close_date != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Contracts/close_employee_contract",
                            {
                                employee_contract_id:employee_contract_id,
                                close_date:close_date,
                                reason:reason,
                                attachement:attachment,
                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#employee_contracts').DataTable().draw('page');
                                toast('success','Contract Closed successful ');
                            }
                        );
                    } else {
                        display_form_fields_error();
                    }
                });
                button.attr('initialized','true');
            }
        });

     $('.activate_employee_contract').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure you want to activate?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Contracts/activate_employee_contract",
                            {
                                contract_id: button.attr('activate_contract_id'),

                            }, function () {
                                $('#employee_contracts').DataTable().draw('page');
                            }
                        ).complete(function(){
                            toast('success','Contract Activated ');
                            stop_spinner();
                        });
                    }
                });
                button.attr('active', 'true');
            }
        });

     

     $('.view_employee_contract').each(function(){
            var button = $(this);
            var tab_pane=button.closest('.tab-pane');
            if(button.attr('active') != 'true') {
                button.click(function () {
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Contracts/employee_contract_details",
                            {
                                employee_contract_id: button.attr('employee_contract_id'),

                            }, function (data) {

                               //alert(data.content);
                                 tab_pane.html(data.content);
                                 initialize_common_js();

                            },'json'
                        ).complete(function(){
                            //salary list

                             employee_salary_list();
                             employee_designation_list();

                            //end salary list

                            stop_spinner();
                        });

                });
                button.attr('active', 'true');
            }
        });
/*
     $('.view_employee_contract').each(function(){
            var button = $(this);
            var tab_pane=button.closest('.tab-pane');
            if(button.attr('active') != 'true') {
                button.click(function () {
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Contracts/employee_contract_details",
                            {
                                employee_contract_id: button.attr('employee_contract_id'),

                            }, function (data) {

                               //alert(data.content);
                                 tab_pane.html(data.content);
                                 initialize_common_js();

                            },'json'
                        ).complete(function(){
                            //designation list

                             employee_designation_list();

                            //end designation list

                            stop_spinner();
                        });

                });
                button.attr('active', 'true');
            }
        });
*/


        $(this).find('tr').each(function () {
            $(this).find('td:last-child').attr('nowrap', 'nowrap');
        });

        initialize_common_js();
    }

    });
 });

$('#employee_ssf').each(function(){
    var employee_id = $(this).attr('employee_id');
$(this).DataTable({

    "processing": true,
    "serverSide": true,
    "ajax" : {
        url: base_url + "human_resource/Contracts/employee_ssf_list/"+employee_id,
        type: 'POST'
    },
    "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
    ],
    "language": {
        "zeroRecords":     "<div class='alert alert-info'>No matching Employee SSf found</div>",
        "emptyTable":     "<div class='alert alert-info'>No  Employee SSF found</div>"
    },
    "drawCallback": function () {
        //Save Employee SSF

        $('.save_employee_ssf_button').each(function () {

            var button = $(this);
            if(button.attr('initialized') != 'true'){
                button.click(function () {
                    var modal = button.closest('.modal');
                    var employee_id = modal.find("input[name='employee_id']").val();
                    var employee_ssf_id = modal.find("input[name='employee_ssf_id']").val();
                    var ssf_id = modal.find("select[name='ssf_id']").val();
                    var ssf_no = modal.find("input[name='ssf_no']").val();
                    var start_date = modal.find("input[name='start_date']").val();

                    console.log(employee_id,employee_ssf_id,ssf_no,ssf_id,start_date);
                    if(ssf_id != ''){
                        start_spinner();
                        modal.modal('hide');
                        $.post(
                            base_url + "human_resource/Contracts/save_employee_ssf",
                            {
                                employee_id:employee_id,
                                employee_ssf_id:employee_ssf_id,
                                ssf_id:ssf_id,
                                ssf_no:ssf_no,
                                start_date:start_date
                            },function () {
                                stop_spinner();
                                modal.find('form')[0].reset();
                                $('#employee_ssf').DataTable().draw('page');
                                toast('success','SSF Added successful ');
                            }
                        );
                    } else {
                        display_form_fields_error();
                    }
                });
                button.attr('initialized','true');
            }
        });

        //Delete Employee SSF
        $('.delete_employee_ssf').each(function(){
            var button = $(this);
            if(button.attr('active') != 'true') {
                button.click(function () {
                    if(confirm('Are you sure?')){
                        start_spinner();
                        $.post(
                            base_url + "human_resource/Contracts/delete_employee_ssf",
                            {
                                employee_ssf_id: button.attr('employee_ssf_id')

                            }, function () {
                                $('#employee_ssf').DataTable().draw('page');
                            }
                        ).complete(function(){
                            toast('success','Employee SSF Deleted ');
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
 });

$('#employee_bank').each(function(){
    var employee_id = $(this).attr('employee_id');
    $(this).DataTable({

        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "human_resource/Contracts/employee_bank_list/"+employee_id,
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
            {"orderable": false}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching Employee BANK found</div>",
            "emptyTable":     "<div class='alert alert-info'>No  Employee BANK found</div>"
        },

         "drawCallback": function () {

        //Save Employee BANK

             $('.save_employee_bank_button').each(function () {

                 var button = $(this);
                 if(button.attr('initialized') != 'true'){
                     button.click(function () {
                         var modal = button.closest('.modal');


                         var employee_id = modal.find("input[name='employee_id']").val();
                         var employee_bank_id = modal.find("input[name='employee_bank_id']").val();
                         var bank_id = modal.find("select[name='bank_id']").val();
                         var account_no = modal.find("input[name='account_no']").val();
                         var branch = modal.find("input[name='branch']").val();
                         var swift_code = modal.find("input[name='swift_code']").val();
                         var start_date = modal.find("input[name='start_date']").val();

                         console.log(employee_id,employee_bank_id,bank_id,account_no,branch,swift_code,start_date);
                         if(bank_id != ''){
                             start_spinner();
                             modal.modal('hide');
                             $.post(
                                 base_url + "human_resource/Contracts/save_employee_bank",
                                 {
                                     employee_id:employee_id,
                                     employee_bank_id:employee_bank_id,
                                     bank_id:bank_id,
                                     account_no:account_no,
                                     branch:branch,
                                     swift_code:swift_code,
                                     start_date:start_date
                                 },function () {
                                     stop_spinner();
                                     modal.find('form')[0].reset();
                                     $('#employee_bank').DataTable().draw('page');
                                     toast('success','BANK Added successful ');
                                 }
                             );
                         } else {
                             display_form_fields_error();
                         }
                     });
                     button.attr('initialized','true');
                 }
             });

             //Delete Employee BANK
             $('.delete_employee_bank').each(function(){
                 var button = $(this);
                 if(button.attr('active') != 'true') {
                     button.click(function () {
                         if(confirm('Are you sure?')){
                             start_spinner();
                             $.post(
                                 base_url + "human_resource/Contracts/delete_employee_bank",
                                 {
                                     employee_bank_id: button.attr('employee_bank_id')

                                 }, function () {
                                     $('#employee_bank').DataTable().draw('page');
                                 }
                             ).complete(function(){
                                 toast('success','Employee BANK Deleted ');
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
});

function employee_salary_list(){

           $('.employee_salary_list').each(function () {

            var employee_contract_id = $(this).attr('employee_contract_id');
            var table = $(this);
            var panel_body = table.closest('.panel-body');
            var load_salary_content = function () {
                start_spinner();
                $.post(
                    base_url + "human_resource/contracts/employee_contract_salary_list",
                    {
                        employee_contract_id: employee_contract_id

                    }, function (data) {

                        table.html(data.salary_table);
                       
                            //Save Employee Salary

                            $('.save_salary_button').each(function () {

                                var button = $(this);
                                if(button.attr('initialized') != 'true'){
                                    button.click(function () {
                                        var modal = button.closest('.modal');
                                        var employee_contract_id = modal.find("input[name='employee_contract_id']").val();
                                        var employee_salary_id=  modal.find("input[name='employee_salary_id']").val();
                                        var start_date = modal.find("input[name='start_date']").val();
                                        var end_date = modal.find("input[name='end_date']").val();
                                        var payroll_no = modal.find("input[name='payroll_no']").val();
                                        var salary = modal.find("input[name='salary']").unmask();
                                        var tax_details = modal.find("select[name='tax_details']").val();
                                        var subsistance = modal.find("input[name='subsistance']").unmask();
                                        var responsibility = modal.find("input[name='responsibility']").unmask();
                                        var currency_id = modal.find("select[name='currency_id']").val();
                                        var payment_mode = modal.find("select[name='payment_mode']").val();
                                        var ssf_contribution = modal.find("select[name='ssf_contribution']").val();

                                       
                                        if(start_date != ''){
                                            start_spinner();
                                            modal.modal('hide');
                                            $.post(
                                                base_url + "human_resource/contracts/save_employee_salary",
                                                {
                                                    
                                                    employee_contract_id:employee_contract_id,
                                                    employee_salary_id:employee_salary_id,
                                                    start_date:start_date,
                                                    end_date:end_date,
                                                    payroll_no:payroll_no,
                                                    salary:salary,
                                                    tax_details:tax_details,
                                                    subsistance:subsistance,
                                                    responsibility:responsibility,
                                                    currency_id:currency_id,
                                                    payment_mode:payment_mode,
                                                    ssf_contribution:ssf_contribution


                                                },function () {
                                                    stop_spinner();
                                                    modal.find('form')[0].reset();
                                                    load_salary_content();
                                                    toast('success','Salary Added successful ');
                                                }
                                            );
                                        } else {
                                            display_form_fields_error();
                                        }
                                    });
                                    button.attr('initialized','true');
                                }
                            });

                        //end save employee salary

                         $('.delete_employee_salary').each(function(){
                            var button = $(this);
                            if(button.attr('active') != 'true') {
                                button.click(function () {
                                    if(confirm('Are you sure?')){
                                        start_spinner();
                                        $.post(
                                            base_url + "human_resource/contracts/delete_employee_salary",
                                            {
                                                employee_salary_id: button.attr('employee_salary_id')

                                            }, function () {

                                                 load_salary_content();
                                            }
                                        ).complete(function(){
                                            toast('success','Salary Deleted  Successfully');
                                            stop_spinner();
                                        });
                                    }
                                });
                                button.attr('active', 'true');
                            }
                        });

                        stop_spinner();
                        initialize_common_js();
                    },
                    'json'
                ).complete();
            };
            
            load_salary_content();
            
         });

}

function employee_designation_list(){

           $('.employee_designation_list').each(function () {

            var employee_contract_id = $(this).attr('employee_contract_id');
            var table = $(this);
            var panel_body = table.closest('.panel-body');
            var panel_body = table.closest('.panel-body');
            var load_designation_content = function () {
                start_spinner();
                $.post(
                    base_url + "human_resource/contracts/employee_contract_designation_list",
                    {
                        employee_contract_id: employee_contract_id

                    }, function (data) {

                        table.html(data.designation_table);

                            //Save Employee Designation

                            $('.save_designation_button').each(function () {

                                var button = $(this);
                                if(button.attr('initialized') != 'true'){
                                    button.click(function () {
                                        var modal = button.closest('.modal');
                                        var employee_contract_id = modal.find("input[name='employee_contract_id']").val();
                                        var employee_designation_id=  modal.find("input[name='employee_designation_id']").val();
                                        var start_date = modal.find("input[name='start_date']").val();
                                        var end_date = modal.find("input[name='end_date']").val();
                                        var department_id = modal.find("select[name='department_id']").val();
                                        var job_position_id = modal.find("select[name='job_position_id']").val();
                                        var branch_id = modal.find("select[name='branch_id']").val();


                                        if(job_position_id != ''){
                                            start_spinner();
                                            modal.modal('hide');
                                            $.post(
                                                base_url + "human_resource/contracts/save_employee_designation",
                                                {

                                                    employee_contract_id:employee_contract_id,
                                                    employee_designation_id:employee_designation_id,
                                                    start_date:start_date,
                                                    end_date:end_date,
                                                    department_id:department_id,
                                                    job_position_id:job_position_id,
                                                    branch_id:branch_id

                                                },function () {
                                                    stop_spinner();
                                                    modal.find('form')[0].reset();
                                                    load_designation_content();
                                                    toast('success','Designation Added successful ');
                                                }
                                            );
                                        } else {
                                            display_form_fields_error();
                                        }
                                    });
                                    button.attr('initialized','true');
                                }
                            });

                        //end save employee designation

                         $('.delete_employee_designation').each(function(){
                            var button = $(this);
                            if(button.attr('active') != 'true') {
                                button.click(function () {
                                    if(confirm('Are you sure?')){
                                        start_spinner();
                                        $.post(
                                            base_url + "human_resource/contracts/delete_employee_designation",
                                            {
                                                employee_designation_id: button.attr('employee_designation_id')

                                            }, function () {

                                                 load_designation_content();
                                            }
                                        ).complete(function(){
                                            toast('success','Designation Deleted  Successfully');
                                            stop_spinner();
                                        });
                                    }
                                });
                                button.attr('active', 'true');
                            }
                        });

                        stop_spinner();
                        initialize_common_js();
                    },
                    'json'
                ).complete();
            };

            load_designation_content();

         });

}


/***************************
 *       PAYROLL
 ***************************/

var d_id = '';
var p_date = '';

$('#generate_payroll').each( function(){
    var button = $(this);

    button.click(function () {
        $('#payroll_container').html('');
        var department_id = button.closest('form').find('select[name="department_id"]').val();
        var payroll_date = button.closest('form').find('input[name="payroll_date"]').val();

            if(department_id != '' && payroll_date != ''){
                start_spinner();

                function generate_payroll(employee_id, terminating_date) {
                    $.post(
                        base_url + "/human_resource/human_resources/payroll",
                        {
                            gererate_payroll: true,
                            department_id: department_id,
                            payroll_date: payroll_date,
                            employee_id: employee_id,
                            terminating_date: terminating_date
                        },function (data) {
                            stop_spinner();
                            $('#payroll_container').html(data);
                            $.post(
                                base_url + "/human_resource/human_resources/check_payroll",
                                {
                                    department_id: department_id,
                                    payroll_date: payroll_date,
                                },function(data){
                                    if(data == '0'){
                                        $('#approve_div').removeAttr('style').trigger('change');
                                    }else{
                                        $('#approve_div').attr('style', 'display: none').trigger('change');
                                    }

                                    $.post(
                                        base_url + "/human_resource/human_resources/check_special_levels",
                                        {
                                            department_id: department_id
                                        },function(data){
                                          $('#special_level_approval').html(data).trigger('change');


                                            $('#special_level_approval').each(function(){
                                                var level = $(this).find('select[name="special_level_approval"]');
                                                level.change(function(){
                                                    var special_level_id = $(this).val();
                                                    $('#submit_payroll_for_approval').attr('special_level_id', special_level_id);
                                                });

                                            })

                                          initialize_common_js();
                                        }
                                    )

                                }
                            )
                            $('#submit_payroll_for_approval').attr('department_id', department_id);
                            d_id = department_id;
                            p_date = payroll_date;

                           //// $('#submit_payroll_for_approval').attr('payroll_date', payroll_date);

                            $('.flaged').click(function(){
                                var recalculate_button = $(this);
                                var employee_id = recalculate_button.attr('id');
                                var terminating_date = recalculate_button.attr('terminating_date');
                                var terminating_date_modified = recalculate_button.attr('terminating_date_modified');
                                var worked_days = recalculate_button.attr('worked_days');

                                $.confirm({
                                    title: 'Re-Calculate Gross Salary for '+worked_days+' Days.?',
                                    content: 'This employee worked until '+terminating_date_modified,
                                    buttons: {
                                        confirm: {
                                            text: 'Re-Calculate',
                                            btnClass: 'btn btn-info btn-xs',
                                            action: function(){
                                                generate_payroll(employee_id, terminating_date);
                                            }
                                        },
                                        cancel: {
                                            text: "Cancel",
                                            btnClass: 'btn btn-default btn-xs'
                                        }
                                    }
                                });

                                ////alert(employee_id + ' # '+ returned_department_id+' # '+returned_payroll_date);
                            });

                            initialize_common_js();
                        }
                    );
                }

                generate_payroll('','');
                initialize_common_js();

            }else{
                display_form_fields_error();
            }

    });
});

$('#submit_payroll_for_approval').each(function(){
    var button = $(this);

    button.click(function(){
        var special_level_id = button.attr('special_level_id');

        $.post(
            base_url + "/human_resource/human_resources/payroll",
            {
                department_id: d_id,
                payroll_date: p_date,
                special_level_id: special_level_id,
                submit:true
            },function(){
                toast('success','Payroll Submitted');
                $('#approve_div').attr('style', 'display: none').trigger('change');
            }
        )
    })



});

function load_department_containers() {
    $.post(
        base_url + "/human_resource/human_resources/departments_list",
        {
            data_to_send: true

        },function (data) {
            $('#payrolls').html(data);

            $('.department_button').each(function () {
                var button = $(this);
                var department_id = button.attr('department_id');
                    button.click(function () {
                        $.post(
                            base_url + "/human_resource/human_resources/payroll_list",
                            {
                                department_id: department_id

                            },function (data) {
                              $("#div"+department_id).html(data);

                              $('.payroll_button'+department_id).each(function () {
                                  var button = $(this);
                                  var payroll_id = button.attr('payroll_id');
                                  var payroll_for = button.attr('payroll_for');
                                  var payroll_id = button.attr('payroll_id');
                                  if (payroll_id != '') {

                                          $.post(
                                              base_url + "/human_resource/human_resources/payroll_list_display",
                                              {
                                                  department_id: department_id,
                                                  payroll_id: payroll_id,
                                                  payroll_date: payroll_for + '-1',
                                                  payroll_id: payroll_id

                                              }, function (data) {
                                                  $("#div" + department_id + payroll_id).html(data);
                                                  $('#submit_payroll_for_final_approval' + department_id + payroll_id).each(function () {
                                                      var button = $(this);
                                                      var payroll_id = button.attr('payroll_id');
                                                      var current_level = button.attr('current_level');
                                                      button.click(function () {

                                                          $.confirm({
                                                              title: 'Submit',
                                                              content: '' +
                                                                  '<form action="" class="formName">' +
                                                                  '<div class="form-group">' +
                                                                  '<label>Date</label>' +
                                                                  '<input type="date" class="form-control submission_date " />' +
                                                                  '</div>' +
                                                                  '<div class="form-group">' +
                                                                  '<label>Coments</label>' +
                                                                  '<textarea class="coments form-control" />' +
                                                                  '</div>' +
                                                                  '</form>',
                                                              buttons: {
                                                                  formSubmit: {
                                                                      text: 'Submit',
                                                                      btnClass: 'btn-blue',
                                                                      action: function () {
                                                                          var submission_date = this.$content.find('.submission_date').val();
                                                                          var coments = this.$content.find('.coments').val();
                                                                          if (!submission_date) {
                                                                              toast('error', 'Date cannot be empty');
                                                                              return false;
                                                                          } else {
                                                                              $.post(
                                                                                  base_url + "/human_resource/human_resources/payroll_approval",
                                                                                  {
                                                                                      payroll_id: payroll_id,
                                                                                      current_level: current_level,
                                                                                      approval_date: submission_date,
                                                                                      coments: coments,
                                                                                      status: 'Verified',
                                                                                      is_final: 0
                                                                                  }, function () {
                                                                                      toast('success', 'Payroll submitted');
                                                                                      load_department_containers();
                                                                                  }
                                                                              )
                                                                          }
                                                                      }
                                                                  },
                                                                  cancel: function () {
                                                                      //close
                                                                  },
                                                              },
                                                              onContentReady: function () {
                                                                  // bind to events
                                                                  var jc = this;
                                                                  this.$content.find('form').on('submit', function (e) {
                                                                      // if the user submits the form by pressing enter in the field.
                                                                      e.preventDefault();
                                                                      jc.$$formSubmit.trigger('click'); // reference the button and click it
                                                                  });
                                                              }
                                                          });

                                                      });
                                                  })


                                                  $('#reject_payroll' + department_id + payroll_id).each(function () {
                                                      var button = $(this);
                                                      var payroll_id = button.attr('payroll_id');
                                                      var current_level = button.attr('current_level');
                                                      button.click(function () {

                                                          $.confirm({
                                                              title: 'Reject',
                                                              content: '' +
                                                                  '<form action="" class="formName">' +
                                                                  '<div class="form-group">' +
                                                                  '<label>Coments</label>' +
                                                                  '<textarea class="coments form-control" />' +
                                                                  '</div>' +
                                                                  '</form>',
                                                              buttons: {
                                                                  formSubmit: {
                                                                      text: 'Submit',
                                                                      btnClass: 'btn-blue',
                                                                      action: function () {
                                                                          var coments = this.$content.find('.coments').val();

                                                                          $.post(
                                                                              base_url + "/human_resource/human_resources/reject_payroll",
                                                                              {
                                                                                  payroll_id: payroll_id,
                                                                                  current_level: current_level,
                                                                                  coments: coments,
                                                                                  status: 'Rejected',
                                                                                  is_final: 0
                                                                              }, function () {
                                                                                  toast('success', 'Payroll Rejected');
                                                                                  load_department_containers();
                                                                              }
                                                                          )


                                                                      }
                                                                  },
                                                                  cancel: function () {
                                                                      //close
                                                                  },
                                                              },
                                                              onContentReady: function () {
                                                                  // bind to events
                                                                  var jc = this;
                                                                  this.$content.find('form').on('submit', function (e) {
                                                                      // if the user submits the form by pressing enter in the field.
                                                                      e.preventDefault();
                                                                      jc.$$formSubmit.trigger('click'); // reference the button and click it
                                                                  });
                                                              }
                                                          });

                                                      });
                                                  })


                                                  $('#payroll_final_approval' + department_id + payroll_id).each(function () {

                                                      var button = $(this);
                                                      var payroll_id = button.attr('payroll_id');
                                                      var current_level = button.attr('current_level');
                                                      button.click(function () {

                                                          $.confirm({
                                                              title: 'Submit',
                                                              content: '' +
                                                                  '<form action="" class="formName">' +
                                                                  '<div class="form-group">' +
                                                                  '<label>Date</label>' +
                                                                  '<input type="date" class="form-control submission_date " />' +
                                                                  '</div>' +
                                                                  '<div class="form-group">' +
                                                                  '<label>Coments</label>' +
                                                                  '<textarea class="coments form-control" />' +
                                                                  '</div>' +
                                                                  '</form>',
                                                              buttons: {
                                                                  formSubmit: {
                                                                      text: 'Submit',
                                                                      btnClass: 'btn-blue',
                                                                      action: function () {
                                                                          var submission_date = this.$content.find('.submission_date').val();
                                                                          var coments = this.$content.find('.coments').val();
                                                                          if (!submission_date) {
                                                                              toast('error', 'Date cannot be empty');
                                                                              return false;
                                                                          }

                                                                          $.post(
                                                                              base_url + "/human_resource/human_resources/payroll_approval",
                                                                              {
                                                                                  payroll_id: payroll_id,
                                                                                  current_level: current_level,
                                                                                  approval_date: submission_date,
                                                                                  coments: coments,
                                                                                  status: 'Approved',
                                                                                  is_final: 1
                                                                              }, function () {
                                                                                  toast('success', 'Payroll submitted');
                                                                                  load_department_containers();
                                                                              }
                                                                          )


                                                                      }
                                                                  },
                                                                  cancel: function () {
                                                                      //close
                                                                  },
                                                              },
                                                              onContentReady: function () {
                                                                  // bind to events
                                                                  var jc = this;
                                                                  this.$content.find('form').on('submit', function (e) {
                                                                      // if the user submits the form by pressing enter in the field.
                                                                      e.preventDefault();
                                                                      jc.$$formSubmit.trigger('click'); // reference the button and click it
                                                                  });
                                                              }
                                                          });
                                                      });
                                                  })


                                              })
                                      }
                              });

                              $('.payroll_status').each(function (){
                                var button = $(this);
                                var payroll_id = button.attr('payroll_id');

                                if(button.attr('initialized') != 'true'){
                                    button.click(function(){
                                        $.post(
                                            base_url + "/human_resource/human_resources/check_payroll_status",
                                            {
                                                payroll_id:payroll_id
                                            },function(data){
                                                var results = data.split('@');

                                                $.confirm({
                                                    title: results[0],
                                                    content: results[1],
                                                    buttons: {
                                                        confirm: {
                                                            text: 'OK',
                                                            btnClass: 'btn btn-info btn-xs',
                                                            action: function () {

                                                            }
                                                        }
                                                    }
                                                });

                                            }
                                        )
                                    });
                                    button.attr('initialize', 'true');
                                }

                              });


                            }
                        )
                    });

            })

        }
    );
}

$('#payrolls').each( function (){
    load_department_containers();
});

$('#payrolls_button').each(function(){
   var button = $(this);
   button.click(function(){
       load_department_containers();
   });
});

function draw_payroll_loan_repay_table(payroll_id) {
    $.post(
        base_url + "/human_resource/human_resources/chek_payroll_payemts/",
        {
            payroll_id:payroll_id
        },function (data) {

            var table = $('.employee_loan_repay_table');
            var result = data.split('-');

            if(result[0] == '0'){
                $('#receive_advance').removeAttr('style').trigger('change');
            }else if(result[0] == '2'){
                $('#receive_advance').attr('style', 'display: none').trigger('change');
                $('#preview_advance').attr('style', 'display: none').trigger('change');
            }else{
                $('#receive_advance').attr('style', 'display: none').trigger('change');
                $('#preview_advance').removeAttr('style').trigger('change');
            }
            if(result[1] == '0'){
                $('#receive_heslb').removeAttr('style').trigger('change');
            }else if(result[1] == '2'){
                $('#receive_heslb').attr('style', 'display: none').trigger('change');
                $('#preview_heslb').attr('style', 'display: none').trigger('change');
            }else{
                $('#receive_heslb').attr('style', 'display: none').trigger('change');
                $('#preview_heslb').removeAttr('style').trigger('change');
            }
            if(result[2] == '0'){
                $('#receive_company_loan').removeAttr('style').trigger('change');
            }else if(result[2] == '2'){
                $('#receive_company_loan').attr('style', 'display: none').trigger('change');
                $('#preview_company').attr('style', 'display: none').trigger('change');
            }else{
                $('#receive_company_loan').attr('style', 'display: none').trigger('change');
                $('#preview_company').removeAttr('style').trigger('change');
            }

            $('#tfooter').removeAttr('style').trigger('change');
    $('#payroll_repay_div').removeAttr('style').trigger('change');

    $('.employee_loan_repay_table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax" : {
            url: base_url + "/human_resource/human_resources/payroll_loan_payments/"+payroll_id,
            type: 'POST'
        },
        "columns": [
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true},
            {"orderable": true}
        ],
        "language": {
            "zeroRecords":     "<div class='alert alert-info'>No matching Loans found</div>",
            "emptyTable":     "<div class='alert alert-info'>No loans found</div>"
        },

        "drawCallback": function (settings) {

            table.find('#total_advance').text(settings.json.advance_total).priceFormat();
            table.find('#total_heslb').text(settings.json.heslb_total).priceFormat();
            table.find('#total_company').text(settings.json.comany_total).priceFormat();
            table.find('#advance_payroll_id').val(payroll_id);
            table.find('#heslb_payroll_id').val(payroll_id);
            table.find('#company_payroll_id').val(payroll_id);

            $.post(
                base_url + "/human_resource/human_resources/load_payroll_department/",
                {
                    payroll_id:payroll_id,
                    type: 'loans'
                },function (data) {
                    $('#payroll_loan_head_div').html(data).trigger('change');
            }
            )

           $('#receive_advance').each(function () {
               var advance_button = $(this);
               if(advance_button.attr('initialized') != 'true'){

                   advance_button.click(function () {

                       $.post(
                           base_url + "/human_resource/human_resources/load_accounts/",
                           {
                               data_to_sent:'TRUE'
                           },function (data) {

                               $.confirm({
                                   title: ' ',
                                   content: '' +
                                   '<form action="" class="formName">' +
                                   '<div class="form-group">' +
                                   '<label>Date</label>' +
                                   '<input type="date" class="form-control submission_date " />' +
                                   '</div>' +
                                   '<div class="form-group">' +
                                   '<label>Dr Account</label>' +
                                    data +
                                   '</div>' +
                                   '<div class="form-group">' +
                                   '<label>Remarks</label>' +
                                   '<textarea class="coments form-control" />' +
                                   '</div>' +
                                   '</form>',
                                   buttons: {
                                       formSubmit: {
                                           text: 'Submit',
                                           btnClass: 'btn-blue',
                                           action: function () {
                                               var submission_date = this.$content.find('.submission_date').val();
                                               var dr_account = this.$content.find('select[name="dr_account"]').val();
                                               var coments = this.$content.find('.coments').val();
                                               if(submission_date != '' && dr_account != ''){
                                                   start_spinner();
                                                   $.post(
                                                       base_url + "/human_resource/human_resources/payroll_advance_payment_repay/",
                                                       {
                                                           payroll_id:payroll_id,
                                                           received_date: submission_date,
                                                           dr_account: dr_account,
                                                           coments: coments
                                                       },function () {
                                                           stop_spinner();
                                                           $('#receive_advance').attr('style', 'display: none').trigger('change');
                                                           $('#preview_advance').removeAttr('style').trigger('change');
                                                           toast('success', 'RECEIVED');
                                                       }
                                                   )

                                               }else{
                                                   toast('error', 'Date cannot be empty');
                                                   return false;
                                               }


                                           }
                                       },
                                       cancel: function () {
                                           //close
                                       },
                                   },
                                   onContentReady: function () {
                                       // bind to events
                                       var jc = this;
                                       this.$content.find('form').on('submit', function (e) {
                                           // if the user submits the form by pressing enter in the field.
                                           e.preventDefault();
                                           jc.$$formSubmit.trigger('click'); // reference the button and click it
                                       });
                                   }
                               });

                           }
                       )


                   });
                   advance_button.attr('initialized','true');
               }

           })
           $('#receive_heslb').each(function () {
               var heslb_button = $(this);
               if(heslb_button.attr('initialized') != 'true'){

                   heslb_button.click(function () {

                       $.post(
                           base_url + "/human_resource/human_resources/load_accounts/",
                           {
                               data_to_sent:'heslb'
                           },function (data) {

                               $.confirm({
                                   title: ' ',
                                   content: '' +
                                   '<form action="" class="formName">' +
                                   '<div class="form-group">' +
                                   '<label>Date</label>' +
                                   '<input type="date" class="form-control submission_date " />' +
                                   '</div>' +
                                   '<div class="form-group">' +
                                   '<label>Dr Account</label>' +
                                   data +
                                   '</div>' +
                                   '<div class="form-group">' +
                                   '<label>Rarks</label>' +
                                   '<textarea class="coments form-control" />' +
                                   '</div>' +
                                   '</form>',
                                   buttons: {
                                       formSubmit: {
                                           text: 'Submit',
                                           btnClass: 'btn-blue',
                                           action: function () {
                                               var submission_date = this.$content.find('.submission_date').val();
                                               var dr_account = this.$content.find('select[name="dr_account"]').val();
                                               var coments = this.$content.find('.coments').val();
                                               if(submission_date != '' && dr_account != ''){
                                                   start_spinner();
                                                   $.post(
                                                       base_url + "/human_resource/human_resources/payroll_heslb_payment_repay/",
                                                       {
                                                           payroll_id:payroll_id,
                                                           received_date: submission_date,
                                                           cr_account: dr_account,
                                                           coments: coments
                                                       },function () {
                                                           stop_spinner();
                                                           $('#receive_heslb').attr('style', 'display: none').trigger('change');
                                                           $('#preview_heslb').removeAttr('style').trigger('change');
                                                           toast('success', 'RECEIVED');
                                                       }
                                                   )

                                               }else{
                                                   toast('error', 'Date cannot be empty');
                                                   return false;
                                               }


                                           }
                                       },
                                       cancel: function () {
                                           //close
                                       },
                                   },
                                   onContentReady: function () {
                                       // bind to events
                                       var jc = this;
                                       this.$content.find('form').on('submit', function (e) {
                                           // if the user submits the form by pressing enter in the field.
                                           e.preventDefault();
                                           jc.$$formSubmit.trigger('click'); // reference the button and click it
                                       });
                                   }
                               });

                           }
                       )


                   });
                   heslb_button.attr('initialized','true');
               }

           })
           $('#receive_company_loan').each(function () {
               var company_button = $(this);
               if(company_button.attr('initialized') != 'true'){

                   company_button.click(function () {

                       $.post(
                           base_url + "/human_resource/human_resources/load_accounts/",
                           {
                               data_to_sent:'TRUE'
                           },function (data) {

                               $.confirm({
                                   title: ' ',
                                   content: '' +
                                   '<form action="" class="formName">' +
                                   '<div class="form-group">' +
                                   '<label>Date</label>' +
                                   '<input type="date" class="form-control submission_date " />' +
                                   '</div>' +
                                   '<div class="form-group">' +
                                   '<label>Dr Account</label>' +
                                   data +
                                   '</div>' +
                                   '<div class="form-group">' +
                                   '<label>Rarks</label>' +
                                   '<textarea class="coments form-control" />' +
                                   '</div>' +
                                   '</form>',
                                   buttons: {
                                       formSubmit: {
                                           text: 'Submit',
                                           btnClass: 'btn-blue',
                                           action: function () {
                                               var submission_date = this.$content.find('.submission_date').val();
                                               var dr_account = this.$content.find('select[name="dr_account"]').val();
                                               var coments = this.$content.find('.coments').val();
                                               if(submission_date != '' && dr_account != ''){
                                                   start_spinner();
                                                   $.post(
                                                       base_url + "/human_resource/human_resources/payroll_company_loan_repay/",
                                                       {
                                                           payroll_id:payroll_id,
                                                           received_date: submission_date,
                                                           dr_account: dr_account,
                                                           coments: coments
                                                       },function () {
                                                           stop_spinner();
                                                           $('#receive_company_loan').attr('style', 'display: none').trigger('change');
                                                           $('#preview_company').removeAttr('style').trigger('change');
                                                           toast('success', 'RECEIVED');
                                                       }
                                                   )

                                               }else{
                                                   toast('error', 'Date cannot be empty');
                                                   return false;
                                               }


                                           }
                                       },
                                       cancel: function () {
                                           //close
                                       },
                                   },
                                   onContentReady: function () {
                                       // bind to events
                                       var jc = this;
                                       this.$content.find('form').on('submit', function (e) {
                                           // if the user submits the form by pressing enter in the field.
                                           e.preventDefault();
                                           jc.$$formSubmit.trigger('click'); // reference the button and click it
                                       });
                                   }
                               });

                           }
                       )


                   });
                   company_button.attr('initialized','true');
               }

           })
        }
    });
     }
    )
}

$('#view_payroll_loan_repay').each(function(){
    var button = $(this);
    var payroll = button.closest('form').find('select[name="payroll_id"]');
    payroll.each(function () {
        if(payroll.attr('initialized') != 'true'){
            payroll.change(function () {
                var payroll_id = payroll.val();
                if(payroll_id != ''){
                    $('.employee_loan_repay_table').DataTable().destroy();
                    draw_payroll_loan_repay_table(payroll_id);
                }
            });
            payroll.attr('initialized','true');
        }
    });
});

function draw_payroll_deductions_table(payroll_id){
    start_spinner();
    $.post(
        base_url + "/human_resource/human_resources/payroll_deductions_list",
        {
            payroll_id:payroll_id
        },function(data){
            stop_spinner();
            $('#payroll_deductions_div').removeAttr('style').trigger('change');
            $('#payroll_deductions_div').html(data).trigger('change');

            $('.deduction_payments').each(function(){
                var button = $(this);
                var deduction_name = button.attr('id');

                if(button.attr('initialized') != 'true'){

                    button.click(function () {
                        $.post(
                            base_url + "/human_resource/human_resources/load_accounts/",
                            {
                                data_to_sent:deduction_name
                            },function (data) {

                                $.post(
                                    base_url + "/human_resource/human_resources/load_accounts/",
                                    {
                                        data_to_sent:'cash-bank'
                                    },function (data2) {

                                        $.confirm({
                                            title: 'Pay '+deduction_name,
                                            content: '' +
                                            '<form action="" class="formName">' +
                                            '<div class="form-group">' +
                                            '<label>Date</label>' +
                                            '<input type="date" class="form-control submission_date " />' +
                                            '</div>' +
                                            '<div class="form-group">' +
                                            '<label>Cr Account</label>' +
                                            data2 +
                                            '</div>' +
                                            '</div>' +
                                            '<div class="form-group">' +
                                            '<label>Dr Account</label>' +
                                            data +
                                            '</div>' +
                                            '<div class="form-group">' +
                                            '<label>Remarks</label>' +
                                            '<textarea class="coments form-control" />' +
                                            '</div>' +
                                            '</form>',
                                            buttons: {
                                                formSubmit: {
                                                    text: 'Submit',
                                                    btnClass: 'btn-blue',
                                                    action: function () {
                                                        var submission_date = this.$content.find('.submission_date').val();
                                                        var dr_account = this.$content.find('select[name="dr_account"]').val();
                                                        var cr_account = this.$content.find('select[name="cr_account"]').val();
                                                        var coments = this.$content.find('.coments').val();
                                                        if(submission_date != '' && dr_account != ''){
                                                            start_spinner();
                                                            $.post(
                                                                base_url + "/human_resource/human_resources/payroll_deduction_payments/",
                                                                {
                                                                    payroll_id:payroll_id,
                                                                    paid_date: submission_date,
                                                                    dr_account: dr_account,
                                                                    cr_account: cr_account,
                                                                    coments: coments,
                                                                    deduction_name:deduction_name
                                                                },function () {
                                                                    stop_spinner();
                                                                    button.attr('style', 'display: none').trigger('change');
                                                                    var preview_button = $('#preview'+deduction_name);
                                                                    preview_button.removeAttr('style').trigger('change');
                                                                    preview_button.attr('style', 'color: #0c0c0c; text-align: right').trigger('change');
                                                                    toast('success', deduction_name+'PAID');
                                                                    
                                                                    if(preview_button.attr('initialized') != 'true'){
                                                                        
                                                                        $.post(
                                                                            base_url + "/human_resource/human_resources/payroll_deduction_preview/",
                                                                            {
                                                                                payroll_id:payroll_id,
                                                                                dr_account: dr_account,
                                                                                cr_account: cr_account,
                                                                                deduction_name:deduction_name 
                                                                            },function () {
                                                                                
                                                                            }
                                                                        )
                                                                        
                                                                        preview_button.attr('initialized', 'true');
                                                                    }
                                                                }
                                                            )

                                                        }else{
                                                            toast('error', 'Date cannot be empty');
                                                            return false;
                                                        }


                                                    }
                                                },
                                                cancel: function () {
                                                    //close
                                                },
                                            },
                                            onContentReady: function () {
                                                // bind to events
                                                var jc = this;
                                                this.$content.find('form').on('submit', function (e) {
                                                    // if the user submits the form by pressing enter in the field.
                                                    e.preventDefault();
                                                    jc.$$formSubmit.trigger('click'); // reference the button and click it
                                                });
                                            }
                                        });

                                    }
                                    )

                            }
                        )


                    });
                    button.attr('initialized','true');
                }

            });
        }
    )
}

$('#view_payroll_deductions').each( function(){
    var button = $(this);
    var payroll_selector = button.closest('form').find('select[name="payroll_id"]');
        payroll_selector.change(function () {
            var payroll_id = button.closest('form').find('select[name="payroll_id"]').val();
            if(payroll_id != ''){
                draw_payroll_deductions_table(payroll_id);
            }
        });

});

function draw_payroll_net_payable_table(payroll_id){
    start_spinner();
    $.post(
        base_url + "/human_resource/human_resources/payroll_netpayable_list",
        {
            payroll_id:payroll_id
        },function(data){
            stop_spinner();
            $('#payroll_netpay_div').removeAttr('style').trigger('change');
            $('#payroll_netpay_div').html(data).trigger('change');

            $('.net_payable_payments').each(function(){
                var button = $(this);
                var payment_name = button.attr('id');

                if(button.attr('initialized') != 'true'){

                    button.click(function () {
                                $.post(
                                    base_url + "/human_resource/human_resources/load_accounts/",
                                    {
                                        data_to_sent:'cash-bank'
                                    },function (data) {

                                        $.confirm({
                                            title: 'Pay Net Payable',
                                            content: '' +
                                            '<form action="" class="formName">' +
                                            '<div class="form-group">' +
                                            '<label>Date</label>' +
                                            '<input type="date" class="form-control submission_date " />' +
                                            '</div>' +
                                            '<div class="form-group">' +
                                            '<label>Cr Account</label>' +
                                            data +
                                            '</div>' +
                                            '<div class="form-group">' +
                                            '<label>Remarks</label>' +
                                            '<textarea class="coments form-control" />' +
                                            '</div>' +
                                            '</form>',
                                            buttons: {
                                                formSubmit: {
                                                    text: 'Submit',
                                                    btnClass: 'btn-blue',
                                                    action: function () {
                                                        var submission_date = this.$content.find('.submission_date').val();
                                                        var cr_account = this.$content.find('select[name="cr_account"]').val();
                                                        var coments = this.$content.find('.coments').val();
                                                        if(submission_date != '' && cr_account != ''){
                                                            start_spinner();
                                                            $.post(
                                                                base_url + "/human_resource/human_resources/payroll_netpayable_payments/",
                                                                {
                                                                    payroll_id:payroll_id,
                                                                    paid_date: submission_date,
                                                                    cr_account: cr_account,
                                                                    coments: coments,
                                                                    payment_name:payment_name
                                                                },function () {
                                                                    stop_spinner();
                                                                    button.attr('style', 'display: none').trigger('change');
                                                                    var preview_button = $('#preview'+payment_name);
                                                                    preview_button.removeAttr('style').trigger('change');
                                                                    preview_button.attr('style', 'color: #0c0c0c; text-align: right').trigger('change');
                                                                    toast('success', 'PAID');

                                                                }
                                                            )

                                                        }else{
                                                            toast('error', 'Fill all required regions');
                                                            return false;
                                                        }


                                                    }
                                                },
                                                cancel: function () {
                                                    //close
                                                },
                                            },
                                            onContentReady: function () {
                                                // bind to events
                                                var jc = this;
                                                this.$content.find('form').on('submit', function (e) {
                                                    // if the user submits the form by pressing enter in the field.
                                                    e.preventDefault();
                                                    jc.$$formSubmit.trigger('click'); // reference the button and click it
                                                });
                                            }
                                        });

                                    }
                                )
                    });
                    button.attr('initialized','true');
                }

            });
        }
    )
}

$('#view_payroll_net_pay').each( function(){
    var button = $(this);
    var payroll_selector = button.closest('form').find('select[name="payroll_id"]');
    payroll_selector.change(function () {
        var payroll_id = button.closest('form').find('select[name="payroll_id"]').val();
        if(payroll_id != ''){
            draw_payroll_net_payable_table(payroll_id);
        }
    });

});

function draw_payroll_employee_salary_slip_table(payroll_id){
    start_spinner();
    $.post(
        base_url + "/human_resource/human_resources/payroll_salary_slip_table",
        {
            payroll_id:payroll_id
        },function(data){
            stop_spinner();
            $('#payroll_salary_slip_div_div').removeAttr('style').trigger('change');
            $('#payroll_salary_slip_div_div').html(data).trigger('change');

                $("#all_employee_checkbox").change(function () {

                    if($("#all_employee_checkbox").is(':checked')){
                        check_all();
                    }
                    if(!$("#all_employee_checkbox").is(':checked')) {
                        uncheck_all();
                    }

                });

        }
    )
}


function check_all() {
    var table = $('.employee_salary_slip_table');
    var tbody = table.find('tbody');

    tbody.find('input[name="employee_checkbox[]"]').each(function(){
        $(this).attr("checked", "true").trigger('change');
    });
}

function uncheck_all() {
    var table = $('.employee_salary_slip_table');
    var tbody = table.find('tbody');

    tbody.find('input[name="employee_checkbox[]"]').each(function(){
        $(this).removeAttr("checked").trigger('change');
    });
}

$('#wiew_payroll_salary_slip').each( function(){
    var button = $(this);
    var payroll_selector = button.closest('form').find('select[name="payroll_id"]');
    payroll_selector.change(function () {
        var payroll_id = button.closest('form').find('select[name="payroll_id"]').val();
        if(payroll_id != ''){
            draw_payroll_employee_salary_slip_table(payroll_id);
        }
    });

});

