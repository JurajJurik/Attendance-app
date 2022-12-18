<?php

    if (!isset($_POST['year'])) {
        $year =  date("Y");
    } else {
        $year = $_POST['year'];
    }

    if (!isset($_POST['month'])) {
        $month =  sprintf("%02d", date("m"));
    } else {
        $month = sprintf("%02d", $_POST['month']);
    }

    if (!isset($_POST['day'])) {
        $form_day =  sprintf("%02d", date("d"));
    } else {
        $form_day = sprintf("%02d", $_POST['day']);
    }

    $emp_id = 1;

    $data = get_time_data(1, $year, $month);

    $day_number = day_number($month);

    // for ($i=1; $i <= 10; $i ++ ) { 
    //     fill_table(2022, $i, 1);
    // }
?>



    <table class="table">
        <thead >
            <tr>
                <th scope="col" class="text-center">Date</th>
                <th scope="col" class="text-center">Day</th>
                <th scope="col" class="text-center">Time from</th>
                <th scope="col" class="text-center">Time to</th>
                <th scope="col" class="text-center">Overtime</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= $day_number; $i += 1) 
                {   
                    $day_date = $year.'-'.$month.'-'.sprintf("%02d", $i);

                    $day_name = date('l', strtotime($day_date));

                    $st = $data[$i-1]->start_time;
                    $et = $data[$i-1]->end_time;
                    $os = $data[$i-1]->ot_sign;
                    $ot = $data[$i-1]->overtime;

                    if (is_null($data[$i-1]->start_time)) {
                        $st = '-';
                    }
                    if (is_null($data[$i-1]->end_time)) {
                        $et = '-';
                    }
                    if (is_null($data[$i-1]->ot_sign)) {
                        $os = '';
                    }
                    if (is_null($data[$i-1]->overtime)) {
                        $ot = '-';
                    }


                    echo    '<tr>
                            <td class="text-center">'.$day_date.'</td>
                            <td class="text-center">'.$day_name.'</td>
                            <td class="text-center">'.$st.'</td>
                            <td class="text-center">'.$et.'</td>
                            <td class="text-center">'.$os.$ot.'</td>
                            </tr>';
                }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th class="text-center">Summary</th>
                <th colspan="3"></th>
                <th class="text-center"><?= total_overtime($day_number, $data) ?></th>
            </tr>
        </tfoot>
    </table>