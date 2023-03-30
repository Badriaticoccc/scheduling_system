<?php
require '../config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;
require CL_SESSION_PATH;
require ISLOGIN;
$error_message = "";

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


    // $teach_load_data = array_map('trim', $teach_load_data);

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
        // $value['day'] = preg_split("/\,/", strtoupper($value['day']));
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

    // echo json_encode($array_room);
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


    foreach ($array_teaching as $key => $value) {
        $faculty_id = $value['faculty_id'];
        $index = $key;
        $sum = 0;
        $sum_availability = 0;

        foreach ($array_teaching[$index]['teaching_load'] as $key_load => $value_load) {
            //get total hours per week
            $hr_wk = $value_load['hr_wk'];
            $sum = $sum + $hr_wk;

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
                $sum = $sum + 1;
            }
        }

        // $sum = $sum + 4;

        if ($sum > $sum_availability) {
            $error_message = "Insuficient time in faculty no. " . $faculty_id . " </br> NOTE: you need to add teacher time availability.";

            $res = [
                'status' => 400,
                'message' => $error_message
            ];
            echo json_encode($res);
            return;
        }
    }


    $last_insert_id = 0;

    foreach ($array_teaching as $key => $value) {
        $faculty_id = $value['faculty_id'];
        $day = $value['Day']; //array
        $name = $value['name'];
        $status = $value['status'];
        $index = $key;
        foreach ($array_teaching[$index]['teaching_load'] as $key_load => $value_load) {

            $day_wk =  $value_load['day_wk'];
            $total_hours =  $value_load['total_hours'];
            $room_type = $value_load['room_type'];
            $section = $value_load['section'];
            $subject = $value_load['subject'];
            $course_title =  $value_load['course_title'];
            $unit = $value_load['unit'];

            for ($count = 0; $count < $day_wk; $count = $count + 1) {

                $i = 0;

                foreach ($array_teaching[$index]['Day'] as $key_day => &$value_day) {

                    // echo json_encode($array_teaching[$index]['Day']);
                    // exit;
                    $run = false;
                    $i = $i + 1;
                    $len = count($array_teaching[$index]['Day']) - 1;

                    // $value_day['fromtime'] = "18:00";

                    $availability_from_time = $value_day['fromtime'];
                    $availability_to_time = $value_day['totime'];


                    $from_time = strtotime($availability_from_time);
                    $to_time = strtotime($availability_to_time);


                    //difference between $from_time $to_time
                    $diff_minutes = round(abs($from_time - $to_time) / 60);

                    if ($total_hours > $diff_minutes) {
                        if ($i == $len) {

                            // echo json_encode(array_values($array_teaching));
                            // exit;
                            $error_message = "Insuficient time in faculty no. " . $faculty_id . "</br> NOTE: you need to add teacher time availability OR Insuficient Room .";
                            $droptable = "DELETE FROM `temporary_sched` WHERE `user_id` = '$user_id'";
                            $droptable_run = mysqli_query($db_connect, $droptable);
                            $i = 0;
                            $res = [
                                'status' => 400,
                                'message' => $error_message
                            ];
                            echo json_encode($res);
                            exit();
                            // }
                        } else {
                            //skip loop 
                            continue; //new day
                        }
                    }

                    //DAY eg. M
                    $available_day = $key_day;

                    // function to give time
                    $schedule_to = Schedule($availability_from_time, $total_hours);
                    $schedule = Schedule($availability_from_time, 0);
                    // get total hours per day
                    $sum_day = (strtotime($availability_to_time) - strtotime($availability_from_time)) / 3600;

                    //get hours
                    $get_hours = (strtotime($schedule_to) - strtotime($availability_from_time)) / 3600;


                    //check duplication of section schedule 
                    // $query_section = "SELECT `user_id` FROM `temporary_sched` WHERE `user_id` = '$user_id' AND `day` = '$available_day' AND `section` = '$section' ";
                    // $query_section_run = mysqli_query($db_connect, $query_section);
                    // if (mysqli_num_rows($query_section_run) > 0) {
                    //     // echo $schedule . "<br>" . $schedule_to . "<br>" . $section . "<br>" . $available_day;
                    //     continue; //next loop

                    // }

                    //check duplication of faculty schedule 
                    $query = "SELECT `user_id` FROM `temporary_sched` WHERE `user_id` = '$user_id' AND `day` = '$available_day' AND `faculty_id` = '$faculty_id' AND `section` = '$section' AND `schedule` = '$schedule'";
                    $query_run = mysqli_query($db_connect, $query);

                    if (mysqli_num_rows($query_run) > 0) {
                        continue; //next loop
                    }

                    if ($availability_from_time >= $availability_to_time ||  $availability_to_time < $schedule_to) {
                        continue; //next loop
                    }

                    // Check kung ilang oras na ba ang naituro ng faculty..
                    // check kung nakapag breaktime naba.
                    $query = "SELECT `schedule`,`schedule_to` FROM `temporary_sched` WHERE `user_id` = '$user_id' AND `day` = '$key_day' AND section = '$section'  ORDER BY temporary_id ASC";
                    $query_run = mysqli_query($db_connect, $query);

                    while ($row = mysqli_fetch_assoc($query_run)) {

                        $sched = $row['schedule'];
                        $sched_to = $row['schedule_to'];
                        $get_hours = (strtotime($sched_to) - strtotime($sched)) / 60;

                        if ($sched_to >= $availability_from_time) {
                            $add_hours = Schedule($availability_from_time, $get_hours);
                            $value_day['fromtime'] = $add_hours;
                        }
                    }

                    if ($value_day['teach_no'] >= 3 && $value_day['break_time'] == 0) {

                        $schedule_to = Schedule($availability_from_time, 60);
                        $get_hours = (strtotime($schedule_to) - strtotime($availability_from_time)) / 3600;

                        // insert Break Time
                        $break = "BREAK TIME";

                        $query = "INSERT INTO `temporary_sched`(`room_type`,`room`,`day`, `from_time`, `to_time`, `faculty_id`, `section`, `subject`,`course_title`,`unit`,`name`, `schedule`,`schedule_to`,`user_id`) VALUES ('$break','$break','$available_day','$availability_from_time','$availability_to_time','$faculty_id','$break','$break','$break','0','$name','$availability_from_time','$schedule_to','$user_id')";
                        $query_run = mysqli_query($db_connect, $query);

                        if ($query_run) {
                            $value_day['fromtime'] = $schedule_to;
                            $value_day['teach_no'] = $value_day['teach_no'] + $get_hours;
                            $value_day['break_time'] = 1;
                        }
                    } else if ($value_day['teach_no'] >= 8 && $value_day['break_time'] == 1) {

                        $schedule_to = Schedule($availability_from_time, 30);
                        $get_hours = (strtotime($schedule_to) - strtotime($availability_from_time)) / 3600;

                        // insert Break Time
                        $break = "BREAK TIME";
                        $query = "INSERT INTO `temporary_sched`(`room_type`,`room`,`day`, `from_time`, `to_time`, `faculty_id`, `section`, `subject`,`course_title`,`unit`,`name`, `schedule`,`schedule_to`,`user_id`) VALUES ('$break','$break','$available_day','$availability_from_time','$availability_to_time','$faculty_id','$break','$break','$break','0','$name','$availability_from_time','$schedule_to','$user_id')";
                        $query_run = mysqli_query($db_connect, $query);

                        if ($query_run) {
                            $value_day['fromtime'] = $schedule_to;
                            $value_day['teach_no'] = $value_day['teach_no'] + $get_hours;
                            $value_day['break_time'] = 2;
                        }
                    }

                    $availability_from_time = $value_day['fromtime'];
                    $schedule_to = Schedule($availability_from_time, $total_hours);

                    shuffle($array_room[ucwords($room_type)]);

                    if ($count == 0) {
                        foreach ($array_room[ucwords($room_type)] as $room_key => $room_value) {

                            $room_value_capacity = $room_value['capacity'];
                            $room_value_room = $room_value['room'];
                            $room_value_type = $room_value['type'];
                            $run = false;

                            // Check kung may mag du duplicate sa temp table..
                            $query = "SELECT `room`,`day`,`schedule`,`schedule_to` FROM `temporary_sched` WHERE `user_id` = '$user_id' AND `room` = '$room_value_room' AND `day` = '$available_day' AND `schedule` = '$availability_from_time' AND `schedule_to` = '$schedule_to'";
                            $query_run = mysqli_query($db_connect, $query);

                            if (mysqli_num_rows($query_run) == 0) {
                                $run = true;
                                break;
                            } else {
                                $run = false;
                                continue;
                            }
                        }
                    } else {
                        $select_last_inserted_room = mysqli_query($db_connect, "SELECT `room`,`room_type` FROM `temporary_sched` WHERE `temporary_id` = $last_insert_id");

                        $insert_row = mysqli_fetch_assoc($select_last_inserted_room);
                        $room_value_room = $insert_row['room'];
                        $room_value_type = $insert_row['room_type'];

                        $query = "INSERT INTO `temporary_sched`(`room_type`,`room`,`day`, `from_time`, `to_time`, `faculty_id`, `section`, `subject`,`course_title`,`unit`,`name`, `schedule`,`schedule_to`,`user_id`) VALUES ('$room_value_type','$room_value_room','$available_day','$availability_from_time','$availability_to_time','$faculty_id','$section','$subject','$course_title','$unit','$name','$availability_from_time','$schedule_to','$user_id')";
                        $query_run = mysqli_query($db_connect, $query);
                        $last_insert_id = mysqli_insert_id($db_connect);
                        if ($query_run) {
                            $value_day['fromtime'] = $schedule_to;
                            $value_day['teach_no'] = $value_day['teach_no'] + $get_hours;

                            break;
                        } else {
                            $i = $i + 1;
                            continue;
                        }
                    }


                    if ($run) {
                        $query = "INSERT INTO `temporary_sched`(`room_type`,`room`,`day`, `from_time`, `to_time`, `faculty_id`, `section`, `subject`,`course_title`,`unit`,`name`, `schedule`,`schedule_to`,`user_id`) VALUES ('$room_value_type','$room_value_room','$available_day','$availability_from_time','$availability_to_time','$faculty_id','$section','$subject','$course_title','$unit','$name','$availability_from_time','$schedule_to','$user_id')";
                        $query_run = mysqli_query($db_connect, $query);
                        $last_insert_id = mysqli_insert_id($db_connect);
                        if ($query_run) {
                            $value_day['fromtime'] = $schedule_to;
                            $value_day['teach_no'] = $value_day['teach_no'] + $get_hours;

                            break;
                        } else {
                            $i = $i + 1;
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

    $session_class->setValue('session_sched',  $last_id);
    $session_class->setValue('session_title', $title);

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

        $query = "INSERT INTO `permanent_sched_detail`(`parent_id`, `room_type`, `room`, `day`, `schedule_from`, `schedule_to`, `faculty_id`,`course_title`, `section`, `subject`,`unit`,`name`, `user_id`) VALUES ('$last_id','$room_type','$room','$day','$schedule_from','$schedule_to','$faculty_id','$course_title','$section','$subject','$unit','$name','$user_id')";
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
