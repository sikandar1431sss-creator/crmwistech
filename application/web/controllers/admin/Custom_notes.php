<?php ob_start();
defined('BASEPATH') or exit('No direct script access allowed');

class Custom_notes extends Admin_controller
{
    public function __construct()
    {
        ob_start();
        parent::__construct();
        $this->load->model('receipts_model');
        $this->load->model('invoices_model');
        $this->load->model('payments_model');
        $this->load->model('clients_model');
        $this->load->model('staff_model');
        $this->load->model('currencies_model');
        $this->load->model('projects_model');
        $this->load->helper('url');
        $this->load->model('cashadvance_model');
        $this->load->model('leads_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->model('customnotes_model');
    }


    /* Get all invoices in case user go on index page */
    public function index($status = '')
    {
        //if (!has_permission('receipts', '', 'view') && !has_permission('invoices', '', 'view_own')) {
        //  access_denied('receipts');
        //  }
        $where = [];

        if ($this->input->post()) {

            if ($this->input->post('noteType')) {
                $where['cn_type'] = $this->input->post('noteType');
            }

            if ($this->input->post('notePosition')) {
                $where['cn_position'] = $this->input->post('notePosition');
            }
        }

        $data['cus_notes'] = $this->customnotes_model->get('', $where);
        $this->load->view('admin/custom_notes/list_notes', $data);

    }

    /* Get all invoices in case user go on index page */
    public function create()
    {

        // if (!has_permission('receipts', '', 'create')) {
        //    access_denied('receipts');
        // }

        //
        $data['staff'] = $this->staff_model->get('', 1);


        if ($this->input->post()) {

            $this->form_validation->set_rules('noteTitle', 'Title', 'required|trim');
            $this->form_validation->set_rules('noteType', 'Note Type', 'required');
            $this->form_validation->set_rules('notePosition', 'Position', 'required');
            $this->form_validation->set_rules('noteDescription', 'Description', 'required|trim');
            // pre_array($this->input->post());
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('admin/custom_notes/create', $data);
            } else {
                $receipt_id = $this->customnotes_model->insert($this->input->post());

                redirect('admin/custom_notes', 'refresh');
            }
        }

        $this->load->view('admin/custom_notes/create', $data);
    }

    /**
     * @param string $id
     */
    public function update($id = '')
    {
        $data['staff'] = $this->staff_model->get('', 1);

        // if (!has_permission('receipts', '', 'edit')) {
        //    access_denied('receipts');
        // }

        if (!is_admin()) {
            redirect('admin/', 'refresh');
        }

        if ($this->input->post()) {

            $this->customnotes_model->updateNote($this->input->post(), $id);
            redirect('admin/custom_notes/update/' . $id, 'refresh');
        }


        $data['cus_notes'] = $this->customnotes_model->getById($id);
        $data['id'] = $id;
        $this->load->view('admin/custom_notes/update', $data);
    }

    /**
     * @param $id
     */
    public function delete()
    {
        if ($this->input->post()) {
            if ($this->customnotes_model->deleteNote($this->input->post('note_id'))) {
                redirect('admin/custom_notes/', 'refresh');
            }
        }
    }

    public function getDescription()
    {
        if ($this->input->post()) {

            $data = $this->customnotes_model->getDescriptionById($this->input->post('noteId'));

            if ($data <> null) {
                print_r($data->cn_notes);
            }
        }
    }
}