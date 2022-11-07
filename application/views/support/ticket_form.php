<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">New Support Ticket</h4>
            </div>
            <div class="modal-body">


                <ul class="nav nav-tabs ">
                    <li class="active"><a data-toggle="tab" href="#conv">Conversations</a></li>
                    <li><a data-toggle="tab" href="#attach">Attachments</a></li>
                </ul>
                <div class="tab-content mt-3">
                    <div id="conv" class="tab-pane in active ">

                        <div class='row ticket_form'>
                                <div class="form-group col-md-12 col-sm-12 col-xm-12">
                                    <label for="subject">Subject</label>
                                    <input type="text" class="form-control" name="subject"/>
                                </div>

                            <div id="select_email" class="form-group col-md-12 col-sm-12 col-xs-12 ">
                                <label>Cc:</label></br>
                                <?php
                                echo form_dropdown('carbon_copies', $email_options,
                                    '',
                                    'class="form-control searchable " multiple'
                                );
                                ?>
                            </div>

                                <div class="form-group col-md-12 col-sm-12 col-xm-12">
                                    <label for="message"></span>Message</label>
                                    <textarea name="message" class="form-control" rows="4" placeholder="Type ur message here"></textarea>
                                </div>

                        </div>
                    </div>
                    <div id="attach" class="tab-pane fade">

                        <div class='row upload_file_form'>
                            <form>
                            <div class="form-group col-md-12 col-sm-12 col-xm-12">
                                <label for="file">File</label>
                                <input type="file" multiple class="form-control" id="uploaded_file" name="file"/>
                            </div>

                            <div class="form-group col-md-12 col-sm-12 col-xm-12">
                                <label for="caption"></span>Caption</label>
                                <textarea name="caption" class="form-control" rows="4"></textarea>
                            </div>
<!--                            <div class="form-group pull-right margin-r-5">-->
<!--                                <button type="button" initialized="false"-->
<!--                                        class="btn btn-primary btn-xs upload_attachment_in_ticket margin-r-5 "><i class="fa fa-upload"></i> Upload-->
<!--                                </button>-->
<!---->
<!--                            </div>-->
                            </form>
                        </div>

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button full_name="<?= $this->session->userdata('employee_name') ?>"
                        phone="<?= $this->session->userdata('employee_phone') ?>"
                        email="<?= $this->session->userdata('employee_email') ?>"
                        initialized="false" type="button"

                        class="btn btn-success btn-xs save_ticket margin-r-5 "><i class="fa fa-paper-plane"></i> Send
                </button>
            </div>
        </form>
    </div>
</div>