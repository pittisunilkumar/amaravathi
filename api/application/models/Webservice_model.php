<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Webservice_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $CI = &get_instance();
        $CI->load->model('setting_model');
        $CI->load->model('staff_model');
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function getStudentProfile($user_id)
    {
        $this->db->select('student_session.transport_fees,students.vehroute_id,vehicle_routes.route_id,vehicle_routes.vehicle_id,transport_route.route_title,vehicles.vehicle_no,hostel_rooms.room_no,vehicles.driver_name,vehicles.driver_contact,hostel.id as `hostel_id`,hostel.hostel_name,room_types.id as `room_type_id`,room_types.room_type ,students.hostel_room_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.religion, students.cast, school_houses.house_name,   students.dob ,students.current_address, students.previous_school,
            students.guardian_is,students.parent_id,
            students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = students.hostel_room_id', 'left');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left');
        $this->db->join('vehicle_routes', 'vehicle_routes.id = students.vehroute_id', 'left');
        $this->db->join('transport_route', 'vehicle_routes.route_id = transport_route.id', 'left');
        $this->db->join('vehicles', 'vehicles.id = vehicle_routes.vehicle_id', 'left');
        $this->db->join('school_houses', 'school_houses.id = students.school_house_id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);

        $this->db->where('students.id', $user_id);

        $q = $this->db->get();

        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'Profile Not Found!');
        } else {
            $data['success'] = 1;
            $data['data']    = $q->result();

            return $data;
        }
    }

    public function getHomework($className, $sectionName)
    {
        $this->db->select('homework.*, subjects.name as subjectName');
        $this->db->from('homework');
        $this->db->join('subjects', 'homework.subject_id = subjects.id');
        $this->db->where('class_id', $className);
        $this->db->where('section_id', $sectionName);
        $q = $this->db->get();

        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No Homework Found');
        } else {
            $data['success'] = 1;
            $data['data']    = $q->result();
            return $data;
        }
    }

    public function getHomeworkDetails($homeworkId, $student_id)
    {
        $this->db->select('homework.*,homework_evaluation.date as evaluation_date, homework_evaluation.status, subjects.name as subjectName,staff.name as `evaluated_by_name`,staff.surname as `evaluated_by_surname`,created.name as `created_by_name`,created.surname as `created_by_surname`');
        $this->db->from('homework');
        $this->db->join('subjects', 'homework.subject_id = subjects.id');
        $this->db->join('homework_evaluation', 'homework_evaluation.homework_id = homework.id and homework_evaluation.student_id =' . $student_id);
        $this->db->join('staff as `created`', 'created.id = homework.created_by');
        $this->db->join('staff', 'staff.id = homework.evaluated_by');
        $this->db->where('homework.id', $homeworkId);
        $q = $this->db->get();

        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No Homework found.');
        } else {
            $data['success'] = 1;
            $data['data']    = $q->result();
            return $data;
        }
    }

    public function getExamListByClassandSection($classId, $sectionId, $studentSession)
    {
        $sql = "SELECT * FROM exams INNER JOIN (SELECT exam_schedules.*,teacher_subjects.class_id,teacher_subjects.class_name ,teacher_subjects.section_id,teacher_subjects.section_name FROM `exam_schedules` INNER JOIN (SELECT teacher_subjects.*,classes.id as `class_id`,classes.class as `class_name` ,sections.id as `section_id`,sections.section as `section_name` FROM `class_sections`
        INNER JOIN teacher_subjects on teacher_subjects.class_section_id=class_sections.id
        INNER JOIN classes on classes.id=class_sections.class_id
        INNER JOIN sections on sections.id=class_sections.section_id
        WHERE class_sections.class_id =" . $classId . " and class_sections.section_id=" . $sectionId . " and teacher_subjects.session_id=" . $studentSession . ") as teacher_subjects on teacher_subjects.id=teacher_subject_id group by exam_schedules.exam_id) as exam_schedules on exams.id=exam_schedules.exam_id";

        $q = $this->db->query($sql);

        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No exams found.');
        } else {
            $data['success'] = 1;
            $data['data']    = $q->result_array();
            return $data;
        }
    }

    public function getExamSchedule($examId)
    {
        $this->db->select('exam_schedules.teacher_subject_id, exam_schedules.date_of_exam, exam_schedules.start_to,  exam_schedules.end_from,  exam_schedules.room_no, exam_schedules.full_marks, exam_schedules.passing_marks, teacher_subjects.subject_id, subjects.name, subjects.type');
        $this->db->from('exam_schedules');
        $this->db->join('teacher_subjects', 'exam_schedules.teacher_subject_id = teacher_subjects.id');
        $this->db->join('subjects', 'subjects.id = teacher_subjects.subject_id');
        $this->db->where('exam_id', $examId);
        $q = $this->db->get();
        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No exam found.');
        } else {
            $data['success'] = 1;
            $data['data']    = $q->result_array();
            return $data;
        }
    }

    public function getNotifications($type)
    {
        $date = date('Y-m-d');
        $this->db->select('send_notification.*,staff.employee_id');
        $this->db->from('send_notification');
        $this->db->join('staff', 'staff.id = send_notification.created_id');
        
        if ($type == "student") {
            $this->db->where('visible_student', 'Yes');
        } elseif ($type == "parent") {
            # code...
            $this->db->where('visible_parent', 'Yes');
        }
        $this->db->where('publish_date <=', $date);
        $this->db->order_by('date', 'desc');
        $q = $this->db->get();

        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No notification found.');
        } else {
            $data['success'] = 1;
            $result          = $q->result_array();
            foreach ($result as $key => $value) {
                
                if (!empty($value['attachment'])) {
                    $result[$key]['attachment'] = "uploads/notice_board_images/" . $value['attachment'];                     
                }else{
                    $result[$key]['attachment'] = '';
                }
                
                if (!empty($value['created_id'])) {
                    $created_by = $this->staff_model->getAll($value['created_id']);                    
                    $result[$key]['created_by'] =  $created_by['name'].' '.$created_by['surname'];                    
                }else{                    
                    $result[$key]['created_by'] = '';                     
                }
            }
            $data['data'] = $result;
            return $data;
        }
    }

    public function getSubjectList($class_id, $section_id)
    {
        $sql   = "SELECT staff.name as `teacher_name`, staff.surname, subjects.name as `subjectName`,subjects.type,subjects.code FROM `teacher_subjects` INNER JOIN subjects ON teacher_subjects.subject_id = subjects.id INNER JOIN class_sections ON teacher_subjects.class_section_id = class_sections.id INNER JOIN staff ON staff.id = teacher_subjects.teacher_id  WHERE class_sections.class_id =" . $class_id . " and class_sections.section_id=" . $section_id;
        $query = $this->db->query($sql);

        if ($query->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No subjects found.');
        } else {
            $data['success'] = 1;
            $data['data']    = $query->result_array();
            return $data;
        }
    }

    public function getTeachersList($sectionId)
    {
        $this->db->select('teacher_id, staff.name, staff.surname, staff.email, staff.contact_no');
        $this->db->from('teacher_subjects');
        $this->db->join('staff', 'teacher_subjects.teacher_id = staff.id');
        $this->db->where('class_section_id', $sectionId);
        $this->db->group_by('teacher_subjects.teacher_id');
        $q = $this->db->get();

        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No teachers found.');
        } else {
            $data['success'] = 1;
            $data['data']    = $q->result_array();
            return $data;
        }
    }

    public function getLibraryBooks()
    {
        $q = $this->db->select('*')->from('books')->order_by('books.id','desc')->get();

        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No books found.');
        } else {
            $data['success'] = 1;
            $data['data']    = $q->result();
            return $data;
        }
    }

    public function getLibraryBookIssued()
    {
        $user_id = $this->input->get_request_header('User-ID', true);
        $this->db->select('*');
        $this->db->from('book_issues');
        $this->db->join('books', 'books.id = book_issues.book_id');
        $this->db->where('member_id', $user_id);
        $this->db->where('is_returned', '0');
        $q = $this->db->get();

        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No books issued.');
        } else {
            $data['success'] = 1;
            $data['data']    = $q->result();
            return $data;
        }
    }

    public function getTransportRoute()
    {
        $user_id = $this->input->get_request_header('User-ID', true);
        $this->db->select('transport_route.route_title, vehicles.vehicle_no');
        $this->db->from('transport_route');
        $this->db->join('vehicle_routes', 'transport_route.id = vehicle_routes.route_id');
        $this->db->join('vehicles', 'vehicle_routes.vehicle_id = vehicles.id');
        $q = $this->db->get();

        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No route found.');
        } else {
            $data['success'] = 1;
            $data['data']    = $q->result();
            return $data;
        }
    }

    public function getHostelList_old()
    {
        $this->db->select('id, hostel_name, type');
        $this->db->from('hostel');
        $q = $this->db->get();

        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No hostel found.');
        } else {
            $data['success'] = 1;
            $data['data']    = $q->result();
            return $data;
        }
    }

    public function getHostelList()
    {
        $this->db->select('hostel_rooms.*,b.hostel_name,c.room_type');
        $this->db->from('hostel_rooms');
        $this->db->join('hostel b', 'b.id=hostel_rooms.hostel_id');
        $this->db->join('room_types c', 'c.id=hostel_rooms.room_type_id');
        $listroomtype = $this->db->get();
        return $listroomtype->result_array();
    }

    public function getHostelDetails($hostelId, $student_id)
    {
        $sql = "SELECT `hostel_rooms`.*, `room_types`.`room_type`,IFNULL(students.id, 0) as `student_id` FROM `hostel_rooms` JOIN `room_types` ON `hostel_rooms`.`room_type_id` = `room_types`.`id` LEFT JOIN students on hostel_rooms.id=students.hostel_room_id and students.id=" . $this->db->escape($student_id) . " WHERE `hostel_id` = " . $this->db->escape($hostelId);
        $q   = $this->db->query($sql);
        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No hostel found.');
        } else {
            $data['success'] = 1;
            $data['data']    = $q->result();
            return $data;
        }
    }

    public function getStudentsharelist($student_id, $class_id, $section_id)
    {
         
        $sql="SELECT `share_contents`.*, `staff`.`name`, `staff`.`surname`, `staff`.`employee_id` FROM `share_contents` JOIN `staff` ON `share_contents`.`created_by` = `staff`.`id` WHERE share_contents.id in (SELECT share_content_id FROM `share_content_for` WHERE group_id ='student' or student_id='".$student_id."' or class_section_id=(SELECT class_sections.id from class_sections WHERE class_sections.class_id='".$class_id."' and class_sections.section_id='".$section_id."')) ORDER BY `share_contents`.`id` DESC";
        $listroomtype = $this->db->query($sql);
        $result  =   $listroomtype->result_array(); 
        
        foreach ($result as $result_key => $result_value) {
            $result[$result_key]['upload_contents'] = $this->getShareContentDocumentsByID($result_value['id']);
                   
        }
        return $result;                 
         
    }
    
    public function getParentsharelist($user_parent_id, $class_id, $section_id)
    {
        $sql="SELECT `share_contents`.*, `staff`.`name`, `staff`.`surname`, `staff`.`employee_id` FROM `share_contents` JOIN `staff` ON `share_contents`.`created_by` = `staff`.`id` WHERE share_contents.id in (SELECT share_content_id FROM `share_content_for` WHERE group_id ='parent' or user_parent_id='".$user_parent_id."' or class_section_id=(SELECT class_sections.id from class_sections WHERE class_sections.class_id='".$class_id."' and class_sections.section_id='".$section_id."')) ORDER BY `share_contents`.`id` DESC";
        $listroomtype = $this->db->query($sql);
        $result  =   $listroomtype->result_array(); 

        foreach ($result as $result_key => $result_value) {
            $result[$result_key]['upload_contents'] = $this->getShareContentDocumentsByID($result_value['id']);
                   
        }
        return $result;  
    }
    
    public function getShareContentDocumentsByID($share_content_id = null)
    {
        $result = array();
        $this->db->select('share_upload_contents.*,upload_contents.real_name,upload_contents.thumb_path,upload_contents.dir_path,upload_contents.img_name,upload_contents.thumb_name,upload_contents.file_type,upload_contents.mime_type,upload_contents.vid_url,upload_contents.vid_title')->from('share_upload_contents');
        $this->db->where('share_upload_contents.share_content_id', $share_content_id);
        $this->db->join('upload_contents', 'upload_contents.id = share_upload_contents.upload_content_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result();
            return $result;

        }else{
            return $result;
        }
    }

    public function getTransportVehicleDetails($vehicleId)
    {
        $this->db->select('*');
        $this->db->from('vehicles');
        $this->db->where('id', $vehicleId);
        $q = $this->db->get();

        if ($q->num_rows() == 0) {
            return array('status' => 401, 'message' => 'Profile not found.');
        } else {
            return $q->result();
        }
    }

    public function getAttendenceRecords($sessionId)
    {
        $this->db->select('student_attendences.*, attendence_type.type');
        $this->db->from('student_attendences');
        $this->db->join('attendence_type', 'student_attendences.attendence_type_id = attendence_type.id');
        $this->db->where('student_session_id', $sessionId);
        $q = $this->db->get();

        if ($q->num_rows() == 0) {
            return array('status' => 401, 'message' => 'Profile not found.');
        } else {
            return $q->result();
        }
    }

    public function getStudent($id = null)
    {
        $this->db->select('student_session.transport_fees,students.vehroute_id,vehicle_routes.route_id,vehicle_routes.vehicle_id,transport_route.route_title,vehicles.vehicle_no,hostel_rooms.room_no,vehicles.driver_name,vehicles.driver_contact,hostel.id as `hostel_id`,hostel.hostel_name,room_types.id as `room_type_id`,room_types.room_type ,students.hostel_room_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast, school_houses.house_name,   students.dob ,students.current_address, students.previous_school,
            students.guardian_is,students.parent_id,
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = students.hostel_room_id', 'left');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left');
        $this->db->join('vehicle_routes', 'vehicle_routes.id = students.vehroute_id', 'left');
        $this->db->join('transport_route', 'vehicle_routes.route_id = transport_route.id', 'left');
        $this->db->join('vehicles', 'vehicles.id = vehicle_routes.vehicle_id', 'left');
        $this->db->join('school_houses', 'school_houses.id = students.school_house_id', 'left');

        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {
            $this->db->where('students.is_active', 'yes');
            $this->db->order_by('students.id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

}
