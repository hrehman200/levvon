<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Companies_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllBillerCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'biller'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllCustomerCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'customer'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllSupplierCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'supplier'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllCustomerGroups()
    {
        $q = $this->db->get('customer_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCompanyUsers($company_id)
    {
        $q = $this->db->get_where('users', array('company_id' => $company_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCompanyByID($id)
    {
        $q = $this->db->get_where('companies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCompanyByEmail($email)
    {
        $q = $this->db->get_where('companies', array('email' => $email), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addCompany($data = array())
    {
        if ($this->db->insert('companies', $data)) {
            $cid = $this->db->insert_id();
            return $cid;
        }
        return false;
    }

    public function updateCompany($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('companies', $data)) {
            return true;
        }
        return false;
    }

    public function addCompanies($data = array())
    {
        if ($this->db->insert_batch('companies', $data)) {
            return true;
        }
        return false;
    }

    public function deleteCustomer($id)
    {
        if ($this->getCustomerSales($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'customer')) && $this->db->delete('users', array('company_id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function deleteSupplier($id)
    {
        if ($this->getSupplierPurchases($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'supplier')) && $this->db->delete('users', array('company_id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function deleteBiller($id)
    {
        if ($this->getBillerSales($id)) {
            return false;
        }
        if ($this->db->delete('companies', array('id' => $id, 'group_name' => 'biller'))) {
            return true;
        }
        return FALSE;
    }

    public function getBillerSuggestions($term, $limit = 10)
    {
        $this->db->select("id, company as text");
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', array('group_name' => 'biller'), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCustomerSuggestions($term, $limit = 10)
    {
        $this->db->select("id, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as text", FALSE);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%' OR email LIKE '%" . $term . "%' OR phone LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', array('group_name' => 'customer'), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getSupplierSuggestions($term, $limit = 10)
    {
        $this->db->select("id, (CASE WHEN company = '-' THEN name ELSE CONCAT(company, ' (', name, ')') END) as text", FALSE);
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%' OR email LIKE '%" . $term . "%' OR phone LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', array('group_name' => 'supplier'), $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

    public function getCustomerSales($id)
    {
        $this->db->where('customer_id', $id)->from('sales');
        return $this->db->count_all_results();
    }

    public function getBillerSales($id)
    {
        $this->db->where('biller_id', $id)->from('sales');
        return $this->db->count_all_results();
    }

    public function getSupplierPurchases($id)
    {
        $this->db->where('supplier_id', $id)->from('purchases');
        return $this->db->count_all_results();
    }

    public function addDeposit($data, $cdata)
    {
        if ($this->db->insert('deposits', $data) && 
            $this->db->update('companies', $cdata, array('id' => $data['company_id']))) {
            return true;
        }
        return false;
    }

    public function updateDeposit($id, $data, $cdata)
    {
        if ($this->db->update('deposits', $data, array('id' => $id)) && 
            $this->db->update('companies', $cdata, array('id' => $data['company_id']))) {
            return true;
        }
        return false;
    }

    public function getDepositByID($id)
    {
        $q = $this->db->get_where('deposits', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteDeposit($id)
    {
        $deposit = $this->getDepositByID($id);
        $company = $this->getCompanyByID($deposit->company_id);
        $cdata = array(
                'deposit_amount' => ($company->deposit_amount-$deposit->amount)
            );
        if ($this->db->update('companies', $cdata, array('id' => $deposit->company_id)) &&
            $this->db->delete('deposits', array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function getAllPriceGroups()
    {
        $q = $this->db->get('price_groups');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCompanyAddresses($company_id)
    {
        $q = $this->db->get_where('addresses', array('company_id' => $company_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addAddress($data)
    {
        if ($this->db->insert('addresses', $data)) {
            return true;
        }
        return false;
    }

    public function updateAddress($id, $data)
    {
        if ($this->db->update('addresses', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteAddress($id)
    {
        if ($this->db->delete('addresses', array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function getAddressByID($id)
    {
        $q = $this->db->get_where('addresses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    /**
     * @param $a
     * @param $b
     * @return bool
     */
    private static function sortCustomValues($a, $b) {
        if($_POST['sSortDir_0'] == 'asc') {
            return $a[$_POST['iSortCol_0']]>$b[$_POST['iSortCol_0']];
        } else {
            return $a[$_POST['iSortCol_0']]<$b[$_POST['iSortCol_0']];
        }
    }

    public function getForecast() {
        $this->load->admin_model('reports_model');
        $this->load->library('datatables');
        $this->datatables
            ->select("id, company, name, phone, 0 AS average_product, 0 AS average_buying_days, 0 AS days_inactive")
            ->from("companies")
            ->where('group_name', 'customer')
            ->add_column("Actions", "<div class=\"text-center\">
            <a class=\"tip\" title='" . lang("list_deposits") . "' href='" . admin_url('customers/deposits/$1') . "' data-toggle='modal' data-target='#myModal'>
                <i class=\"fa fa-money\"></i>
            </a>
            <a class=\"tip\" title='" . lang("add_deposit") . "' href='" . admin_url('customers/add_deposit/$1') . "' data-toggle='modal' data-target='#myModal'>
                <i class=\"fa fa-plus\"></i>
            </a> <a class=\"tip\" title='" . lang("list_addresses") . "' href='" . admin_url('customers/addresses/$1') . "' data-toggle='modal' data-target='#myModal'>
                <i class=\"fa fa-location-arrow\"></i>
            </a>
            </a> <a class=\"tip list-notes\" data-company-id='$1' href='" . admin_url('customers/notes/$1/customers') . "' data-toggle='modal' data-target='#myModal' data-last-note='@@@@@'>
                <i class=\"fa fa-newspaper-o\"></i>
            </a>
            <a class=\"tip\" title='" . lang("list_users") . "' href='" . admin_url('customers/users/$1') . "' data-toggle='modal' data-target='#myModal'>
                <i class=\"fa fa-users\"></i></a> <a class=\"tip\" title='" . lang("add_user") . "' href='" . admin_url('customers/add_user/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-user-plus\"></i>
            </a>
            <a class=\"tip\" title='" . lang("edit_customer") . "' href='" . admin_url('customers/edit/$1') . "' data-toggle='modal' data-target='#myModal'>
                <i class=\"fa fa-edit\"></i>
            </a>
            <a href='#' class='tip po' title='<b>" . lang("delete_customer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('customers/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'>
                <i class=\"fa fa-trash-o\"></i>
            </a>
        </div>", "id");
        //->unset_column('id');

        $json = $this->datatables->generate();
        $arr = json_decode($json, true);

        foreach($arr['aaData'] as &$row) {
            $avg_data = $this->reports_model->getAverageReportForCustomer($row[0]);
            $row[4] = $avg_data['avg_buying_date'];
            $row[5] = $avg_data['avg_product_name'];
            $row[6] = $avg_data['days_inactive'];

            $last_note = $this->getLatestUnreadNote($row[0]);
            $row[7] = str_replace('@@@@@', htmlspecialchars(json_encode($last_note), ENT_QUOTES, 'UTF-8'), $row[7]);
        }

        switch($_POST['iSortCol_0']) {
            case 4:
            case 5:
            case 6:
            usort($arr['aaData'], "self::sortCustomValues");
                break;
        }

        return json_encode($arr);
    }

    // **************************************** [CUSTOMER NOTES ****************************************/
    /**
     * @param $data
     * @param $user_id
     * @return mixed
     */
    public function addNote($data, $user_id) {

        $data['isDeleted'] = 0;
        $data['created'] = date('Y-m-d H:i:s');
        $data['modified'] = date('Y-m-d H:i:s');

        $success = $this->db->insert('company_notes', $data);

        $note_id = $this->db->insert_id();

        $this->load->admin_model('company_notes_readby_model');
        $this->company_notes_readby_model->add(array(
            'user_id' => $user_id,
            'note_id' => $note_id
        ));

        return $success;
    }

    /**
     * @param $note_id
     * @param $data
     * @return bool
     */
    public function updateNote($note_id, $data) {

        $data['modified'] = date('Y-m-d H:i:s');

        if ($this->db->update('company_notes', $data, array('id' => $note_id))) {
            return true;
        }
        return false;
    }

    /**
     * @param $note_id
     * @return bool
     */
    public function deleteNote($note_id) {

        $data['isDeleted'] = 1;
        $data['modified'] = date('Y-m-d H:i:s');

        if ($this->db->update('company_notes', $data, array('id' => $note_id))) {
            return true;
        }
        return false;
    }

    /**
     * @param $customer_id
     * @param $page
     * @return mixed
     */
    public function getNotes($customer_id, $page) {
        $this->datatables->select('id, created, companyTitle, description')
            ->from('company_notes')
            ->where('companyId', $customer_id)
            ->where('isDeleted', 0)
            ->add_column("Actions", "<div class=\"text-center\">
                <a class=\"tip send-note-msg\" title='Send message related to this note' href='" . admin_url('messages/add/$1') . "' data-toggle='modal' data-target='#myModal2'>
                    <i class=\"fa fa-envelope-o\"></i>
                </a>
                <a class=\"tip edit-note\" title='" . lang("edit_note") . "' data-id='$1' href='" . admin_url('customers/edit_note/'.$customer_id.'/'.$page.'/$1') . "' data-toggle='modal' data-target='#myModal2'>
                    <i class=\"fa fa-edit\"></i>
                </a>
                <a href='#' class='tip po' title='<b>" . lang("delete_note") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p>
                    <a class='btn btn-danger po-delete' href='" . admin_url('customers/delete_note/$1') . "'>" . lang('i_m_sure') . "</a>
                    <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i>
                </a>
            </div>", "id");

        $json = $this->datatables->generate();
        $arr = json_decode($json, true);

        foreach($arr['aaData'] as &$row) {
            $user_row = $this->db->select('CONCAT(u.first_name, " ", u.last_name) AS userName', false)
                ->from('company_notes_readby cnrb')
                ->join('users u', 'cnrb.user_id = u.id', 'INNER')
                ->order_by('cnrb.modified', 'ASC')
                ->where('cnrb.note_id', $row[0])
                ->limit(1)
                ->get()->row();

            if(is_object($user_row)) {
                $row[1] .= '<br/>' . $user_row->userName;
            }
        }

        echo json_encode($arr);
    }

    /**
     * @param $note_id
     * @return bool
     */
    public function getNoteById($note_id) {
        $result = $this->db->select('company_notes.*, companies.name, companies.company, CONCAT(first_name, " ", last_name) AS username', false)
                ->from('company_notes')
                ->join('companies', 'company_notes.companyId = companies.id', 'INNER')
                ->join('company_notes_readby', 'company_notes_readby.note_id = company_notes.id', 'INNER')
                ->join('users', 'company_notes_readby.user_id = users.id', 'INNER')
                ->where('company_notes.id', $note_id)
                ->limit(1)
                ->get()
                ->result_array();

        if(count($result) > 0) {
            return $result[0];
        }
        return false;
    }

    /**
     * @param $customer_id
     * @return bool
     */
    public function getLatestUnreadNote($customer_id) {
        $q = $this->db->select('companyTitle, description')
            ->order_by('created', 'desc')
            ->limit(1)
            ->get_where('company_notes', array('companyId' => $customer_id, 'isDeleted'=>0));

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }



    // **************************************** CUSTOMER NOTES] ****************************************/



}
