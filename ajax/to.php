<?php
require '../config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;
require CL_SESSION_PATH;
require ISLOGIN;
$error_message = "";

set_time_limit(0);
session_write_close();
function military_time($data)
{
    $military_time = preg_match('/^([01][0-9]|2[0-3]):([0-5][0-9])$/', $data);
    if (!$military_time) {
        return false;
    }
    return true;
}



$is_digit = '/^[0-9]+$/';
if (isset($_POST['data_sched']) && isset($_POST['room_data']) && isset($_POST['teach_load_data']) && isset($_POST['subject_data'])) {
    //array
    $array_availability = [];
    $array_day = [];
    $array_room = [];
    $array_subject = [];
    $array_teaching = [];
    $array_section = [];
    $time_table = [];
    $title = $_POST['title'];


    $pt_availability = [];
    $ft_availability = [];

    $sql = mysqli_query($db_connect, "SELECT * FROM permanent_sched_summary WHERE title = '$title'");
    if (mysqli_num_rows($sql) > 0) {
        $error_message = "Title is already exists";
        $res = [
            'status' => 400,
            'message' => $error_message
        ];
        echo json_encode($res);
        return;
    }

    // do some validation in schedule
    $data_sched = json_decode($_POST['data_sched'], true);
    $teach_load_data = json_decode($_POST['teach_load_data'], true);
    $subject_data = json_decode($_POST['subject_data'], true);
    $room_data = json_decode($_POST['room_data'], true);

    if (JSON_ERROR_NONE !== json_last_error()) {
        $error_message = "Invalid Json";
    }
    if (empty($data_sched)) {
        $error_message = "No data found in data schedule";
    }
    if (empty($teach_load_data)) {
        $error_message = "No data found in teaching load";
    }
    if (empty($subject_data)) {
        $error_message = "No data found in Subject";
    }
    if (empty($room_data)) {
        $error_message = "No data found in room";
    }
    $day = array('M', 'T', 'W', 'TH', 'F', 'S', 'SU');
    $status = array("FULL TIME", "PART TIME");
    $array_time = array();



    if ($error_message != '') {
        $res = [
            'status' => 400,
            'message' => $error_message
        ];
        echo json_encode($res);
        return;
    }

    // do some validation in room
    foreach ($room_data['room_data'] as $key => &$value) {
        if ($value['room'] == "" || $value == "" || $value == "") {
            $error_message = "Missing data in room";
        }
        if (!preg_match($is_digit, $value["capacity"])) {
            $error_message = "Not Numeric in room - " . $value["room"];
            break;
        }
        if (!in_array(strtoupper($value['type']), ROOM_TYPE)) {
            $error_message = "Room type is invalid -" . $value['room'];
        }

        if ($error_message != '') {
            $res = [
                'status' => 400,
                'message' => $error_message
            ];
            echo json_encode($res);
            return;
        }

        if ($value['type'] != 'ONLINE') {
            $value['type'] = strtoupper($value['type']);
            $array_room[$value['type']][$value['room']] = $value;
        }
    }

    // echo json_encode($time_table);
    // exit;

    function sortByOrder($a, $b)
    {
        if ($a['day_length'] > $b['day_length']) {
            return 1;
        } else if ($a['day_length'] < $b['day_length']) {
            return -1;
        }
        return 0;
    }


    //Validation for Data sched
    foreach ($data_sched['data_sched'] as $key => &$value) {
        $value['day'] = explode(",", $value['day']);
        $day_length =  count($value['day']);


        $last_day_availability = end($value['day']);

        if ($value['faculty_id'] ==  "" || $value['name'] == "" || $value['day'] == "" || $value['from_time'] == "" || $value['to_time'] == "" || $value['status'] == "") {
            $error_message = "Missing Data in Teacher Sched";
            break;
        }
        // $new_day_array = [$day_array];
        if (in_array("", $value['day'])) {
            $error_message = "Empty day in -" . $value['faculty_id'];
            break;
        }
        if (!array_intersect($day, $value['day'])) {
            $error_message = "Day is not Valid in -" . $value['faculty_id'];
            break;
        }
        $from_time = military_time($value['from_time']);

        if (!$from_time) {
            $error_message = "From time is not Valid in- " . $value['faculty_id'];
            break;
        }
        $to_time = military_time($value['to_time']);
        if (!$to_time) {
            $error_message = "to time is not Valid in- " . $value['faculty_id'];
            break;
        }
        if ($value['from_time'] >= $value['to_time']) {
            $error_message = "From time is greater than To time - " . $value['faculty_id'];
            break;
        }
        if (!in_array(strtoupper($value['status']), $status)) {
            $error_message = "status is invalid in faculty -" . $value['faculty_id'];
            break;
        }

        if ($error_message != '') {
            $res = [
                'status' => 400,
                'message' => $error_message
            ];
            echo json_encode($res);
            return;
        }

        $faculty_id_ = $value['faculty_id'];
        $faculty_id = sha1($value['faculty_id']);
        $stats = strtoupper($value['status']);

        $total_hours = (strtotime($value['to_time']) - strtotime($value['from_time'])) / 3600;

        // $array_teaching[$faculty_id] = array('faculty_id' => $value['faculty_id'], 'name' => $value['name'], 'status' => $value['status']);
        $availability = array('faculty_id' => $value['faculty_id'], 'name' => $value['name'], 'status' => $value['status'], "fromtime" => $value['from_time'] . ":00", "totime" => $value['to_time'] . ":00", "teach_no" => 0, "break_time" => 0, "day_length" => $day_length, "total_hrs" => $total_hours, "last_day" => $last_day_availability);


        // $days = array();
        foreach ($value['day'] as $value) {
            if ($stats === 'PART TIME') {
                $pt_availability[$value][] = $availability;
                usort($pt_availability[$value], 'sortByOrder');
            } else {
                $ft_availability[$value][] = $availability;
                usort($ft_availability[$value], 'sortByOrder');
            }
        }
    }


    // echo json_encode($pt_availability);
    // exit;

    // do some validation in subject
    foreach ($subject_data['subject_data'] as $key => &$value) {
        if ($value['subject'] == "" || $value['room'] == "" || $value['unit'] == "" || $value['hr_wk'] == "" || $value['day_wk'] == "" || $value['course_title'] == "") {
            $error_message = "Missing data in Subject";
            break;
        }
        if (!in_array(strtoupper($value['room']), ROOM_TYPE)) {
            $error_message = "Room type is invalid in subject-" . $value['room'];
        }
        if (!preg_match($is_digit, $value["unit"])) {
            $error_message = "Unit Not Numeric in room - " . $value["subject"];
            break;
        }
        if (!preg_match($is_digit, $value["hr_wk"])) {
            $error_message = "Hour/Week Not Numeric in room - " . $value["subject"];
            break;
        }
        if (!preg_match($is_digit, $value["day_wk"])) {
            $error_message = "Day/Week Not Numeric in room - " . $value["subject"];

            break;
        }
        if ($error_message != '') {
            $res = [
                'status' => 400,
                'message' => $error_message
            ];
            echo json_encode($res);
            return;
        }

        $value['room'] = strtoupper($value['room']);
        $value['subject'] = strtoupper($value['subject']);
        $total_hours =  $value['hr_wk'] / $value['day_wk'] * 60;
        $array_subject[$value['subject']][] = array('subject' => $value['subject'], 'course_title' => $value['course_title'], 'room_type' => $value['room'], 'unit' => $value['unit'], 'hr_wk' => $value['hr_wk'], 'day_wk' => $value['day_wk'], 'total_hours' => $total_hours);
    }

    // echo json_encode($array_subject);
    // exit;

    // do some validation in teach load
    foreach ($teach_load_data['teach_load_data'] as $key => &$value) {
        if ($value['faculty_id'] == "" || $value['subject'] == "" || $value['section'] == "" || $value['no_of_students'] == "") {
            $error_message = "Missing data in Teacher Load";
        }
        if (!preg_match($is_digit, $value["no_of_students"])) {
            $error_message = "Not Numeric no. of students -" . $value['faculty_id'];
            break;
        }

        if ($error_message != '') {
            $res = [
                'status' => 400,
                'message' => $error_message
            ];
            echo json_encode($res);
            return;
        }

        $faculty_id = sha1($value['faculty_id']);
        $value['subject'] = strtoupper($value['subject']);

        if (isset($array_subject[$value['subject']])) {
            foreach ($array_subject[$value['subject']] as $subj) {
                $array_teaching[$faculty_id]['teaching_load'][] = array_merge($subj, array('subject' => $value['subject'], 'name' => $value['name'], 'section' => $value['section'], 'no_of_students' => $value['no_of_students']));
            }
        } else {
            $error_message = "Invalid course code in faculty id -" . $value['faculty_id'] . " in Teacher Load";
            $res = [
                'status' => 400,
                'message' => $error_message
            ];
            echo json_encode($res);
            return;
        }
    }

    // echo json_encode($pt_availability);
    // exit;

    //validation for available rooms
    $array_room_type = [];
    foreach ($array_room as $room_type_key => $room_type_value) {
        array_push($array_room_type, $room_type_key);
    }

    // echo json_encode($array_room);
    // exit;
    // array_multisort(array_map(function ($element) {
    //     return $element['status'];
    // }, $array_teaching), SORT_DESC, $array_teaching);

    function islastday($key_day, $value_day, $schedule_to, $last_day_availability, $faculty_id, $name, $user_id)
    {
        global $db_connect;

        if (strtotime($schedule_to) > strtotime($value_day . ":00") && $last_day_availability == $key_day) {
            $error_message = "Insufficient time in faculty no. " . $faculty_id . " -" . $name . "</br> NOTE: you need to add teacher time availability.";
            //call function -- delete db yung $faculty_id .. $load ..
            //collect_error --

            $droptable = "DELETE FROM `temporary_sched` WHERE `user_id` = '$user_id'";
            $droptable_run = mysqli_query($db_connect, $droptable);

            if (!$droptable_run) {
                $res = [
                    'status' => 400,
                    'message' => 'Temporary table not deleted!'
                ];
                echo json_encode($res);
                exit();
            }

            $res = [
                'status' => 400,
                'message' => $error_message
            ];
            echo json_encode($res);
            exit();
        }

        return false;
    }

    function isEndDay($totime, $schedule, $schedule_to)
    {
        if (strtotime($totime . ":00") == strtotime($schedule) || strtotime($totime . ":00") < strtotime($schedule_to) ||  strtotime($schedule_to) <= strtotime($schedule)) {
            return true;
        }
        return false;
    }


    //generate schedule for faculty status part time

    foreach ($pt_availability as $pt_key => $pt_value) {
        //$pt_key == M - S

        $day = $pt_key;

       
       

        foreach ($pt_value as $a_key => &$a_value) {

            $faculty_id = sha1($a_value['faculty_id']);
            $break_time = $a_value['break_time'];
            $day_length = $a_value['day_length'];
            $name = $a_value['name'];
            $status = $a_value['status'];
            $teach_no = $a_value['teach_no'];
            $total_hrs = $a_value['total_hrs'];
            $fromtime = $a_value['fromtime'];
            $totime = $a_value['totime'];
            $last_day = $a_value['last_day'];




            // print_r($array_teaching[$faculty_id]['teaching_load']);
            // exit;

            //faculty teaching load 
            // if (count($array_teaching[$faculty_id]['teaching_load']) === 0) {
            //     // list is empty.
            //     break; //next loop
            // }

            // echo json_encode($array_teaching[$faculty_id]['teaching_load']);
            // exit;
            $schedule = $fromtime;
        //    var_dump($array_teaching[$faculty_id]['teaching_load']) ;
    
                    if(empty($array_teaching[$faculty_id]['teaching_load'])){
                        continue;
                    }
             

            foreach ($array_teaching[$faculty_id]['teaching_load'] as $key => &$value) {

                $room_type = $value['room_type'];
                $section = $value['section'];
                $subject = $value['subject'];
                $course_title = $value['course_title'];
                $unit = $value['unit'];
                $name = $value['name'];
                // echo $a_value['faculty_id'];
                
                $schedule_to = Schedule($fromtime, $value['total_hours']);

                //checking for overlapping in section
                $sec_lap = "SELECT user_id FROM temporary_sched WHERE user_id = '" . $user_id . "' AND day = '" . $day . "' AND section = '" . $section . "' 
                  AND (( '" . $schedule . "' > schedule && '" . $schedule_to . "' <  schedule_to ) 
                  OR (('" . $schedule . "' > schedule && '" . $schedule . "' < schedule_to) || ('" . $schedule_to . "' > schedule && '" . $schedule_to . "' < schedule_to)) 
                  OR ( '" . $schedule . "' = schedule || '" . $schedule_to . "' = schedule_to) 
                  OR (schedule > '" . $schedule . "' && schedule_to < '" . $schedule_to . "')) ";

                $section_overlap = mysqli_query($db_connect, $sec_lap);
                if (mysqli_num_rows($section_overlap) > 0) { //if section have overlap
                    //check if kung last day na.
                    if ($day == $last_day) {
                        //insert array for manual schedule
                        unset($array_teaching[$faculty_id]['teaching_load'][$key]);
                    }
                    break;
                } else { //dont have overlap

                    //check for overlapping in room
                    foreach ($array_room[$room_type] as $room_key => $room_value) {


                        end($array_room[$room_type]);
                        $last_room = key($array_room[$room_type]); // last room of type


                        $sec_lap = "SELECT user_id FROM temporary_sched WHERE user_id = '" . $user_id . "' AND day = '" . $day . "' AND room = '" . $room_key . "' 
                  AND (( '" . $schedule . "' > schedule && '" . $schedule_to . "' <  schedule_to ) 
                  OR (('" . $schedule . "' > schedule && '" . $schedule . "' < schedule_to) || ('" . $schedule_to . "' > schedule && '" . $schedule_to . "' < schedule_to)) 
                  OR ( '" . $schedule . "' = schedule || '" . $schedule_to . "' = schedule_to) 
                  OR (schedule > '" . $schedule . "' && schedule_to < '" . $schedule_to . "')) ";
                        $section_overlap = mysqli_query($db_connect, $sec_lap);
                        if (mysqli_num_rows($section_overlap) > 0) { //if room have overlap next room

                            if ($last_room == $room_key) { //if last room add in manual scched
                                unset($array_teaching[$faculty_id]['teaching_load'][$key]);
                                //insert array for manual schedule
                                break;
                            }

                            continue;
                        } else {
                            $hash = sha1($room_type . $room_key . $day . $from_time . $schedule_to . $faculty_id . $section . $subject . $course_title . $unit . $name . $schedule . $schedule_to . $user_id);
                            $query_insert = "INSERT INTO `temporary_sched`(`room_type`,`room`,`day`, `from_time`, `to_time`, `faculty_id`, `section`, `subject`,`course_title`,`unit`,`name`, `schedule`,`schedule_to`,`user_id`,`total_hrs`) VALUES ('$room_type','$room_key','$day','" . $fromtime . "','" . $totime . "','$faculty_id','$section','$subject','$course_title','$unit','$name','$schedule','$schedule_to','$user_id','" . $total_hrs . "')";
                            $query_insert_run = mysqli_query($db_connect, $query_insert);

                            if ($query_insert_run) {
                                //delete teaching load of faculty.
                                unset($array_teaching[$faculty_id]['teaching_load'][$key]);
                                $a_value['fromtime'] = $schedule_to;
                                $schedule= $schedule_to;
                            }
                        }
                    }
                }
            }
        }
    }


    $insert_permanent_sched = "INSERT INTO `permanent_sched_summary`(`title`, `created_by`) VALUES ('$title','$user_id')";
    $insert_permanent_sched_run = mysqli_query($db_connect, $insert_permanent_sched);
    $last_id = mysqli_insert_id($db_connect);

    // $session_class->setValue('session_sched',  $last_id);
    // $session_class->setValue('session_title', $title);

    if (!$insert_permanent_sched_run) {
        $res = [
            'status' => 400,
            'message' => 'Something went wrong!'
        ];
        echo json_encode($res);
        return;
    }
    else{
        $res = [
            'status' => 200,
            'message' => 'Success!'
        ];
        echo json_encode($res);
        return;
    }

    $select_temporary_sched = "SELECT * FROM `temporary_sched` WHERE `user_id` = '$user_id'";
    $select_temporary_sched_run = mysqli_query($db_connect, $select_temporary_sched);
    while ($row = mysqli_fetch_assoc($select_temporary_sched_run)) {

        $room_type = $row['room_type'];
        $room = $row['room'];
        $day = $row['day'];
        $schedule_from = $row['schedule'];
        $schedule_to = $row['schedule_to'];
        $faculty_id = $row['faculty_id'];
        $section = $row['section'];
        $subject = $row['subject'];
        $course_title = $row['course_title'];
        $unit = $row['unit'];
        $name = $row['name'];
        $user_id = $row['user_id'];
        $total_hrs = $row['total_hrs'];

        $query = "INSERT INTO `permanent_sched_detail`(`parent_id`, `room_type`, `room`, `day`, `schedule_from`, `schedule_to`, `faculty_id`,`course_title`, `section`, `subject`,`unit`,`name`, `user_id`,`total_hrs`) VALUES ('$last_id','$room_type','$room','$day','$schedule_from','$schedule_to','$faculty_id','$course_title','$section','$subject','$unit','$name','$user_id','$total_hrs')";
        $query_run = mysqli_query($db_connect, $query);

        if (!$query_run) {
            $res = [
                'status' => 400,
                'message' => 'unknown error occured!'
            ];
            echo json_encode($res);
            return;
            exit;
            break;
        }
    }

    $droptable = "DELETE FROM `temporary_sched` WHERE `user_id` = '$user_id'";
    $droptable_run = mysqli_query($db_connect, $droptable);

    if (!$droptable_run) {
        $res = [
            'status' => 400,
            'message' => 'Temporary table not deleted!'
        ];
        echo json_encode($res);
        return;
    }

    $res = [
        'status' => 200,
        'message' => 'Schedule Generated!'
    ];
    echo json_encode($res);
    return;
    exit;

    // echo json_encode(array_values($array_teaching));

}
