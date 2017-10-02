<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Company_notes_readby_model extends CI_Model {

    const TBL = 'company_notes_readby';

    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function add($data) {
        return $this->db->insert(self::TBL, $data);
    }

    /**
     * @param $note_ids
     * @param $user_id
     */
    public function markNotesAsRead($note_ids, $user_id) {
        for($i=0; $i<count($note_ids); $i++) {
            $this->add(array(
                'note_id' => $note_ids[$i],
                'user_id' => $user_id
            ));
        }
    }

    /**
     * @param $company_ids
     * @param $user_id
     * @return mixed
     */
    public function getUnreadCompanyNotesCount(array $company_ids, $user_id) {

        $query = sprintf("SELECT COUNT(cn.id) AS a, cn.companyId
            FROM %s cn
            WHERE cn.companyId IN (%s) AND isDeleted = 0
            GROUP BY cn.companyId",
            $this->db->dbprefix('company_notes'), implode(",", $company_ids));
        $total_result = $this->db->query($query)->result_array();

        $query = sprintf("SELECT COUNT(user_id) AS a, cn.companyId
            FROM %s cnrb
            INNER JOIN %s cn ON cn.id = cnrb.note_id
            WHERE cnrb.user_id = %d AND cn.companyId IN (%s) AND cn.isDeleted = 0
            GROUP BY cn.companyId",
            $this->db->dbprefix('company_notes_readby'), $this->db->dbprefix('company_notes'), $user_id, implode(",", $company_ids));
        $read_result = $this->db->query($query)->result_array();

        $arr = array();
        foreach($total_result as $total_row) {
            $company_id = $total_row['companyId'];

            $row_find = array_filter($read_result, function($read_row) use ($company_id) {
                return $company_id == $read_row['companyId'];
            });

            $read = 0;
            if(count($row_find) > 0) {
                $row_reset = array_values($row_find);
                $read = $row_reset[0]['a'];
            }

            $arr[] = array(
                'company_id'=>$company_id,
                'unread'=>($total_row['a'] - $read)
            );
        }

        return array('data'=>$arr);
    }
}
