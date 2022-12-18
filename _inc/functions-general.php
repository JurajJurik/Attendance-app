<?php 

// global functions

function show_404()
{
    header("HTTP/1.0 404 Not Found");
        include_once "404.php";
        die();
}

//---------------------------------------
function base_folder($path, $base = BASE_URL .'/')
{
    $path = trim ($path, '/');
    return filter_var( $base.$path, FILTER_SANITIZE_URL);
}

//---------------------------------------
function redirect( $page, $status_code = 302 )
{
    if ($page == 'back') {
        $location = $_SERVER['HTTP_REFERER'];
    } else {
        $page = str_replace( BASE_URL, '', $page );
        $page = ltrim($page , '/');
        
        $location = BASE_URL . "/$page";
    }

    //var_dump($location);

    header("Location: $location", true, $status_code);
    die('success');
}

//***** */
//function for building options for dropdown menu including 5 year going after each other
//function is used in forms and it keeps the last submitted value from dropdown menu

function repeat_option_year($num_years = 5)
{
    $start = date("Y");
    
    $end = $start + $num_years;

    for($i = $start; $i <= $end; $i += 1)
    {
        (isset($_POST['year']) && $i == $_POST['year'] ) ? $selected = 'selected' : $selected = '';
        echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>'; 
    }    
}

function repeat_option_month()
{
    $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $current_month = date("F");

    foreach ($months as $key => $value) {
        $key = $key + 1;

        (!isset($_POST['month'])) ? $default = true : $default = false;

        if ($default) {
            ($value == $current_month ) ? $selected = 'selected' : $selected = '';
            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
        } else {
            ($key == $_POST['month'] ) ? $selected = 'selected' : $selected = '';
            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
        }
    } 
}

function repeat_option_day ()
{
    $long_months = ['1', '3', '5', '7', '8', '10', '12'];
    $current_day = date("j");
    //$current_year = date("Y");

    if (isset($_POST['month'])) {
        $current_month = $_POST['month'];
    }else {    
        $current_month = date("n");
    }

    if (isset($_POST['year'])) {
        $current_year = $_POST['year'];
    }else {    
        $current_year = date("Y");
    }

    if ($current_month == 2 && is_leap_year($current_year.'-01-01')) {
        $end = 29;
    } elseif ($current_month == 2 ) {
        $end = 28;
    } else {
        ( in_array($current_month,$long_months) ) ? $end = 31 : $end = 30;
    }

    for($i = 1; $i <= $end; $i += 1)
    {
        (!isset($_POST['day'])) ? $default = true : $default = false;
        if ($default) {
            ($i == $current_day ) ? $selected = 'selected' : $selected = '';
            echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
        } else {
            ($i == $_POST['day'] ) ? $selected = 'selected' : $selected = '';
            echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
        }    
    }    
}


function today()
{
    
}

//check if the year is leap/bisect year (priestupny)
function is_leap_year($date)
{
    $date = date_create($date);
    return date_format($date, 'L');
}

//detect number of days in current month
function day_number($month)     
{   
    $long_months = ['1', '3', '5', '7', '8', '10', '12'];

    if (isset($_POST['year'])) {
        $current_year = $_POST['year'];
    }else {    
        $current_year = date("Y");
    }

    if ($month == 2 && is_leap_year($current_year.'-01-01')) {
        $day_number = 29;
    } elseif ($month == 2 ) {
        $day_number = 28;
    } else {
        ( in_array($month,$long_months) ) ? $day_number = 31 : $day_number = 30;
    }

    return $day_number;
}