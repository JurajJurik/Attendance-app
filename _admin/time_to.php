<?php 
require_once "../_inc/config.php";

// add employee id, date and time from

$emp_id = 1;
$date = date("Y-m-d");
$end_time = date("H:i:s");
$end_time = randomTimeFormatted('15:30:00','16:15:00');

$start_time = get_start_time(1, $date);
$overtime = overtime($date, $start_time, $end_time);

var_dump($overtime[0]);

if(check_tt_in_db($emp_id, $date))
{
    flash()->warning('End time is already recorded!');
    redirect('/');
}
//die();

if (check_tf_in_db($emp_id, $date)) {
     $query = $db->prepare("
        UPDATE attendance_all SET
            end_time = :end_time,
            ot_sign  = :ot_sign,
            overtime = :overtime
        WHERE
            emp_id = :emp_id AND date = :date
    ");

    $insert = $query -> execute([
        'emp_id'        => $emp_id,
        'date'          => $date,
        'end_time'      => $end_time,
        'ot_sign'       => $overtime[0],
        'overtime'      => $overtime[1]
    ]);

    

    flash()->success('End time recorded successfully!');
    redirect('/');

} else {
        $query = $db->prepare("
        INSERT INTO attendance_all
            (emp_id, date, end_time)
        VALUES
            (:emp_id, :date, :end_time)
    ");

    $insert = $query -> execute([
        'emp_id'        => $emp_id,
        'date'          => $date,
        'end_time'       => $end_time
    ]);

    flash()->success('End time recorded successfully, but with NO start time!');
    redirect('/');
}