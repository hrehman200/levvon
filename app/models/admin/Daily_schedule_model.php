<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Daily_schedule_model extends CI_Model {

    const TBL = 'daily_schedule';

    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $user_id
     * @param int $is_read
     * @param string $date
     * @return array
     */
    public function get($user_id, $is_read = 0, $date = '') {
        $arr_where = array(
            'user_id' => $user_id
        );

        if ($is_read !== null) {
            $arr_where['is_read'] = $is_read;
        }

        if ($date != '') {
            $this->db->like('date', $date);
        }

        return $this->db->order_by('date, time', 'ASC')
            ->get_where(self::TBL, $arr_where)->result_array();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id) {
        return $this->db->get_where(self::TBL, array('id' => $id))->row();
    }

    /**
     * @param $user_id
     * @param $date
     * @return mixed
     */
    public function getSchedules($user_id, $date) {
        $this->datatables->select('id, date, time, note')
            ->from('daily_schedule')
            ->where('user_id', $user_id)
            //->where('is_read', $is_read)
            ->where('date', $date)
            ->add_column("Actions", "<div class=\"text-center\">
                <a class=\"tip\" title='" . lang("edit_note") . "' href='" . admin_url('daily_schedule/edit/$1/$2') . "' data-toggle='modal' data-target='#myModal2'>
                    <i class=\"fa fa-edit\"></i>
                </a>
                <a href='#' class='tip po' title='<b>" . lang("delete_note") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p>
                    <a class='btn btn-danger po-delete' href='" . admin_url('daily_schedule/delete/$2') . "'>" . lang('i_m_sure') . "</a>
                    <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i>
                </a>
            </div>", "date,id")
            ->unset_column('id')
            ->unset_column('date');

        return $this->datatables->generate();
    }

    /**
     * @param $data
     */
    public function save($data) {
        if ($data['id'] > 0) {
            return $this->db->where('id', $data['id'])
                ->update(self::TBL, $data);

        } else {
            return $this->db->insert(self::TBL, $data);
        }
    }

    /**
     * @param $user_id
     * @param $date
     * @return mixed
     */
    public function markRead($user_id, $date) {
        return $this->db->where('user_id', $user_id)
            ->where('date', $date)
            ->update(self::TBL, array('is_read' => 1));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id) {
        return $this->db->where('id', $id)
            ->delete(self::TBL);
    }

}