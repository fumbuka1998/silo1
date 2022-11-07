<?php

class Requisition_attachment extends MY_Model{
    
    const DB_TABLE = 'requisition_attachments';
    const DB_TABLE_PK = 'id';

    public $requisition_id;
    public $attachment_id;

    public function attachment()
    {
        $this->load->model('attachment');
        $attachment = new Attachment();
        $attachment->load($this->attachment_id);
        return $attachment;
    }

    public function requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        $requisition->load($this->requisition_id);
        return $requisition;
    }

}

