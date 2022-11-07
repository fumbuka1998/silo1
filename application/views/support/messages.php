<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 01/11/2018
 * Time: 09:10
 */
?>

    <div class="modal-dialog">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?= strtoupper($conversation->subject) ?></h4>
                </div>
                <div class="modal-body">

                    <div class="container-fluid">


                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="<?= '#conv_tab'.$conversation->conversation_id ?>">Message</a></li>
                                <li><a data-toggle="tab" href="<?= '#attach_tab'.$conversation->conversation_id ?>">Attachments</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="<?= 'conv_tab'.$conversation->conversation_id ?>" class="tab-pane fade in active">

                                    <div class='row message_form_conversation'>
                                        <div class="form-group col-md-12 col-sm-12 col-xm-12">
                                            <h5 class="col-md-12 col-sm-12 col-xm-12" for="subject">Subject: <b><?= strtoupper($conversation->subject) ?></b></h5>
                                        </div>
                                        <div class="form-group col-md-12 col-sm-12 col-xm-12">
                                            <h5 class="col-md-12 col-sm-12 col-xm-12" for="message"></span>Message:</h5>
                                            <textarea name="message_replied" class="col-md-12 col-sm-12 col-xm-12" rows="4"></textarea>
                                        </div>
                                        <div class="form-group pull-right margin-r-5 margin">
                                            <button ticket_id="<?= $ticket_id ?>"
                                                    subject="<?= $conversation->subject ?>"
                                                    full_name="<?= $this->session->userdata('employee_name') ?>"
                                                    phone="<?= $this->session->userdata('employee_phone') ?>"
                                                    email="<?= $this->session->userdata('employee_email') ?>"
                                                    initialized="false" type="button"  data-dismiss="modal"
                                                    class="btn btn-success btn-xs save_conversation margin-r-5 "><i class="fa fa-paper-plane"></i> Send
                                            </button>
                                        </div>
                                    </div>

                                </div>
                                <div id="<?= 'attach_tab'.$conversation->conversation_id ?>" class="tab-pane fade">

                                    <div class='row upload_file_form_conversation'>
                                        <form>
                                            <div class="form-group col-md-12 col-sm-12 col-xm-12 margin-bottom">
                                                <h5 class="col-md-12 col-sm-12 col-xm-12" for="file">File:</h5>
                                                <input id="<?= 'file'.$conversation->conversation_id ?>" type="file" multiple class="col-md-12 col-sm-12 col-xm-12" name="file"/>
                                            </div>

                                            <div class="form-group col-md-12 col-sm-12 col-xm-12 ">
                                                <h5 class="col-md-12 col-sm-12 col-xm-12" for="caption"></span>Caption:</h5>
                                                <textarea name="caption" class="col-md-12 col-sm-12 col-xm-12" rows="3"></textarea>
                                            </div>
                                            <div class="form-group pull-right margin-r-5">
                                                <button ticket_id="<?= $ticket_id ?>"
                                                        conversation_id="<?= $conversation->conversation_id ?>"
                                                        subject="<?= $conversation->subject ?>"
                                                        full_name="<?= $this->session->userdata('employee_name') ?>"
                                                        phone="<?= $this->session->userdata('employee_phone') ?>"
                                                        email="<?= $this->session->userdata('employee_email') ?>"
                                                        initialized="false" type="button"  data-dismiss="modal"
                                                        class="btn btn-primary btn-xs upload_conversation_attachment margin-r-5 margin "><i class="fa fa-upload"></i> Upload
                                                </button>
                                            </div>
                                        </form>
                                    </div>


                                </div>
                            </div>


                    </div>


                </div>
            </form>
        </div>
    </div>


