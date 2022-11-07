<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 11/04/2018
 * Time: 08:44
 */
$employee = $project->created_by();
?>
<div class="box" style="background-color: #a6b3cd;" style="overflow-y: scroll !important;">
    <div class="box-body">
        <div class="row tab_container">
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">New Post</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <form class="form-inline">
                                <div class="form-group pull-left">
                                    <div class="col-sm-12">
                                        <div class="col-sm-2">
                                            <label for="subject_id">Subject:  </label>
                                        </div>
                                        <div class="col-sm-10">
                                            <?= form_dropdown('subject_id',$project_objects_dropdown_options,'','class="form-control searchable"') ?>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="col-sm-12">
                                        <div class="col-sm-2">
                                            <label for="topic_message">Caption:  </label>
                                        </div>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" style="width: 100%" name="topic_message" id="" rows="3"></textarea>
                                            <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="col-sm-12">
                                        <div class="col-sm-2">
                                            <label for="topic_attachment">Attach File:  </label>
                                        </div>
                                        <div class="col-sm-10">
                                            <input type="file" style="width: 80%" name="topic_attachment" class="form-control">
                                            <button type="button" class="btn btn-default btn-sm" id="send_public_post"><i class="fa fa-send"></i>Post</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">Posts</h3>
                    </div>
                </div>
                <div id="post_container">
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">Chat Rooms</h3>
                    </div>
                </div>

                <div class="box box-info">
                    <div class="box-header">
                        <button data-toggle="modal" data-target="#start_chat<?= $project->{$project::DB_TABLE_PK} ?>" class="btn btn-flat btn-xs btn-info">
                            <i class="fa fa-plus"></i>Start Chat
                        </button>
                        <div id="start_chat<?= $project->{$project::DB_TABLE_PK} ?>" class="modal fade chat_form" role="dialog">
                            <?php
                            $this->load->view('projects/wallposts/chats/chat_form');
                            ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12 table-responsive">
                                <table class="table table-bordered table-hover" id="project_chat_rooms" project_id="<?= $project->{$project::DB_TABLE_PK} ?>" style="table-layout: fixed">
                                    <thead>
                                    <tr>
                                        <th style="width: 20%">Date</th><th>Started By</th><th style="width: 20%">Status</th><th>Last Opened</th><th style="width: 20%"></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
