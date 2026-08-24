<?php
/**
 * Created by PhpStorm.
 * User: faisal
 * Date: 10/28/17
 * Time: 10:23 AM
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Customnotes_model extends CRM_Model
{
    /**
     * Receipts_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all  items
     * @param  mixed $id invoiceid
     * @return array
     */
    public function get($id = '', $where = array())
    {
        $this->db->select('*');
        $this->db->from('tblcustomnotes');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblcustomnotes.en_userId', 'left');
        $this->db->where($where);
        $this->db->order_by('tblcustomnotes.en_dateadded', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get all  items
     * @param  mixed $id invoiceid
     * @return array
     */
    public function getById($id = '')
    {
        $this->db->select('*');
        $this->db->from('tblcustomnotes');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblcustomnotes.en_userId', 'left');
        $this->db->where(['cn_id' => $id]);
        return $this->db->get()->row();
    }

    /**
     * @param $data
     * @return bool
     */
    public function insert($data)
    {
        $owner = $this->session->userdata['staff_user_id'];

        $table = [
            'cn_title' => $data['noteTitle'],
            'cn_type' => $data['noteType'],
            'cn_notes' => $data['noteDescription'],
            'cn_position' => $data['notePosition'],
            'en_userId' => $this->session->userdata['staff_user_id'],
            'en_dateadded' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('tblcustomnotes', $table);
        $insertId = $this->db->insert_id();

        if (!$insertId) {
            return false;
        }

        return $insertId;
    }

    public function updateNote($data, $id)
    {
        $owner = $this->session->userdata['staff_user_id'];

        $table = [
            'cn_title' => $data['noteTitle'],
            'cn_type' => $data['noteType'],
            'cn_notes' => $data['noteDescription'],
            'cn_position' => $data['notePosition'],
            'en_userId' => $this->session->userdata['staff_user_id'],
            'en_dateadded' => date('Y-m-d H:i:s')
        ];

        $this->db->where('cn_id', $id);
        $insertId = $this->db->update('tblcustomnotes', $table);

        return $insertId;
    }

    public function deleteNote($id)
    {
        $this->db->where('cn_id', $id);
        echo $this->db->delete('tblcustomnotes');
    }

    /**
     * Get all  items
     * @param  mixed $id invoiceid
     * @return array
     */
    public function getByTypePosition($type = "", $position = "")
    {
        $this->db->select('*');
        $this->db->from('tblcustomnotes');

        if($type){
            $this->db->where(['cn_type' => $type]);
        }

        if($position){
            $this->db->where(['cn_position' => $position]);
        }

        return $this->db->get()->result();
    }

    public function getDescriptionById($id)
    {
        $this->db->select('cn_notes');
        $this->db->from('tblcustomnotes');
        $this->db->where(['cn_id' => $id]);
        return $this->db->get()->row();
    }
}