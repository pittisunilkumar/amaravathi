<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feegrouptype_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function getFeeGroupByID($id)
    {
        $query                = "SELECT fee_groups_feetype.*,fee_groups.name as `fee_group_name`,feetype.type,feetype.code FROM `fee_groups_feetype` INNER JOIN fee_groups on fee_groups_feetype.fee_groups_id=fee_groups.id INNER JOIN feetype on feetype.id=fee_groups_feetype.feetype_id WHERE fee_groups_feetype.id=" . $this->db->escape($id);
        $query                = $this->db->query($query);
        $fee_group_type_array = $query->row();
        return $fee_group_type_array;
    }

}
