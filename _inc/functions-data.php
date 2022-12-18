<?php 

//get starting time of work for certain day and certain employee from db

use LDAP\Result;

function get_start_time($emp_id = 0, $date)
{
    if (!$emp_id) {
        return false;
    }

    global $db;

    $query = $db->prepare("
        SELECT start_time FROM attendance_all
        WHERE emp_id = :emp_id AND date = :date
    ");
    $query -> execute([$emp_id, $date]);

    $result = $query->fetch(PDO::FETCH_OBJ);

    $result = $result->start_time;

    return $result;
}

//get time data of work for certain day and certain employee from db

function get_time_data($emp_id = 0, $year, $month)
{
    if (!$emp_id) {
        return false;
    }

    global $db;

    $query = $db->prepare("
        SELECT date, start_time, end_time, ot_sign, overtime, verification FROM attendance_all
        WHERE emp_id = :emp_id AND year = :year AND month = :month
    ");
    $query -> execute([$emp_id, $year, $month]);

    $result = $query->fetchAll(PDO::FETCH_OBJ);

    return $result;
}

//generate random time between min and max time inserted as string

function randomTimeFormatted(string $min_time, string $max_time): string
{
    $min = strtotime($min_time);
    $max = strtotime($max_time);

    $time = rand($min, $max);

    return date("H:i:s", $time);
}

//overtime - calculate overtime for certain day inserted with $date from start to end including worktime

function overtime($date, $timeFrom, $timeTo, $workTime = '08:30:00')
{    
    $start = $date . " " . $timeFrom;
    $end = $date . " " . $timeTo;
    $wt = $date . " " . $workTime;

    $origin = new DateTime($start);
    $target = new DateTime($end);
    $interval = $origin->diff($target);

    $timeInWork = $date . " " . $interval->format('%r%H:%I:%S');

    $origin = new DateTime($wt);
    $target = new DateTime($timeInWork);
    $interval = $origin->diff($target);
    $overtime = $interval->format('%H:%I:%S');
    $sign = $interval->format('%R');

    // if ($interval->format('%r')) {
    //     $sign = 'minus';
    // }else {
    //     $sign = 'plus';
    // }

    return array($sign, $overtime);
}


//check if the start_time is already in db for current employee
function check_tf_in_db($emp_id, $date)
{
    global $db;

    $query = $db->prepare("
        SELECT start_time
        FROM attendance_all
        WHERE emp_id = :emp_id AND date = :date
    ");

    $query -> execute([
        'emp_id'        => $emp_id,
        'date'          => $date
    ]);

    $result = $query->rowCount();

    if ($result >= 1) {
        return true;
    }else {
        return false;
    }
}
//check if the end_time is already in db for current employee
function check_tt_in_db($emp_id, $date)
{
    global $db;

    $query = $db->prepare("
        SELECT end_time
        FROM attendance_all
        WHERE emp_id = :emp_id AND date = :date
    ");

    $query -> execute([
        'emp_id'        => $emp_id,
        'date'          => $date
    ]);

    $result = $query->fetch(PDO::FETCH_OBJ);

    if (!$result->end_time || is_null($result->end_time) ) {
        return false;
    }else {
        return true;
    }
}

//make day, month and year from date with explode
function make_dmy($date)
{    
    $explode = explode('-', $date);

    $day = $explode[2];
    $month = $explode[1];
    $year = $explode[0];

    return compact('day', 'month', 'year');
}


// fill table with complete data for inserted year and month and employee
function fill_table($year, $month, $emp_id)
{
    global $db;
    $long_months = ['1', '3', '5', '7', '8', '10', '12'];

    if ($month == 2 && is_leap_year($year.'-01-01')) {
        $end = 29;
    } elseif ($month == 2 ) {
        $end = 28;
    } else {
        ( in_array($month,$long_months) ) ? $end = 31 : $end = 30;
    }

    for ($i=1; $i <= $end; $i++) 
    {
        $date = $year.'-'.$month.'-'.sprintf("%02d", $i);
        $start_time = randomTimeFormatted('06:45:00','07:15:00');
        $end_time = randomTimeFormatted('15:15:00','16:00:00');
        $overtime = overtime($date, $start_time, $end_time);
        $verification = 1; 

        $day_name = date('l', strtotime($date));

        if ($day_name === 'Saturday' || $day_name === 'Sunday' ) {
            // $start_time = '';
            // $end_time = '';
            // $overtime[0] = '';
            // $overtime[1] = '';
            // $verification = 1;

            $query = $db->prepare("
                INSERT INTO attendance_all
                    (emp_id, date, year, month)
                VALUES
                    (:emp_id, :date, :year, :month)
                "); 

            $query -> execute([
                'emp_id'        => $emp_id,
                'date'          => $date,
                'year'          => $year,
                'month'         => $month
            ]);
        } else {
            $query = $db->prepare("
                INSERT INTO attendance_all
                    (emp_id, date, year, month, start_time, end_time, ot_sign, overtime, verification)
                VALUES
                    (:emp_id, :date, :year, :month, :start_time, :end_time, :ot_sign, :overtime, :verification)
                ");
            
            $query -> execute([
                'emp_id'        => $emp_id,
                'date'          => $date,
                'year'          => $year,
                'month'         => $month,
                'start_time'    => $start_time,
                'end_time'      => $end_time,
                'ot_sign'       => $overtime[0],
                'overtime'      => $overtime[1],
                'verification'  => $verification
            ]);
        }        
    }

}

//check if there are some missing days between last day and current day, which is going to be inserted, if there are some missing days, it will insert them without time data, only date

function check_missing_days($date, $emp_id = 1, $year, $month)
    {
        global $db;

        $query = $db->prepare("
        SELECT date FROM attendance_all
        WHERE emp_id = :emp_id
        ORDER BY id DESC LIMIT 1
            ");

        $query -> execute([$emp_id]);

        $last_day = $query->fetch(PDO::FETCH_OBJ);

        $last_day = strtotime($last_day->date);
        $current_day = strtotime($date);

        $diff = ($current_day - $last_day)/86400;

        for ($i=1; $i < $diff; $i +=1) {

            $missing_day = $last_day + $i * 86400;
            $date = date("Y-m-d", $missing_day);
            
            if ($current_day > $last_day) {
                $query = $db->prepare("
                    INSERT INTO attendance_all
                        (emp_id, date, year, month)
                    VALUES
                        (:emp_id, :date, :year, :month)
                ");

                $query -> execute([
                    'emp_id'        => $emp_id,
                    'date'          => $date,
                    'year'          => $year,
                    'month'         => $month
                ]);
            }
        }    
    }

//split time into hours, minutes and seconds and return total time in seconds
function time_HMS($time)
{
    $hours = date('H', strtotime($time));
    $minutes = date('i', strtotime($time));
    $seconds = date('s', strtotime($time));

    $hours_in_seconds = $hours * 60 * 60;
    $minutes_in_seconds = $minutes * 60;
    $total_seconds = $hours_in_seconds + $minutes_in_seconds + $seconds;

    return $total_seconds;
}

//

function total_overtime($day_number, $data)
{
    $total_time_pos = null;
    $total_time_neg = null;

    for ($i=1; $i <= $day_number; $i += 1) {

        $seconds = time_HMS($data[$i-1]->overtime);

        if ($data[$i-1]->ot_sign == '+') {
            $total_time_pos += $seconds;
        } elseif ($data[$i-1]->ot_sign == '-') {
            $total_time_neg -= $seconds;
        }
    }
        
    $total_ot = $total_time_pos + $total_time_neg;

    if (abs($total_time_pos) > abs($total_time_neg)) {
        $sign = '+';
    } elseif (abs($total_time_pos) < abs($total_time_neg)) {
        $sign = '-';
    } else {
        $sign = null;
    }
    $total_ot = date("H:i:s", abs($total_ot));

    return $sign.$total_ot;
}