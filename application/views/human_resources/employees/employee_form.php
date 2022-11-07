<?php
    $action = "human_resources/save_employee/";
    if(isset($employee->{$employee::DB_TABLE_PK})){
        $action .= $employee->{$employee::DB_TABLE_PK};
        $modal_title = "Edit Employee: ".$employee->full_name();
    } else {
        $modal_title = "Add Employee";
    }
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <?php echo form_open_multipart($action); ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo $modal_title; ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group  col-md-4">
                        <?php
                        echo form_label('First name','first_name');

                        $data = array(
                            'name' => 'first_name',
                            'id' => 'first_name',
                            'class' => 'form-control ',
                            'placeholder' => 'First Name',
                            'required' => 'required',
                            'value' => $employee->first_name
                        );

                        echo form_input($data);
                        ?>

                    </div>

                    <div class="form-group  col-md-4">
                        <?php
                        echo form_label('Middle name','middle_name');

                        $data = array(
                            'name' => 'middle_name',
                            'id' => 'middle_name',
                            'class' => 'form-control ',
                            'placeholder' => 'Middle Name',
                            'value' => $employee->middle_name
                        );

                        echo form_input($data);
                        ?>

                    </div>

                    <div class="form-group  col-md-4">
                        <?php
                        echo form_label('Last name','last_name');

                        $data = array(
                            'name' => 'last_name',
                            'id' => 'last_name',
                            'class' => 'form-control ',
                            'placeholder' => 'Last Name',
                            'required' => 'required',
                            'value' => $employee->last_name
                        );

                        echo form_input($data);
                        ?>

                    </div>

                    <div class="form-group  col-md-4">
                        <?php
                        echo form_label('Gender','gender');
                        $gender_options = [
                            '' => '',
                            'MALE' => 'MALE',
                            'FEMALE' => 'FEMALE'
                        ];
                        echo form_dropdown('gender', $gender_options, $employee->gender, " class = 'form-control ' required ");

                        ?>

                    </div>

                    <div class="form-group  col-md-4">

                        <?php
                        echo form_label('Birth Date','date_of_birth');

                        $data = array(
                            'name' => 'dob',
                            'id' => 'dob',
                            'class' => ' form-control datepicker ',
                            'value' => $employee->date_of_birth

                        );

                        echo form_input($data);
                        ?>

                    </div>

                    <div class="form-group  col-md-4">
                    <?php
                        echo form_label('Department','department_id');
                        echo form_dropdown('department_id', $department_options, $employee->department_id, " class = ' searchable form-control' required ");
                    ?>
                    </div>

                    <div class="form-group  col-md-4">
                    <?php
                        echo form_label('Position','position_id');
                        echo form_dropdown('position_id', $job_position_options, $employee->position_id, " class = ' searchable form-control' required ");
                    ?>
                    </div>

                    <div class="form-group  col-md-4">
                        <?php
                        echo form_label('Phone','phone');

                        $data = array(
                            'name' => 'phone',
                            'id' => 'phone',
                            'class' => 'form-control ',
                            'placeholder' => 'Mobile Number',
                            'value' => $employee->phone
                        );

                        echo form_input($data);
                        ?>

                    </div>


                    <div class="form-group  col-md-4">
                        <?php
                        echo form_label('Alternative Phone','alternative_phone');

                        $data = array(
                            'name' => 'alternative_phone',
                            'id' => 'alternative_phone',
                            'class' => 'form-control ',
                            'placeholder' => 'Alternative Number',
                            'value' => $employee->alternative_phone
                        );

                        echo form_input($data);
                        ?>

                    </div>


                    <div class="form-group  col-md-4">
                        <?php
                        echo form_label('Address','address');

                        $data = array(
                            'name' => 'address',
                            'id' => 'address',
                            'rows' => '2',
                            'class' => 'form-control ',
                            'placeholder' => 'Address',
                            'value' => $employee->address
                        );

                        echo form_textarea($data);
                        ?>

                    </div>

                    <div class="form-group  col-md-4">
                        <?php
                        echo form_label('Email','email');

                        $data = array(
                            'name' => 'email',
                            'id' => 'email',
                            'class' => 'form-control ',
                            'placeholder' => 'Email Address',
                            'type' => 'email',
                            'value' => $employee->email
                        );

                        echo form_input($data);
                        ?>

                    </div>

                    <div class="form-group  col-md-4">
                        <?php
                            echo form_label('Photo','avatar');

                            $data = array(
                                'name' => 'avatar',
                                'id' => 'avatar',
                            );

                            echo form_upload($data);
                        ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <?php
                 echo form_submit('submit', "Save", " title='Save' class ='btn btn-default'")."&nbsp;";
            ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>