<?php 
require "../_inc/config.php";

// add employee id, date and time from

$emp_id = 1;
$date = date("Y-m-d");
$dt = make_dmy($date);
$year = $dt['year'];
$month = $dt['month'];
//$start_time = date("H:i:s");
$start_time = randomTimeFormatted('06:45:00','07:15:00');


// if (check_if_tf_is_null ($emp_id, $date)) {
//     flash()->error('You forgot to record start time. You have to do it manually. Contact your admin.');
//     redirect('/');
// }

check_missing_days($date, $emp_id, $year, $month);

if (check_tf_in_db($emp_id, $date)) {
    flash()->warning('Start time is already recorded!');
    redirect('/');
}
    $query = $db->prepare("
        INSERT INTO attendance_all
            (emp_id, date, year, month, start_time)
        VALUES
            (:emp_id, :date, :year, :month, :start_time)
    ");

    $query -> execute([
        'emp_id'        => $emp_id,
        'date'          => $date,
        'year'          => $year,
        'month'         => $month,
        'start_time'     => $start_time
    ]);

flash()->success('Start time recorded successfully!');
redirect('/');