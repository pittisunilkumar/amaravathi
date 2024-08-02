<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Video_tutorial_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getvideotutorial($class_id, $section_id)
    {
        $this->db->select('video_tutorial.*,class_sections.class_id,class_sections.section_id,classes.class,sections.section,staff.name,staff.surname,staff.employee_id')
            ->join('staff', 'staff.id = video_tutorial.created_by')
            ->join('video_tutorial_class_sections', 'video_tutorial_class_sections.video_tutorial_id=video_tutorial.id')
            ->join('class_sections', 'class_sections.id=video_tutorial_class_sections.class_section_id')
            ->join('classes', 'classes.id=class_sections.class_id')
            ->join('sections', 'sections.id=class_sections.section_id')
            ->from('video_tutorial');
        $this->db->where('class_sections.class_id', $class_id);
        $this->db->where('class_sections.section_id', $section_id);
        $this->db->order_by('video_tutorial.id', 'DESC');
        $this->db->group_by('video_tutorial_class_sections.video_tutorial_id');
        $query = $this->db->get();
        return $query->result_array();
    }

}
