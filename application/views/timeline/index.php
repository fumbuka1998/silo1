<?php
$images_extension = array('gif','jpg','png','jpeg');
$session_id = $this->session->userdata('employee_id');
$this->load->view('includes/header');
?>
<section class="content-header">
    <h1>
        Timeline
        <small>Activities & Events</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li class="active">Timeline</li>
    </ol>
</section>

<section class="content">
    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <!-- The time line -->
            <ul class="timeline">
                <?php
                if(!empty($convo_logs)) {
                    foreach ($convo_logs as $index => $convo_log) {
                        $log_details = unserialize(urldecode($convo_log->log_details));
                        $time_set = set_duration($log_details['topic']->updated_at);
                        $date = explode(' ', $convo_log->datetime_posted)[0];
                        $sender = $log_details['topic_convo']->sender;
                        $recipient = $log_details['topic_convo']->recipient;

                        if (($log_details['topic']->type == "DIRECT" && in_array($session_id,array($sender,$recipient))) || $log_details['topic']->type == "PUBLIC") {
                            $icon_n_hint = $convo_log->timeline_message($session_id, $convo_log->log_details);
                            $icon = explode('_', $icon_n_hint)[0];
                            $hint = explode('_', $icon_n_hint)[1];
                            ?>
                            <!-- timeline time label -->
                            <li class="time-label">
                              <span class="bg-red">
                                <?= set_date($date) ?>
                              </span>
                            </li>

                            <li>
                                <script>
                                    function <?= 'time_sent' . $convo_log->topic_id?>() {
                                        $.alert({
                                            title: '',
                                            content: "<?= "<b>Date: </b>" . explode(' ', $convo_log->datetime_posted)[0] ?> <br/> <?= "<b>Time: </b>" . explode(' ', $convo_log->datetime_posted)[1] ?> ",
                                        });
                                    }
                                </script>
                                <?= $icon ?>


                                <div class="timeline-item">
                                    <a href="#" onclick="<?= 'time_sent' . $convo_log->topic_id ?>()" class="time mr-1"><i
                                                class="fa fa-clock-o"></i>
                                        <small><?= ' ' . $time_set ?></small>
                                    </a>
                                    <h3 class="timeline-header"><?= $hint ?>
                                        &nbsp;<?php if (check_permission('Projects')) { ?><a target="_blank"
                                                                                             href="<?= base_url('projects/profile/' . $log_details['topic']->project_id) ?>"><?= $log_details['topic']->project()->project_name ?></a><?php } else {
                                            echo $log_details['topic']->project()->project_name;
                                        } ?></h3>
                                    <?php if ($log_details['topic_convo']->type == "CAPTION") { ?>
                                        <div class="timeline-body">
                                            <?php
                                            echo wordwrap($log_details['topic_convo']->message, 150, '<br/>');
                                            if ($log_details['topic']->has_thumbnail()) {
                                                $ext = pathinfo($log_details['topic']->attachment_name, PATHINFO_EXTENSION);
                                                if (in_array($ext, $images_extension)) {
                                                    ?>
                                                    <div class="timeline-body">
                                                        <img class="gallery-items"
                                                             src="<?= $log_details['topic']->thumbnail() ?>"
                                                             alt="There is a problem with the attachment" width="100px">
                                                    </div>
                                                <?php }
                                            } ?>
                                        </div>
                                        <div class="timeline-footer">
                                            <!--                                            <a class="btn btn-primary btn-xs">Read more</a>-->
                                            <!--                                            <a class="btn btn-danger btn-xs">Delete</a>-->
                                        </div>
                                    <?php } ?>
                                </div>
                            </li>
                        <?php
                        }
                    }
                } else {
                    ?>
                    <li class="time-label">
                          <span class="bg-red">
                            <?= set_date(date('Y-m-d')) ?>
                          </span>
                    </li>
                    <li>
                        <i class="fa fa-info-circle bg-purple"></i>
                        <div class="timeline-item">
                            <div class="timeline-body">
                                No item shared for now!
                            </div>
                        </div>
                    </li>
                    <?php
                }
                ?>
                <!-- END timeline item -->
                <li>
                    <i class="fa fa-clock-o bg-gray"></i>
                </li>
            </ul>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<?php
$this->load->view('includes/footer');
?>
