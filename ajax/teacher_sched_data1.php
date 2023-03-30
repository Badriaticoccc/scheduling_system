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

    $title = $_POST['title'];

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

    // echo json_encode($teach_load_data);
    // exit;

    if ($error_message != '') {
        $res = [
            'status' => 400,
            'message' => $error_message
        ];
        echo json_encode($res);
        return;
    }

    //Validation for Data sched
    foreach ($data_sched['data_sched'] as $key => &$value) {
        $value['day'] = explode(",", $value['day']);

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

        $faculty_id = sha1($value['faculty_id']);
        $array_teaching[$faculty_id] = array('faculty_id' => $value['faculty_id'], 'name' => $value['name'], 'status' => $value['status']);
        $availability = array("fromtime" => $value['from_time'], "totime" => $value['to_time'], "teach_no" => 0, "break_time" => 0);

        // $days = array();
        foreach ($value['day'] as $value) {
            if (isset($array_time[$faculty_id][$value])) {
                $array_time[$faculty_id][$value] = $availability;
            } else {
                $array_time[$faculty_id][$value] = $availability;
            }
        }
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

        $value['type'] = strtoupper($value['type']);
        $array_room[$value['type']][] = $value;
    }

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
        if (isset($array_teaching[$faculty_id]))
            if (isset($array_subject[$value['subject']])) {
                foreach ($array_subject[$value['subject']] as $subj) {
                    $array_teaching[$faculty_id]['teaching_load'][] = array_merge($subj, array('subject' => $value['subject'], 'section' => $value['section'], 'no_of_students' => $value['no_of_students']));
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

    //validation for available rooms
    $array_room_type = [];
    foreach ($array_room as $room_type_key => $room_type_value) {
        array_push($array_room_type, $room_type_key);
    }

    foreach ($array_teaching as $key => $value) {
        if (isset($array_time[$key])) {
            $array_teaching[$key]['Day'] = $array_time[$key];
        }
    }
    $schedule_array = [];

    // echo json_encode($array_room['LECTURE']);
    // exit;

    //validation for availability of faculty
    foreach ($array_teaching as $key => $value) {
        $faculty_id = $value['faculty_id'];

        $index = $key;
        $sum = 0;
        $sum_availability = 0;

        if (isset($array_teaching[$index]['teaching_load'])) {
            foreach ($array_teaching[$index]['teaching_load'] as $key_load => $value_load) {
                //get total hours per week
                $hr_wk = $value_load['hr_wk'];
                // $sum = $sum + $hr_wk;

                //validation for room type if existing room
                if (!in_array($value_load['room_type'], $array_room_type) and $value_load['room_type'] != 'ONLINE') {
                    $error_message = "Room type (" . $value_load['room_type'] . ") not found  in room table";

                    $res = [
                        'status' => 400,
                        'message' => $error_message
                    ];
                    echo json_encode($res);
                    return;
                }
            }

            foreach ($array_teaching[$index]['Day'] as $key_day => &$value_day) {
                //get total hours per day
                $availability_from_time = $value_day['fromtime'];
                $availability_to_time = $value_day['totime'];

                // get total hours per day times number of teaching on that day
                // ex. 3 hours per day pero may 2 section kang tuturuan
                // 3*2 = 6, then compare
                $sum_day = (strtotime($availability_to_time) - strtotime($availability_from_time)) / 3600;
                $sum_availability = $sum_availability + $sum_day;
                if ($sum_day >= 6) {

                    // $sum = $sum + 1;
                }
            }

            // $sum = $sum + 4;
            if ($sum > $sum_availability) {
                $error_message = "Insufficient time in faculty no. " . $faculty_id . " </br> NOTE: you need to add teacher time availability.";

                $res = [
                    'status' => 400,
                    'message' => $error_message
                ];
                echo json_encode($res);
                return;
            }
        }
    }



    array_multisort(array_map(function ($element) {
        return $element['status'];
    }, $array_teaching), SORT_DESC, $array_teaching);

    array_multisort(array_map(function ($element) {
        return $element['room'];
    }, $array_room['LECTURE']), SORT_ASC, $array_room['LECTURE']);

    function islastday($key_day, $value_day, $schedule_to, $last_day_availability,$faculty_id,$name,$user_id){
        global $db_connect;

        if (strtotime($schedule_to) > strtotime($value_day. ":00") && $last_day_availability == $key_day) {
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

    function isEndDay($totime,$schedule,$schedule_to){
        if (strtotime($totime . ":00") == strtotime($schedule) || strtotime($totime. ":00") <= strtotime($schedule_to) ||  strtotime($schedule_to) <= strtotime($schedule)) {
            return true;
        }
        return false;
    }

    foreach ($array_teaching as $key => $value) {


        $faculty_id = $value['faculty_id'];
        $day = $value['Day']; //array
        $name = $value['name'];
        $status = $value['status'];
        $index = $key;


        if (isset($array_teaching[$index]['teaching_load'])) {
            foreach ($array_teaching[$index]['teaching_load'] as $key_load => $value_load) {


                //error_log("faculty id: " . $faculty_id . " " . json_encode($value_load));

                $day_wk =  $value_load['day_wk'];
                $total_hours =  $value_load['total_hours'];
                $room_type = $value_load['room_type'];
                $section = $value_load['section'];
                $subject = $value_load['subject'];
                $course_title =  $value_load['course_title'];
                $unit = $value_load['unit'];

                for ($count = 0; $count < $day_wk; $count++) {
                    // error_log('day');


                    //RANDOM DAY
                    foreach ($array_teaching[$index]['Day'] as $key_day => &$value_day) {
                        //error_log("teaching_load faculty id: " . $faculty_id . " " . json_encode($value_day));

                        //lastday of availability
                        //end($array_teaching[$index]['Day']);
                        $last_day_availability = array_key_last($array_teaching[$index]['Day']);
                        $run = false;

                        //to get total hours of faculty
                        $from_time = strtotime($value_day['fromtime']);
                        $to_time = strtotime($value_day['totime']);


                        //total hours of faculty
                        $diff_minutes = round(abs($from_time - $to_time) / 60);

                        // function to give time
                        $schedule = Schedule($value_day['fromtime'], 0);
                        $schedule_to = Schedule($value_day['fromtime'], $total_hours);

                        //get hours eg. 08:00 to 09:30 equal 1.5
                        $get_hours = (strtotime($schedule_to) - strtotime($value_day['fromtime'])) / 3600;

                        //overlapping of schedule per day

                        if (strtotime($value_day['fromtime']) >= strtotime($value_day['totime'] . ":00") || strtotime($value_day['totime'] . ":00") < strtotime($schedule_to)) {
                            if (strtotime($schedule_to) > strtotime($value_day['totime'] . ":00") && $last_day_availability == $key_day) {
                                $error_message = "Insufficient time in faculty no. " . $faculty_id . " -" . $name . "</br> NOTE: you need to add teacher time availability.";

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
                                    'status' => 400,
                                    'message' => $error_message
                                ];
                                echo json_encode($res);
                                return;
                            }
                            continue; //next loop
                        }

                        if ($value_day['teach_no'] >= 3 and $value_day['break_time'] == 0) {
                     
                            $schedule_to = Schedule($value_day['fromtime'], 60);
                            $get_hours = (strtotime($schedule_to) - strtotime($value_day['fromtime'])) / 3600;
                            // insert Break Time
                            $break = "BREAK TIME";

                            $temp_from_time = $value_day['fromtime'];
                            $query = "INSERT INTO `temporary_sched`(`room_type`,`room`,`day`, `from_time`, `to_time`, `faculty_id`, `section`, `subject`,`course_title`,`unit`,`name`, `schedule`,`schedule_to`,`user_id`) VALUES ('$break','$break','$key_day','$from_time','$to_time','$faculty_id','$break','$break','$break','0','$name','$temp_from_time','$schedule_to','$user_id')";
                            $query_run = mysqli_query($db_connect, $query);

                            if ($query_run) {
                                $value_day['fromtime'] = $schedule_to;
                                $value_day['teach_no'] = $value_day['teach_no'] + $get_hours;
                                $value_day['break_time'] = 1;
                            }
                        }

                        if ($value_day['teach_no'] >= 8 and $value_day['break_time'] == 1) {
                       
                            $schedule_to = Schedule($value_day['fromtime'], 30);
                            $get_hours = (strtotime($schedule_to) - strtotime($value_day['fromtime'])) / 3600;

                            // insert Break Time
                            $break = "BREAK TIME";
                            $query = "INSERT INTO `temporary_sched`(`room_type`,`room`,`day`, `from_time`, `to_time`, `faculty_id`, `section`, `subject`,`course_title`,`unit`,`name`, `schedule`,`schedule_to`,`user_id`) VALUES ('$break','$break','$key_day','$from_time','$to_time','$faculty_id','$break','$break','$break','0','$name','$temp_from_time','$schedule_to','$user_id')";
                            $query_run = mysqli_query($db_connect, $query);

                            if ($query_run) {
                                $value_day['fromtime'] = $schedule_to;
                                $value_day['teach_no'] = $value_day['teach_no'] + $get_hours;
                                $value_day['break_time'] = 2;
                            }
                        }

                        $availability_from_time = $value_day['fromtime'];
                        $schedule = Schedule($availability_from_time, 0);
                        $schedule_to = Schedule($availability_from_time, $total_hours);

                        //for day week
                        if ($count > 0) {
                            $select_last_inserted_room = mysqli_query($db_connect, "SELECT `day` FROM `temporary_sched` WHERE  user_id = '" . $user_id . "' AND day = '" . $key_day . "'  AND subject = '" . $subject . "' AND section = '" . $section . "' ORDER BY day DESC");
                            //error_log("SELECT `day` FROM `temporary_sched` WHERE  user_id = '" . $user_id . "' AND day = '" . $key_day . "'  AND subject = '" . $subject . "' AND section = '" . $section . "' ORDER BY day DESC");
                            if (mysqli_num_rows($select_last_inserted_room) > 0) {
                                continue;
                            }
                        }

                        //check section if has already more than -- give break time

                        $stop_loop = false;
                        $count_loop = 0;
                        $overlapping = true;
                        $rm_type = strtoupper($room_type);
                        // shuffle($array_room[$rm_type]);
                        while ($overlapping === true) {

                            $tmp_schedule = $schedule;
                            $tmp_schedule_to = $schedule_to;

                            // Check kung may mag overlapping sa section schedule. 
                            $select_dup = "SELECT user_id FROM temporary_sched WHERE user_id = '" . $user_id . "' AND day = '" . $key_day . "' AND section = '" . $section . "' 
							AND (( '" . $schedule . "' > schedule && '" . $schedule_to . "' <  schedule_to ) 
							OR (('" . $schedule . "' > schedule && '" . $schedule . "' < schedule_to) || ('" . $schedule_to . "' > schedule && '" . $schedule_to . "' < schedule_to)) 
							OR ( '" . $schedule . "' = schedule || '" . $schedule_to . "' = schedule_to) 
							OR (schedule > '" . $schedule . "' && schedule_to < '" . $schedule_to . "')) ";

                            $orig_from = '';
                            $orig_to = '';

                            $select_dup_run = mysqli_query($db_connect, $select_dup);
                            //error_log('count_loop' . $sel_sched_sect." -- ".$select_dup);
                            if (mysqli_num_rows($select_dup_run) > 0) { //if have overlap

                                $sel_sched_sect = "SELECT schedule_to FROM temporary_sched WHERE user_id = '" . $user_id . "' AND day = '" . $key_day . "' AND section = '" . $section . "' ORDER BY schedule DESC LIMIT 1"; 
                                $sel_sched_sect_query = mysqli_query($db_connect, $sel_sched_sect);
                                if (mysqli_num_rows($sel_sched_sect_query) > 0) {
                                    $schedule_db = mysqli_fetch_assoc($sel_sched_sect_query);
                                    $schedule = Schedule($schedule_db['schedule_to'], 0);
                                    $schedule_to = Schedule($schedule, $total_hours);

                                    $orig_from = $schedule;
                                    $orig_to = $schedule_to;
                                }

                                //add 03/13
                                //$log_out = ($schedule_to <= $schedule) ? 'true' : 'false';
                                // error_log('count_loop' . $faculty_id . "--" . $section . "--" . $subject . "--" . $total_hours."--".$schedule." >= ".$schedule_to.">>".$log_out ."<<".$key_day);


                                $schedule_to_valid = Schedule_datetime($schedule_to, $total_hours);
                                if (strtotime($value_day['totime']) < $schedule_to_valid) { //checking if valid schedule to
                                    $stop_loop = true;
                                    $overlapping = false;
                                    break;
                                }
                                //end 03/13

                                // $log_out = ($schedule_to <= $schedule) ? 'true' : 'false';
                                // error_log('count_loop' . $faculty_id . "--" . $section . "--" . $subject . "--" . $total_hours."--".$schedule." >= ".$schedule_to.">>".$log_out ."<<".$key_day);

                                //error_log('count_loop' . $schedule_db['schedule_to'] . "--" . $schedule . "--" . $schedule_to . "--" . $total_hours."==".$key_day);
                                //add 03/13
                                if(isEndDay($value_day['totime'], $schedule, $schedule_to)){
                                    $stop_loop = true;
                                    $overlapping = false;
                                    break;
                                }
                                //end 03/13


                                islastday($key_day, $value_day['totime'], $schedule_to, $last_day_availability,$faculty_id,$name,$user_id);

                                //$schedule_valid = strtotime($schedule_to);
                                //add 03/13
                                $tmp_schedule = $schedule;
                                $tmp_schedule_to = $schedule_to;

                                $schedule = $schedule_to;
                                $schedule_to_valid = Schedule_datetime($schedule_to, $total_hours);
                                $schedule_to = Schedule($schedule_to, $total_hours);
                                if (strtotime($value_day['totime']) < $schedule_to_valid) {
                                    $stop_loop = true;
                                    $overlapping = false;
                                    break;
                                }
                                //end 03/13


                            } else {

                                //error_log('count_loop' .$value_day['totime'] . ":00==".$schedule ."--". $value_day['totime'].":00" ."--". $schedule_to ."--".  $schedule_to ."--". $schedule);
                                //overlapping of schedule per day
                                //add 03/13
                                $tmp_schedule = $schedule;
                                $tmp_schedule_to = $schedule_to;

                                if(isEndDay($value_day['totime'], $schedule, $schedule_to)){
                                    $stop_loop = true;
                                    $overlapping = false;
                                    break;
                                }
                                //end 03/13


                                // error_log('count_loop' . $faculty_id . "--" . $section . "--" . $subject . "--" . $total_hours."++".$room_type);
                                islastday($key_day, $value_day['totime'], $schedule_to, $last_day_availability,$faculty_id,$name,$user_id);
                                $overlapping = false;
                            }



                           $room_value_capacity ="";
                           $room_value_room ="";
                            $no_room = true;
                            foreach ($array_room[$rm_type] as $room_key => $room_value) {
                                $tmp_sched = $tmp_schedule;
                                $tmp_sched_to = $tmp_schedule_to;
    
                                $room_value_type = $room_value['type'];
                                if ($room_value_type != $room_type) {
                                    continue; //skip eg. COMLAB == COMLAB else skip next room if not comlab
                                }
    
                                $found = false;
                                $room_value_capacity = $room_value['capacity'];
                                $room_value_room = $room_value['room'];
                                $query_run = false;
                                // Check kung may mag overlapping sa room table.
                                $query = "SELECT `room`,`day`,`schedule`,`schedule_to` FROM `temporary_sched` WHERE `user_id` = '$user_id' AND `day` = '$key_day' AND `room` = '" . $room_value_room . "'  AND (( '" . $tmp_sched . "' > schedule && '" . $tmp_sched_to . "' <  schedule_to ) 
                                OR (('" . $tmp_sched . "' > schedule && '" . $tmp_sched . "' < schedule_to) || ('" . $tmp_sched_to . "' > schedule && '" . $tmp_sched_to . "' < schedule_to)) 
                                OR ( '" . $tmp_sched . "' = schedule || '" . $tmp_sched_to . "' = schedule_to) 
                                OR (schedule > '" . $tmp_sched . "' && schedule_to < '" . $tmp_sched_to . "'))";
                                if ($query_run = mysqli_query($db_connect, $query)) {
                                    if (mysqli_num_rows($query_run) > 0) {  
                                        //overlap room;
                                        $tmp_sched = $tmp_sched_to;
                                        $tmp_sched_valid = Schedule_datetime($tmp_sched_to, $total_hours);
                                        $tmp_sched_to = Schedule($tmp_sched_to, $total_hours);

                                        if(isEndDay($value_day['totime'], $tmp_sched, $tmp_sched_to)){
                                            error_log($value_day['totime']."-- Dumaan".$tmp_sched."--".$tmp_sched_to);
                                            continue; //next room

                                        }

                                        if (strtotime($value_day['totime']) <= $tmp_sched_valid) {
                                            error_log(strtotime($value_day['totime'])."-- Dumaan_daw".$tmp_sched_valid);

                                            $stop_loop = true;
                                            $overlapping = false;
                                            continue; //next room
                                        }

                                    } else { //no same room schedule
                                        $found = true;


                                    }
                                }

    
                                if ($found) {

                                    $select_dup = "SELECT user_id FROM temporary_sched WHERE user_id = '" . $user_id . "' AND day = '" . $key_day . "' AND section = '" . $section . "' 
                                    AND (( '" . $tmp_sched . "' > schedule && '" . $tmp_sched_to . "' <  schedule_to ) 
                                    OR (('" . $tmp_sched . "' > schedule && '" . $tmp_sched . "' < schedule_to) || ('" . $tmp_sched_to . "' > schedule && '" . $tmp_sched_to . "' < schedule_to)) 
                                    OR ( '" . $tmp_sched . "' = schedule || '" . $tmp_sched_to . "' = schedule_to) 
                                    OR (schedule > '" . $tmp_sched . "' && schedule_to < '" . $tmp_sched_to . "')) ";
                                    $select_dup_run = mysqli_query($db_connect, $select_dup);
                                    if (mysqli_num_rows($select_dup_run) > 0) {
                                       
                                        $no_room = false;
                                        $stop_loop = true;
                                        $overlapping = false;
                                        break;
                                    } else {
                                        $schedule = $tmp_sched;
                                        $schedule_to = $tmp_sched_to;
                                    }

                                    $no_room = false;
                                    $room_value_capacity = $room_value['capacity'];
                                    $room_value_room = $room_value['room'];
                                    $overlapping = false;
                                    break;
                                }
                            }

                            if($no_room){
                                $stop_loop = true;
                                $overlapping = false;
                                break;
                            }

                            $count_loop++;
                        } //end while loop

                        if($no_room === true){
                            $error_message = "Insufficient room for faculty no. " . $faculty_id . " -" . $name."--".$room_type. "</br> NOTE: you need to add room.";

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
                                'status' => 400,
                                'message' => $error_message
                            ];
                            echo json_encode($res);
                            return;
                        }

                        if ($stop_loop) {
                            if ($last_day_availability == $key_day) {
                                $error_message = "Insufficient time in faculty no. " . $faculty_id . " -" . $name . "</br> NOTE: you need to add teacher time availability.";

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
                                    'status' => 400,
                                    'message' => $error_message
                                ];
                                echo json_encode($res);
                                return;
                            }

                            continue; //next day
                        }

                       

                        //add 03/13
                        // $no_room = true;



                        // if ($no_room === true) {
                        //     $error_message = "Insufficient of room NOTE: you need to add rooms.";
                        //     $log_out = ($schedule_to <= $schedule) ? 'true' : 'false';
                        //     //error_log($rm_type.'count_loop' . $faculty_id . "--" . $section . "--" . $subject . "--" . $total_hours."--".$schedule." >= ".$schedule_to.">>".$log_out ."<<".$key_day);

                        //     $droptable = "DELETE FROM `temporary_sched` WHERE `user_id` = '$user_id'";
                        //     $droptable_run = mysqli_query($db_connect, $droptable);

                        //     if (!$droptable_run) {
                        //         $res = [
                        //             'status' => 400,
                        //             'message' => 'Temporary table not deleted!'
                        //         ];
                        //         echo json_encode($res);
                        //         return;
                        //     }

                        //     $res = [
                        //         'status' => 400,
                        //         'message' => $error_message
                        //     ];
                        //     echo json_encode($res);
                        //     return;
                        // }

                        $select_dup = "SELECT user_id FROM temporary_sched WHERE user_id = '" . $user_id . "' AND day = '" . $key_day . "' AND section = '" . $section . "' 
                        AND (( '" . $schedule . "' > schedule && '" . $schedule_to . "' <  schedule_to ) 
                        OR (('" . $schedule . "' > schedule && '" . $schedule . "' < schedule_to) || ('" . $schedule_to . "' > schedule && '" . $schedule_to . "' < schedule_to)) 
                        OR ( '" . $schedule . "' = schedule || '" . $schedule_to . "' = schedule_to) 
                        OR (schedule > '" . $schedule . "' && schedule_to < '" . $schedule_to . "')) ";
                        $select_dup_run = mysqli_query($db_connect, $select_dup);
                        if (mysqli_num_rows($select_dup_run) > 0) {
                            $error_message = "Overlapping schedule in faculty no. " . $faculty_id . " -" . $name . "</br> NOTE: you need to add teacher time availability.";

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
                                'status' => 400,
                                'message' => $error_message
                            ];
                            echo json_encode($res);
                            return;
                        }


                        $total_hrs = (strtotime($schedule_to) - strtotime($schedule)) / 3600;
                        $hash = sha1($room_value_type . $room_value_room . $key_day . $value_day['fromtime'] . $value_day['totime'] . $faculty_id . $section . $subject . $course_title . $unit . $name . $schedule . $schedule_to . $user_id);
                        $query_insert = "INSERT INTO `temporary_sched`(`room_type`,`room`,`day`, `from_time`, `to_time`, `faculty_id`, `section`, `subject`,`course_title`,`unit`,`name`, `schedule`,`schedule_to`,`user_id`,`total_hrs`) VALUES ('$room_value_type','$room_value_room','$key_day','" . $value_day['fromtime'] . "','" . $value_day['totime'] . "','$faculty_id','$section','$subject','$course_title','$unit','$name','$schedule','$schedule_to','$user_id','" . $total_hrs . "')";

                        $query_insert_run = mysqli_query($db_connect, $query_insert);
                        $last_insert_id = mysqli_insert_id($db_connect);

                        if ($query_run) {

                            $value_day['fromtime'] = $schedule_to;
                            $value_day['teach_no'] = $value_day['teach_no'] + $get_hours;

                            break;
                        } else {
                            continue;
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
