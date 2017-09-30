<?php

class Daily_schedule extends MY_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('sales', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('daily_schedule_model');
        $this->data['logo']        = true;

        $this->load->admin_model('auth_model');
        $this->load->library('ion_auth');
    }

    /**
     * @param $date
     */
    function modal($date) {
        $this->sma->checkPermissions('users', true);

        $user_id = $this->session->userdata('user_id');
        $this->data['user'] = $this->ion_auth->user($user_id)->row();
        $this->data['date'] = $date;
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['modal_js'] = $this->site->modal_js();
        $this->load->view($this->theme . 'daily_schedule/list', $this->data);
    }

    /**
     * @param $date
     */
    function readonlyModal($date) {
        $user_id = $this->session->userdata('user_id');
        $result = $this->daily_schedule_model->get($user_id, 0, $date);

        $user_id = $this->session->userdata('user_id');
        $this->data['user'] = $this->ion_auth->user($user_id)->row();
        $this->data['schedules'] = $result;
        $this->data['date'] = $date;

        $this->daily_schedule_model->markRead($user_id, $date);

        $this->load->view($this->theme . 'daily_schedule/view', $this->data);
    }

    function getList($date) {
        $this->sma->checkPermissions('users', TRUE);
        $this->load->library('datatables');

        $user_id = $this->session->userdata('user_id');
        $response = $this->daily_schedule_model->getSchedules($user_id, $date);

        echo $response;
    }

    function edit($date, $schedule_id = NULL) {
        $this->sma->checkPermissions('users', true);

        if($schedule_id != null) {
            $schedule = $this->daily_schedule_model->getById($schedule_id);
        }
        $user_id = $this->session->userdata('user_id');
        $user = $this->ion_auth->user($user_id)->row();

        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('time', 'Time', 'required');
        $this->form_validation->set_rules('note', 'Note', 'required');

        if ($this->input->post('edit_schedule')) {

            if($this->form_validation->run() == true) {
                $data = array(
                    'user_id' => $user_id,
                    'date' => $this->input->post('date'),
                    'time' => $this->input->post('time'),
                    'note'  => $this->input->post('note')
                );

                if($schedule_id != null) {
                    $data['id'] = $schedule_id;
                }

                $success = $this->daily_schedule_model->save($data);

                if($success) {
                    $this->session->set_flashdata('message', 'Schedule updated');
                } else {
                    $this->session->set_flashdata('error', 'Schedule not updated');
                }
            } else {
                $this->session->set_flashdata('error', validation_errors());
            }

            admin_redirect('reports/daily_schedule/');

        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['user'] = $user;
            $this->data['date'] = $date;
            $this->data['schedule'] = @$schedule;

            $this->load->view($this->theme . 'daily_schedule/edit', $this->data);
        }
    }

    /**
     * @param $id
     */
    function delete($id) {
        $this->sma->checkPermissions('users', true);

        if ($this->daily_schedule_model->delete($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("schedule_deleted")));
        }
    }


}