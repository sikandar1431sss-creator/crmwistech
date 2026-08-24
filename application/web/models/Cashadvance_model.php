<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cashadvance_model extends CRM_Model
{
    private $shipping_fields = array('shipping_street', 'shipping_city', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country');
    private $statuses = array(1, 2, 3, 4, 5, 6);

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($table)
    {

        $this->db->insert('tblcashadvance', $table);
        $insertId = $this->db->insert_id();

        if (!$insertId) {
            return false;
        }

        return $insertId;
    }

    /**
     * Get all  items
     * @param  mixed $id invoiceid
     * @return array
     */
    public function get_total_advance_amount($client_id)
    {
        $advance = 0;
        $this->db->select('SUM(amount) as credit, SUM(withdraw) as debit');
        $this->db->from('tblcashadvance');
        $this->db->where(['client_id' => $client_id]);
        $data = $this->db->get()->row();

        if ($data <> null) {
            $advance = $data->credit - $data->debit;
        }

        return $advance;
    }

    /**
     * @param $id
     * @param $status
     */
    public function updateStatus($id, $status)
    {

        $data = array();

        $data = ['receipt_status' => $status];
        $date = date('Y-m-d H:i:s');
        $staff = $this->session->userdata['staff_user_id'];

        if ($status == 'handover') {
            $data['receipt_handover_by'] = $staff;
            $data['receipt_handover_on'] = $date;
        }

        if ($status == 'deposited') {
            $data['receipt_deposit_by'] = $staff;
            $data['receipt_deposit_on'] = $date;
        }

        if ($status == 'verified') {
            $data['receipt_verified_by'] = $staff;
            $data['receipt_verified_on'] = $date;
        }


        $this->db->where('receipt_id', $id);
        return $this->db->update('tblreciepts', $data);
    }

    public function staffNameById($id)
    {
        $this->db->select('firstname, lastname');
        $this->db->from('tblstaff');
        $this->db->where(['staffid' => $id]);
        return $query = $this->db->get()->row();
    }

    public function updateData($id, $data)
    {
        $this->db->where(['id' => $id]);
        return $this->db->update('tblcashadvance', $data);
    }
}
