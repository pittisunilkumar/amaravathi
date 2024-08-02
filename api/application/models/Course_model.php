<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Course_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function coursedetail($courseid)
    {
        $this->db->select('online_courses.*,online_course_class_sections.id as class_sections_id,classes.class,staff.name,staff.surname,staff.image,staff.gender,sections.section,class_sections.class_id,class_sections.section_id')->from('online_courses');
        $this->db->where('online_courses.id', $courseid);
        $this->db->join('staff', 'staff.id = online_courses.teacher_id');
        $this->db->join('online_course_class_sections', 'online_course_class_sections.course_id = online_courses.id');
        $this->db->join('class_sections', 'class_sections.id =  online_course_class_sections.class_section_id');
        $this->db->join('classes', 'classes.id = class_sections.class_id');
        $this->db->join('sections', 'sections.id = class_sections.class_id');
        $this->db->group_by('online_course_class_sections.course_id');
        $this->db->order_by('online_courses.title', 'asc');
        $query  = $this->db->get();
        $result = $query->row_array();
        if (!empty($result['image'])) {
            $result['image'] = $result['image'];
        } else {
            if ($result['gender'] == 'Female') {
                $result['image'] = "default_female.jpg";
            } else {
                $result['image'] = "default_male.jpg";
            }
        }

        $result['total_hour']   = $this->counthours($courseid);
        $result['lesson_count'] = count($this->totallessonbycourse($courseid));
        $result['quiz_count']   = count($this->totalquizbycourse($courseid));
        return $result;
    }

    public function courselistforstudent($classID, $sectionID)
    {
        $this->db->select('online_courses.*,classes.class,staff.name,staff.surname,staff.image,staff.gender,staff.id as staff_id,staff.employee_id as staff_employee_id,sections.section,created_staff.name as created_staff_name,created_staff.surname as created_staff_surname,created_staff.employee_id as created_staff_employee_id')->from('online_courses');
        $this->db->join('staff', 'staff.id = online_courses.teacher_id');
        $this->db->join('staff as created_staff', 'created_staff.id = online_courses.created_by');        
        $this->db->join('online_course_class_sections', 'online_course_class_sections.course_id = online_courses.id', 'left');
        $this->db->join('class_sections', 'class_sections.id =  online_course_class_sections.class_section_id', 'left');
        $this->db->join('classes', 'classes.id = class_sections.class_id', 'left');
        $this->db->join('sections', 'sections.id = class_sections.class_id', 'left');
        $this->db->group_by('online_course_class_sections.course_id');
        $this->db->where('class_sections.class_id', $classID);
        $this->db->where('class_sections.section_id', $sectionID);
        $this->db->where('online_courses.status', 1);
        $this->db->order_by('online_courses.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function totallessonbycourse($courseID)
    {
        $this->db->select('online_course_lesson.*')->from('online_course_section');
        $this->db->join('online_course_lesson', 'online_course_lesson.course_section_id = online_course_section.id');
        $this->db->where('online_course_section.online_course_id', $courseID);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function counthours($id)
    {
        $this->db->select('online_course_lesson.duration,online_course_lesson.lesson_type')
            ->join("online_course_section", "online_course_section.online_course_id = online_courses.id")
            ->join("online_course_lesson", "online_course_lesson.course_section_id = online_course_section.id")
            ->where("online_course_lesson.lesson_type", 'video')
            ->where("online_courses.id", $id);
        $query     = $this->db->get('online_courses');
        $result    = $query->result_array();
        $totaltime = 0;
        $hours     = 0;
        $min       = 0;
        $sec       = 0;
        $total     = 0;
        $hh        = 0;
        $mm        = 0;
        $ss        = 0;

        foreach ($result as $rs) {
            if ($rs['lesson_type'] == 'video') {
                $str_arr = explode(":", $rs['duration']);
                if (!empty($str_arr[0])) {
                    $hh = $str_arr[0] * 3600;
                }
                if (!empty($str_arr[1])) {
                    $mm = $str_arr[1] * 60;
                }
                if (!empty($str_arr[2])) {
                    $ss = $str_arr[2];
                }
                $total = $hh + $mm + $ss;
            }
            $totaltime += $total;
        }
        $hours = intval($totaltime / 3600);
        $min1  = $totaltime - ($hours * 3600);
        $min   = intval($min1 / 60);
        $sec   = $totaltime - (($min * 60) + ($hours * 3600));
        if ($hours < 10) {$hours = "0" . $hours;}
        if ($min < 10) {$min = "0" . $min;}
        if ($sec < 10) {$sec = "0" . $sec;}
        return $hours . ':' . $min . ':' . $sec;
    }

    public function paidstatus($courseid, $studentid)
    {
        $this->db->select('*');
        $this->db->from('online_course_payment');
        $this->db->where('online_course_payment.online_courses_id', $courseid);
        $this->db->where('online_course_payment.student_id', $studentid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function courseprogresscount($courseid, $student_id)
    {
        $result = $this->db->select("id")
            ->where(array("course_id" => $courseid, "student_id" => $student_id))
            ->get("course_progress")
            ->result_array();
        return $result;
    }

    public function totalquizbycourse($courseID)
    {
        $this->db->select('online_course_quiz.*')->from('online_course_quiz');
        $this->db->join('online_course_section', 'online_course_section.id = online_course_quiz.course_section_id');
        $this->db->join('online_courses', 'online_courses.id = online_course_section.online_course_id');
        $this->db->where('online_courses.id', $courseID);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getsectionbycourse($course_id, $student_id)
    {
        $this->db->select('online_course_section.*,online_courses.title');
        $this->db->from('online_course_section');
        $this->db->join('online_courses', 'online_courses.id=online_course_section.online_course_id');
        $this->db->where('online_course_section.online_course_id', $course_id);
        $this->db->order_by('online_course_section.order', 'asc');
        $query  = $this->db->get();
        $result = $query->result();

        foreach ($result as $key => $sectionList_value) {

            $chackquizcount   = count($this->numberofquiz($sectionList_value->id));
            $chacklessoncount = count($this->numberoflesson($sectionList_value->id));
            $chackcount       = $chackquizcount + $chacklessoncount;
            if ($chackcount > 0) {
                $result[$key]->lesson_quiz = $this->lessonquizbysection($sectionList_value->id, $student_id, $course_id);
            } else {
                $result[$key]->lesson_quiz = array();
            }
        }
        return $result;
    }

    public function numberofquiz($sectionid)
    {
        $this->db->select('online_course_quiz.*')->from('online_course_quiz');
        $this->db->where('online_course_quiz.course_section_id', $sectionid);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function numberoflesson($sectionid)
    {
        $this->db->select('online_course_lesson.*')->from('online_course_lesson');
        $this->db->where('online_course_lesson.course_section_id', $sectionid);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function lessonquizbysection($sectionid, $student_id, $course_id)
    {
        $this->db->select('online_course_lesson.id as lesson_id,online_course_lesson.lesson_title,online_course_lesson.lesson_type,online_course_lesson.thumbnail,online_course_lesson.summary,online_course_lesson.attachment,online_course_lesson.video_provider,online_course_lesson.video_url,online_course_lesson.video_id,online_course_lesson.duration,online_course_quiz.id as quiz_id,online_course_quiz.quiz_title,course_lesson_quiz_order.id,course_lesson_quiz_order.type,course_lesson_quiz_order.order,course_lesson_quiz_order.course_section_id,course_lesson_quiz_order.lesson_quiz_id');
        $this->db->from('online_course_section');
        $this->db->join('course_lesson_quiz_order', 'course_lesson_quiz_order.course_section_id=online_course_section.id');
        $this->db->join('online_course_quiz', 'online_course_quiz.id=course_lesson_quiz_order.lesson_quiz_id', 'left');
        $this->db->join('online_course_lesson', 'online_course_lesson.id=course_lesson_quiz_order.lesson_quiz_id');
        $this->db->order_by('course_lesson_quiz_order.order', 'asc');
        $this->db->where('online_course_section.id', $sectionid);
        $query  = $this->db->get();
        $result = $query->result();
        foreach ($result as $key => $result_value) {
            if ($result_value->video_provider == 's3_bucket') {
                $result[$key]->video_url = $this->aws3->generateUrl($result_value->video_id);
            }

            if ($result_value->type == 'lesson') {
                $ptype                     = 1;
                $result[$key]->quiz_status = 0;
            } elseif ($result_value->type == 'quiz') {
                $ptype       = 2;
                $quiz_status = $this->quizsubmitstatus($result_value->lesson_quiz_id, $student_id);
                if (!empty($quiz_status)) {
                    $result[$key]->quiz_status = $quiz_status['status'];
                } else {
                    $result[$key]->quiz_status = 0;
                }
            }

            $course_progress = $this->course_progress($student_id, $course_id, $result_value->course_section_id, $result_value->lesson_quiz_id, $ptype);

            if (!empty($course_progress)) {
                $result[$key]->progress = 1;
            } else {
                $result[$key]->progress = 0;
            }
        }
        return $result;
    }

    public function lessonbysection($id, $student_id, $course_id)
    {
        $this->db->select('online_course_lesson.lesson_title as title,online_course_lesson.id,online_course_lesson.course_section_id,online_course_lesson.lesson_type,online_course_lesson.thumbnail,online_course_lesson.summary,online_course_lesson.attachment,online_course_lesson.video_provider,online_course_lesson.video_url,online_course_lesson.video_id,online_course_lesson.duration,online_course_lesson.created_date');
        $this->db->from('online_course_lesson');
        $this->db->where('online_course_lesson.course_section_id', $id);
        $query  = $this->db->get();
        $result = $query->result();
        foreach ($result as $key => $result_value) {
            $course_progress = $this->course_progress($student_id, $course_id, $id, $result_value->id, 1);

            if (!empty($course_progress)) {
                $result[$key]->progress = 1;
            } else {
                $result[$key]->progress = 0;
            }
        }
        return $result;
    }

    public function quizbysection($id, $student_id, $course_id)
    {
        $this->db->select('online_course_quiz.id as id,online_course_quiz.quiz_title as title,online_course_quiz.quiz_instruction');
        $this->db->from('online_course_quiz');
        $this->db->where('online_course_quiz.course_section_id', $id);
        $query  = $this->db->get();
        $result = $query->result();
        foreach ($result as $key => $result_value) {

            $course_progress = $this->course_progress($student_id, $course_id, $id, $result_value->id, 2);
            $quiz_status     = $this->quizsubmitstatus($result_value->id, $student_id);
            if (!empty($quiz_status)) {
                $result[$key]->quiz_status = $quiz_status['status'];
            } else {
                $result[$key]->quiz_status = 0;
            }

            if (!empty($course_progress)) {
                $result[$key]->progress = 1;
            } else {
                $result[$key]->progress = 0;
            }
        }
        return $result;
    }

    public function course_progress($student_id, $course_id, $course_section_id, $lesson_quiz_id, $lesson_quiz_type)
    {
        $this->db->select('course_progress.id');
        $this->db->from('course_progress');
        $this->db->where('course_progress.student_id', $student_id);
        $this->db->where('course_progress.course_id', $course_id);
        $this->db->where('course_progress.course_section_id', $course_section_id);
        $this->db->where('course_progress.lesson_quiz_id', $lesson_quiz_id);
        $this->db->where('course_progress.lesson_quiz_type ', $lesson_quiz_type);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function quizsubmitstatus($id, $student_id)
    {
        $this->db->select('student_quiz_status.status');
        $this->db->from('student_quiz_status');
        $this->db->where('student_quiz_status.course_quiz_id', $id);
        $this->db->where('student_quiz_status.student_id', $student_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getquestionbyquizid($quizID, $student_id)
    {
        $this->db->select('course_quiz_question.id,course_quiz_question.question,course_quiz_question.option_1,course_quiz_question.option_2,course_quiz_question.option_3,course_quiz_question.option_4,course_quiz_question.option_5,course_quiz_question.correct_answer')->from('course_quiz_question');
        $this->db->where('course_quiz_question.course_quiz_id', $quizID);
        $this->db->group_by('course_quiz_question.id');
        $query  = $this->db->get();
        $result = $query->result();
        foreach ($result as $key => $result_value) {
            $answer = $this->getstudentanswer($quizID, $student_id, $result_value->id);
            if (!empty($answer)) {
                $result[$key]->studentanswer = $answer['answer'];
            } else {
                $result[$key]->studentanswer = $answer['answer'];
            }
        }
        return $result;
    }

    public function getstudentanswer($quizID, $studentid, $questionid)
    {
        $this->db->select('*')->from('course_quiz_answer');
        $this->db->where('course_quiz_answer.course_quiz_id', $quizID);
        $this->db->where('course_quiz_answer.student_id', $studentid);
        $this->db->where('course_quiz_answer.course_quiz_question_id', $questionid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getquizresult($quizID, $studentid)
    {
        $this->db->select('course_quiz_question.id,course_quiz_question.question,course_quiz_question.option_1,course_quiz_question.option_2,course_quiz_question.option_3,course_quiz_question.option_4,course_quiz_question.option_5,course_quiz_question.correct_answer,course_quiz_answer.answer')->from('course_quiz_question');
        $this->db->join('course_quiz_answer', 'course_quiz_question.id = course_quiz_answer.course_quiz_question_id', 'left');
        $this->db->where('course_quiz_question.course_quiz_id', $quizID);
        $this->db->where('course_quiz_answer.student_id', $studentid);
        $this->db->group_by('course_quiz_question.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function quizresult($quizID, $studentid)
    {
        $this->db->select('*')->from('student_quiz_status');
        $this->db->where('course_quiz_id', $quizID);
        $this->db->where('student_id', $studentid);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function quizstudentanswerlist($quizID, $studentid)
    {
        $answerlist = $this->getquestionbyquizid($quizID, $studentid);
        foreach ($answerlist as $key => $answerlist_value) {
            $submit_answer = json_decode($answerlist_value->studentanswer);
            $option        = '';
            if (!empty($submit_answer)) {
                foreach ($submit_answer as $answerkey => $submit_answer_value) {

                    if (!empty($submit_answer_value)) {
                        $answerkey = $answerkey + 1;

                        if ($answerkey == 1) {
                            $option = "option_1,";
                        }
                        if ($answerkey == 2) {
                            $option = $option . "option_2,";
                        }
                        if ($answerkey == 3) {
                            $option = $option . "option_3,";
                        }
                        if ($answerkey == 4) {
                            $option = $option . "option_4,";
                        }
                        if ($answerkey == 5) {
                            $option = $option . "option_5";
                        }
                    }
                }
            }
            $option1 = rtrim($option, ',');

            $result1[$key]['question_id']    = $answerlist_value->id;
            $result1[$key]['question']       = $answerlist_value->question;
            $result1[$key]['option_1']       = $answerlist_value->option_1;
            $result1[$key]['option_2']       = $answerlist_value->option_2;
            $result1[$key]['option_3']       = $answerlist_value->option_3;
            $result1[$key]['option_4']       = $answerlist_value->option_4;
            $result1[$key]['option_5']       = $answerlist_value->option_5;
            $result1[$key]['correct_answer'] = (explode(",", $answerlist_value->correct_answer));

            if (!empty($option1)) {
                $result1[$key]['your_answer'] = (explode(",", $option1));
            } else {
                $result1[$key]['your_answer'] = '';
            }
        }
        return $result['answerlist'] = $result1;
    }

    public function addanswer($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('course_quiz_answer', $data);
        } else {
            $this->db->insert('course_quiz_answer', $data);
            return $this->db->insert_id();
        }
    }

    public function getquizanswerexistornot($questionID, $quizID, $student_id)
    {
        $this->db->select('course_quiz_answer.id')->from('course_quiz_answer');
        $this->db->where('course_quiz_answer.course_quiz_question_id', $questionID);
        $this->db->where('course_quiz_answer.course_quiz_id', $quizID);
        $this->db->where('course_quiz_answer.student_id', $student_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function add($data)
    {
        $this->db->insert('online_course_payment', $data);
        return $this->db->insert_id();
    }
    
    public function get($id = null)
    {
        $this->db->select('pickup_point.name as pickup_point_name,student_session.route_pickup_point_id,student_session.transport_fees,students.app_key,students.parent_app_key,student_session.vehroute_id,vehicle_routes.route_id,vehicle_routes.vehicle_id,transport_route.route_title,vehicles.vehicle_no,hostel_rooms.room_no,vehicles.driver_name,vehicles.driver_contact,vehicles.vehicle_model,vehicles.manufacture_year,vehicles.driver_licence,vehicles.vehicle_photo,hostel.id as `hostel_id`,hostel.hostel_name,room_types.id as `room_type_id`,room_types.room_type ,students.hostel_room_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no,students.roll_no,students.admission_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,students.mobileno, students.email ,students.state,students.city,students.pincode,students.note,students.religion,students.cast, school_houses.house_name,students.dob,students.current_address,students.previous_school,           students.guardian_is,students.parent_id,  students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email, users.username,users.password,users.id as user_id,students.dis_reason,students.dis_note,students.disable_at,IFNULL(currencies.short_name,0) as currency_name,IFNULL(currencies.symbol,0) as symbol,IFNULL(currencies.base_price,0) as base_price,IFNULL(currencies.id,0) as `currency_id`')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = students.hostel_room_id', 'left');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left');
        $this->db->join('route_pickup_point', 'route_pickup_point.id = student_session.route_pickup_point_id', 'left');
        $this->db->join('pickup_point', 'route_pickup_point.pickup_point_id = pickup_point.id', 'left');
        $this->db->join('vehicle_routes', 'vehicle_routes.id = student_session.vehroute_id', 'left');
        $this->db->join('transport_route', 'vehicle_routes.route_id = transport_route.id', 'left');
        $this->db->join('vehicles', 'vehicles.id = vehicle_routes.vehicle_id', 'left');
        $this->db->join('school_houses', 'school_houses.id = students.school_house_id', 'left');
        $this->db->join('users', 'users.user_id = students.id', 'left');
         $this->db->join('currencies', 'currencies.id=users.currency_id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('users.role', 'student');
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
    
    public function get_old($id = null)
    {
        $this->db->select('student_session.transport_fees,students.app_key,students.parent_app_key,students.vehroute_id,vehicle_routes.route_id,vehicle_routes.vehicle_id,transport_route.route_title,vehicles.vehicle_no,hostel_rooms.room_no,vehicles.driver_name,vehicles.driver_contact,hostel.id as `hostel_id`,hostel.hostel_name,room_types.id as `room_type_id`,room_types.room_type ,students.hostel_room_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast, school_houses.house_name,   students.dob ,students.current_address, students.previous_school,
            students.guardian_is,students.parent_id,            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email, users.username,users.password,students.dis_reason,students.dis_note,students.disable_at')->from('students');
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
        $this->db->join('users', 'users.user_id = students.id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('users.role', 'student');
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

    /*
    This is used to add quiz status
     */
    public function addquizstatus($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('student_quiz_status', $data);
        } else {
            $this->db->insert('student_quiz_status', $data);
            $id = $this->db->insert_id();
            return $id;
        }
    }

    /*
    This is for delete result status record when student click on reset button
     */
    public function removequizstatus($quiz_id, $studentid)
    {
        $this->db->where('course_quiz_id', $quiz_id);
        $this->db->where('student_id', $studentid);
        $this->db->delete('student_quiz_status');
    }

    /*
    This is for delete all answer when result status is 1
     */
    public function removestudentquizanswer($quiz_id, $student_id)
    {
        $this->db->where('course_quiz_id', $quiz_id);
        $this->db->where('student_id', $student_id);
        $this->db->delete('course_quiz_answer');
    }

    /*
    This is used to get single student result
     */
    public function getresult($quizID, $studentid)
    {
        $this->db->select('course_quiz_answer.answer,course_quiz_question.id,course_quiz_question.question,course_quiz_question.option_1,course_quiz_question.option_2,course_quiz_question.option_3,course_quiz_question.option_4,course_quiz_question.option_5,course_quiz_question.correct_answer')->from('course_quiz_question');
        $this->db->join('course_quiz_answer', 'course_quiz_question.id = course_quiz_answer.course_quiz_question_id', 'left');
        $this->db->where('course_quiz_question.course_quiz_id', $quizID);
        $this->db->where('course_quiz_answer.course_quiz_id', $quizID);
        $this->db->where('course_quiz_answer.student_id', $studentid);
        $this->db->group_by('course_quiz_question.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
    This is used to getting course id based on section id
     */
    public function coursebysection($section_id)
    {
        $this->db->select('online_courses.id');
        $this->db->join('online_courses', 'online_courses.id = online_course_section.online_course_id');
        $this->db->from('online_course_section');
        $this->db->where('online_course_section.id', $section_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * This function is used to get course progress
     */
    public function getcourseprogress($courseid, $student_id, $section_id, $lesson_quiz_type, $lesson_quiz_id)
    {
        $result = $this->db->select("id")
            ->where(array("course_id" => $courseid, "student_id" => $student_id, "course_section_id" => $section_id, "lesson_quiz_type" => $lesson_quiz_type, "lesson_quiz_id" => $lesson_quiz_id))
            ->get("course_progress")
            ->result_array();
        return $result;
    }

    /**
     * This function is used to mark/unmark lesson complete
     */
    public function markascomplete($lesson_data, $mark)
    {
        if ($mark == 0) {
            $this->db->where(array(
                "course_id"         => $lesson_data["course_id"],
                "course_section_id" => $lesson_data["course_section_id"],
                "student_id"        => $lesson_data["student_id"],
                "lesson_quiz_type"  => $lesson_data["lesson_quiz_type"],
                "lesson_quiz_id"    => $lesson_data["lesson_quiz_id"]));
            $this->db->delete("course_progress");
        } elseif ($mark == 1) {
            $this->db->insert("course_progress", $lesson_data);
        }
    }

    public function courseperformance($courseID, $student_id)
    {
        $result = $this->db->select('student_quiz_status.*,online_course_quiz.id,online_course_quiz.quiz_title')->from('student_quiz_status');
        $this->db->join('online_course_quiz', 'online_course_quiz.id = student_quiz_status.course_quiz_id');
        $this->db->join('online_course_section', 'online_course_section.id = online_course_quiz.course_section_id');
        $this->db->where('online_course_section.online_course_id', $courseID);
        $this->db->where('student_quiz_status.student_id', $student_id);
        $this->db->where('student_quiz_status.status', 1);
        $this->db->order_by('online_course_quiz.id', 'asc');
        $this->db->group_by('online_course_quiz.id');
        $query  = $this->db->get();
        $result = $query->result();
        foreach ($result as $key => $result_value) {
            if ($result_value->total_question > 0) {
                $result[$key]->percentage = ($result_value->correct_answer / $result_value->total_question) * 100;
            } else {
                $result[$key]->percentage = '';
            }
        }
        return $result;
    }

    public function lessoncompleted($course_id, $student_id, $lesson_quiz_type)
    {
        $this->db->select('course_progress.*')->from('course_progress');
        $this->db->where('course_progress.course_id', $course_id);
        $this->db->where('course_progress.student_id', $student_id);
        $this->db->where('course_progress.lesson_quiz_type', $lesson_quiz_type);
        $query = $this->db->get();
        return $query->result_array();
    }

    /* This is used to get class section list by course id
     */
    public function sectionbycourse($courseid)
    {
        $this->db->select('class_section_id')->from('online_course_class_sections');
        $this->db->where('course_id', $courseid);
        $query = $this->db->get();

        $result = $query->result_array();
$results=array();
        foreach ($result as $result_value) {
            $results[] = $result_value['class_section_id'];
        }
        return $results;
    }

    /*
    This function is used to get s3 bucket settings
     */
    public function getAwsS3Settings()
    {
        $this->db->select('*');
        $this->db->from('aws_s3_settings');
        $this->db->order_by('aws_s3_settings.id');
        $query = $this->db->get();
        return $query->row();
    }

    public function addprocessingpayment($data)
    {
        $this->db->insert('online_course_processing_payment', $data);
        return $this->db->insert_id();
    }
    
    
    public function getcourserating($courseid){     

        $this->db->select("course_rating.*,guest.guest_name as rating_provider_name,'null' as middlename, 'null' as lastname,guest_image as image, guest.guest_unique_id as admission_no, guest.gender as gender");
        $this->db->from("course_rating");
        $this->db->join('guest','guest.id=course_rating.guest_id');
        $this->db->where('course_rating.course_id',$courseid);
        $query1 = $this->db->get_compiled_select(); // It resets the query just like a get()
        $this->db->select("course_rating.*,students.firstname as rating_provider_name,students.middlename as middlename,students.lastname as lastname,students.image as image, students.admission_no as admission_no, students.gender as gender");
        $this->db->from("course_rating");
         $this->db->join('students','students.id=course_rating.student_id');
        $this->db->where('course_rating.course_id',$courseid);
        $query2 = $this->db->get_compiled_select();
        
        $query = $this->db->query($query1 . " UNION " . $query2);
        
        return $query->result_array();
    }
    
    public function getcourseratingbystudentid($courseid,$student_id){     

       
        $this->db->select("course_rating.*,students.firstname as rating_provider_name,students.middlename as middlename,students.lastname as lastname,students.image as image, students.admission_no as admission_no, students.gender as gender");
        $this->db->from("course_rating");
         $this->db->join('students','students.id=course_rating.student_id');
        $this->db->where('course_rating.course_id',$courseid);
        $this->db->where('course_rating.student_id',$student_id);  
        
         $query = $this->db->get();
        return $query->result();
        
        
    }
    
    // public function addanswer($data)
    // {
    //     if (isset($data['id'])) {
    //         $this->db->where('id', $data['id']);
    //         $this->db->update('course_quiz_answer', $data);
    //     } else {
    //         $this->db->insert('course_quiz_answer', $data);
    //         return $this->db->insert_id();
    //     }
    // }
    
    public function addCourseRatingandReview($data)
    {
        if (!empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('course_rating', $data);
        } else {
            $this->db->insert('course_rating', $data);
            return $this->db->insert_id();
        }
    } 
    
    
    public function getSectionNameByCourseId($id) {
        $this->db->select('sections.*');
        $this->db->from('online_course_class_sections');
        $this->db->where('online_course_class_sections.course_id',$id);
        $this->db->join('class_sections','class_sections.id=online_course_class_sections.class_section_id');
        $this->db->join('sections','sections.id=class_sections.section_id');      
      
        $query = $this->db->get();
        return $query->result();
    }
    
}
