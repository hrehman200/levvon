<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends MY_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer || $this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->load->library('form_validation');
        $this->load->admin_model('companies_model');
        $this->load->model('mahana_model');
    }

    function index($action = NULL) {
        $this->sma->checkPermissions('customers');

        $this->data['error']  = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc                   = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Messages'));
        $meta                 = array('page_title' => 'Messages', 'bc' => $bc);
        $this->page_construct('customers/messages/index', $meta, $this->data);
    }

    public function add($note_id) {
        $this->sma->checkPermissions(false, true);

        $this->form_validation->set_rules('to[]', 'To', 'trim|required');
        $this->form_validation->set_rules('subject', 'Subject', 'trim|required');
        $this->form_validation->set_rules('cc', 'CC', 'trim|valid_emails');
        $this->form_validation->set_rules('bcc', 'BCC', 'trim|valid_emails');
        $this->form_validation->set_rules('note', 'Message', 'trim');

        $company_note = $this->companies_model->getNoteById($note_id);

        if ($this->form_validation->run() == true) {

            $to      = $this->input->post('to[]');
            $subject = $this->input->post('subject');
            if ($this->input->post('cc')) {
                $cc = $this->input->post('cc');
            } else {
                $cc = null;
            }
            if ($this->input->post('bcc')) {
                $bcc = $this->input->post('bcc');
            } else {
                $bcc = null;
            }
            $attachment = null;

            $this->load->library('parser');

            $users = $this->site->getUsersFromIds($to);
            $user_names = array_map(function($v) {return $v['first_name']." ".$v['last_name'];}, $users);
            $emails = array_map(function($v) {return $v['email'];}, $users);

            $parse_data = array(
                'contact_person' => $company_note['name'],
                'company'        => $company_note['company'],
                'username'       => implode(', ', $user_names),
                'msg_date'       => $company_note['created'],
                'site_link'      => base_url(),
                'site_name'      => $this->Settings->site_name,
                'logo'           => '<img src="' . base_url() . 'assets/uploads/logos/LOGOSTOCK.png" />',
            );
            $msg        = $this->input->post('note');
            $message    = $this->parser->parse_string($msg, $parse_data);

            try {
                if ($this->sma->send_email($emails, $subject, $message, null, null, $attachment, $cc, $bcc)) {

                    $sender_id  = $this->session->userdata('user_id');
                    $recipients = $to;
                    $this->mahana_model->send_new_message($company_note['id'], $sender_id, $recipients, $subject, $message, PRIORITY_URGENT);

                    $this->session->set_flashdata('message', sprintf(lang("email_sent"), $this->Settings->protocol));
                    admin_redirect("messages");
                }
            } catch (Exception $e) {
                $this->session->set_flashdata('error', $e->getMessage());
                redirect($_SERVER["HTTP_REFERER"]);
            }

        } elseif ($this->input->post('send_email')) {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->session->set_flashdata('error', $this->data['error']);
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $user_msg_template = file_get_contents('./themes/default/admin/views/email_templates/user_msg.html');

            $this->data['subject'] = array('name'  => 'subject',
                                           'id'    => 'subject',
                                           'type'  => 'text',
                                           'value' => $this->form_validation->set_value('subject', sprintf('About %s', $company_note['company'])),
            );
            $this->data['note']    = array('name'  => 'note',
                                           'id'    => 'note',
                                           'type'  => 'text',
                                           'value' => $this->form_validation->set_value('note', $user_msg_template),
            );

            $this->data['users']        = $this->site->getUsers();
            $this->data['note_id']      = $note_id;
            $this->data['company_note'] = $company_note;
            $this->data['modal_js']     = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/messages/edit', $this->data);
        }
    }

    function getThreads() {
        //$this->sma->checkPermissions('customers');

        $user_id  = $this->session->userdata('user_id');
        $response = $this->mahana_model->get_all_threads($user_id, false, 'DESC');

        foreach ($response as &$r) {

            $msg = $this->mahana_model->get_message($r['id'], $user_id);
            $r['status'] = $msg[0]['status'];

            $r['actions'] = sprintf('<div align="center">
                <a href="%s"><i class="fa fa-eye"></i></a>
            </div>', admin_url('messages/view/' . $r['thread_id']));
        }

        echo json_encode([
            'aaData' => $response
        ]);
    }

    function view($thread_id) {
        $this->sma->checkPermissions('customers');

        $user_id  = $this->session->userdata('user_id');
        $response = $this->mahana_model->get_full_thread($thread_id, $user_id, true);
        $subject  = $response[0]['subject'];

        $this->data['error']     = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['thread_id'] = $thread_id;
        $this->data['subject']   = $subject;
        $bc                      = array(
            array('link' => base_url(), 'page' => lang('home')),
            array('link' => admin_url('messages'), 'page' => 'Messages'),
            array('link' => '#', 'page' => $subject)
        );
        $meta                    = array('page_title' => $subject, 'bc' => $bc);
        $this->page_construct('customers/messages/view', $meta, $this->data);
    }

    function getThread($thread_id) {
        $user_id  = $this->session->userdata('user_id');
        $response = $this->mahana_model->get_full_thread($thread_id, $user_id, true);

        $msg_ids = array_map(function ($v) {
            return $v['id'];
        }, $response);

        $this->mahana_model->update_message_status($msg_ids, $user_id, MSG_STATUS_READ);

        echo json_encode([
            'aaData' => $response
        ]);
    }

    public function reply($thread_id) {
        $this->sma->checkPermissions(false, true);

        $this->form_validation->set_rules('to[]', 'To', 'trim|required');
        $this->form_validation->set_rules('subject', 'Subject', 'trim|required');
        $this->form_validation->set_rules('cc', 'CC', 'trim|valid_emails');
        $this->form_validation->set_rules('bcc', 'BCC', 'trim|valid_emails');
        $this->form_validation->set_rules('note', 'Message', 'trim');

        $user_id = $this->session->userdata('user_id');
        $thread  = $this->mahana_model->get_full_thread($thread_id, $user_id, true);

        if ($this->form_validation->run() == true) {

            $to      = $this->input->post('to[]');
            $subject = $this->input->post('subject');
            if ($this->input->post('cc')) {
                $cc = $this->input->post('cc');
            } else {
                $cc = null;
            }
            if ($this->input->post('bcc')) {
                $bcc = $this->input->post('bcc');
            } else {
                $bcc = null;
            }
            $attachment = null;

            $msg = $this->input->post('note');

            $thread_msgs = $this->mahana_model->getThreadMessages($thread_id);
            $prev_msgs   = '';
            foreach ($thread_msgs as $thread_msg) {
                $prev_msgs .= sprintf('%s<br/>
                    %s<br/>
                    %s<br/><hr>', $thread_msg['user_name'], $thread_msg['cdate'], $thread_msg['body']);
            }

            $message = $msg . '<br/><br/>' . $prev_msgs;

            try {
                $users = $this->site->getUsersFromIds($to);
                $emails = array_map(function($v) {return $v['email'];}, $users);
                if ($this->sma->send_email($emails, $subject, $message, null, null, $attachment, $cc, $bcc)) {

                    $sender_id  = $this->session->userdata('user_id');
                    $recipients = $to;
                    $msg_id     = $this->mahana_model->reply_to_thread($thread_id, $sender_id, $recipients, $msg);
                    if ($msg_id == false) {
                        throw new Exception('Failed in replying to message');
                    }

                    $this->session->set_flashdata('message', sprintf(lang("email_sent"), $this->Settings->protocol));
                    admin_redirect("messages/view/" . $thread_id);
                }
            } catch (Exception $e) {
                $this->session->set_flashdata('error', $e->getMessage());
                redirect($_SERVER["HTTP_REFERER"]);
            }

        } elseif ($this->input->post('send_email')) {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->session->set_flashdata('error', $this->data['error']);
            redirect($_SERVER["HTTP_REFERER"]);

        } else {

            $subject = strpos($thread[0]['subject'], 'Re: ') !== false ? sprintf('Re: %s', $thread[0]['subject']) : $thread[0]['subject'];

            $this->data['subject'] = array('name'  => 'subject',
                                           'id'    => 'subject',
                                           'type'  => 'text',
                                           'value' => $this->form_validation->set_value('subject', $subject));

            $this->data['note'] = array('name'  => 'note',
                                        'id'    => 'note',
                                        'type'  => 'text',
                                        'value' => $this->form_validation->set_value('note', ''));

            $this->data['users']     = $this->site->getUsers();
            $this->data['thread_id'] = $thread_id;
            $this->data['modal_js']  = $this->site->modal_js();
            $this->load->view($this->theme . 'customers/messages/edit', $this->data);
        }
    }
}
