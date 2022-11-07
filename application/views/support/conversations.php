<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 02/11/2018
 * Time: 11:32
 */
$this->load->view('includes/header');

?>

    <section class="content-header">
        <h1>
            Tickets
            <small>Conversations</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?= base_url('support/tickets') ?>">Tickets</a></li>
            <li class="active">Conversations</li>
        </ol>
    </section>

    <div class="col-md-12 margin conversation_texts" status="<?= $status ?>">

        <div>
            <h3 class="timeline-header"><b><?= strtoupper($subject) ?></b></h3>
        </div>

        <div class="container-fluid">

            <div class="nav-tabs-custom pre-scrollable scroll_to_bottom" id="content_div">

                <div class="tab-content" >

                    <?php
                    foreach ($conversations as $conversation){
                        $date_set = set_date(explode(" ",$conversation->date_sent)[0]);
                        $time_set = set_duration($conversation->date_sent);
                        $position = '';
                        $sender = '';
                        $background_color = '';
                        $sent_time = explode(" ",$conversation->date_sent)[1];

                        if($conversation->sender != 'epm'){
                            $position = "pull-left";
                            $sender = "Sent from:";
                            $background_color = 'defff6';
                        }else{
                            $position = "pull-right";
                            $sender = "Sent By:";
                            $background_color = 'ececec';
                        }
                        ?>

                        <!-- /.tab-pane -->
                        <div class="tab-pane active <?= $position ?> col-md-7 col-sm-7 col-xs-12" id="timeline">

                            <script>
                                function <?= 'time_set'.$conversation->conversation_id ?>() {
                                    $.alert({
                                        title: '',
                                        content: "<?= "<b>Date: </b>".$date_set ?> <br/> <?= "<b>Time: </b>".$sent_time ?> ",
                                    });
                                }

                            </script>

                            <ul class="timeline timeline-inverse">
                                <li class="time-label">
                        <span class="bg-light-blue">
                          <?= $date_set ?>
                        </span>
                                </li>

                                <li>

                                    <div class="timeline-item" style="background: <?= '#'.$background_color ?>">
                                        <a href="#" onclick="<?= 'time_set'.$conversation->conversation_id ?>()" class="time"><i class="fa fa-clock-o"></i><?= ' '.$time_set ?></a>

                                        <div class="timeline-body">
                                            <?php
                                            if($conversation->file_path != NULL){

                                                if($conversation->message != ''){
                                                    echo $conversation->message;
                                                    $attachments = $conversation->file_path;
                                                    $count = 0;
                                                    foreach ($attachments as $attachment){
                                                        $file_array = explode('/', $conversation->file_path[$count]);
                                                        $size_array = sizeof($file_array);

                                                        echo '<p></p>';
                                                        echo anchor($this->config->item('crm_url').'support_tickets/download_files/?file_path='.$conversation->file_path[$count], '<i class="fa fa-file"></i> '.explode('/', $conversation->file_path[$count])[$size_array-1]);
                                                        $count++;
                                                    }

                                                }else{
                                                    $count = 0;
                                                    foreach ($attachments as $attachment){
                                                        $file_array = explode('/', $conversation->file_path[$count]);
                                                        $size_array = sizeof($file_array);
                                                        echo '<p></p>';
                                                        echo anchor($this->config->item('crm_url').'support_tickets/download_files/?file_path='.$conversation->file_path[$count], '<i class="fa fa-file"></i> '.explode('/', $conversation->file_path[$count])[$size_array-1]);
                                                        $count++;
                                                    }
                                                }

                                            }else{
                                                echo $conversation->message;
                                            }
                                            ?>
                                        </div>
                                        <div id="contacts_details" class="timeline-footer">
                                            <script>
                                                function <?= 'popup'.$conversation->conversation_id ?>() {
                                                    $.alert({
                                                        title: '<i  class=" fa fa-address-book"></i> Sender Details'+'<br/> ------------------------------',
                                                        content: '<?= '<b>Name: </b>'.$conversation->full_name ?> <br/> <?= '<b>Phone: </b>'.$conversation->phone ?> <br/> <?= '<b>E-mail: </b>'.$conversation->email ?> ',
                                                    });
                                                }
                                            </script>

                                            <span id="<?= 'contacts'.$conversation->conversation_id ?>" onclick="<?= 'popup'.$conversation->conversation_id ?>()" ><b><?= $sender ?> </b><a href="#"><?= ' '.$conversation->full_name ?></a></span>
                                        </div>
                                    </div>
                                </li>


                            </ul>
                        </div>
                        <!-- /.tab-pane -->

                        <?php

                    }
                    ?>


                </div>

            </div>
        </div>


        <div class="container-fluid conversations_replay " >
            <div class="nav-tabs-custom fa-border ">
                <div class="tab-content" style="background: #d4e5eb">

                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="<?= '#conv_tab'.$conversation->conversation_id ?>">Message</a></li>
                        <li><a data-toggle="tab" href="<?= '#attach_tab'.$conversation->conversation_id ?>">Attachments</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="<?= 'conv_tab'.$conversation->conversation_id ?>" class="tab-pane fade in active">

                            <div class='row message_form_conversation'>
                                <div class="form-group col-md-12 col-sm-12 col-xm-12">
                                    <h5 hidden class="col-md-12 col-sm-12 col-xm-12" for="subject">Subject: <b><?= strtoupper($conversation->subject) ?></b></h5>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xm-12">
                                    <textarea id="message_replied" name="message_replied" class="col-md-12 col-sm-12 col-xm-12" rows="2" placeholder="Message"></textarea>
                                </div>
                                <div class="form-group margin">

                                </div>
                            </div>

                        </div>
                        <div id="<?= 'attach_tab'.$conversation->conversation_id ?>" class="tab-pane fade">

                            <div class='row upload_file_form_conversation'>
                                <form>
                                    <div class="form-group col-md-12 col-sm-12 col-xm-12 ">
                                        <input id="files" type="file" multiple class="col-md-4 col-sm-6 col-xm-12 margin" name="file"/>
                                        <textarea id="caption" name="caption" class="col-md-7 col-sm-4 col-xm-12 margin" rows="2" placeholder="Caption"></textarea>
                                    </div>

                                </form>
                            </div>

                        </div>
                    </div>


                </div>
                <button ticket_id="<?= $ticket_id ?>"
                        subject="<?= $conversation->subject ?>"
                        full_name="<?= $this->session->userdata('employee_name') ?>"
                        phone="<?= $this->session->userdata('employee_phone') ?>"
                        email="<?= $this->session->userdata('employee_email') ?>"
                        initialized="false" type="button"  data-dismiss="modal"
                        style="width: 100%"
                        class="btn btn-success btn-xs save_conversation"><i class="fa fa-paper-plane"></i> Send
                </button>
            </div>

        </div>

    </div>
<?php
$this->load->view('includes/footer');
