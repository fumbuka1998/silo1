<?php
class Deployment_attachment extends MY_Model {
    const DB_TABLE = 'deployment_attachments';
    const DB_TABLE_PK = 'id';
    public $attachment_id;
    public $deployment_id;

    public function attachment(){
        $this->load->model('attachment');
        $attachment = new Attachment();
        $attachment->load($this->attachment_id);
        return $attachment;
    }

    public function deployment(){
        $this->load->model('deployment');
        $deployment = new Deployment();
        $deployment->load($this->deployment_id);
        return $deployment;
    }
}