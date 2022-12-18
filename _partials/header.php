<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= isset($page_title) ? "$page_title /" : '' ?>Attendance</title>
    <link rel="stylesheet" href="<?= base_folder('/_inc/node_modules/bootswatch/dist/slate/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_folder('/assets/css/main.css') ?>">
    <link rel="icon" href="data:,">

</head>
<body>
    <header class="container my-3">
        <?= flash()->display() ?>
        <div class="row align-items-center">
            <label for="date" class="nav-item col-2">Select date:</label>
            <ul class="nav nav-pills d-flex justify-content-end col">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Log out</a>
                    </li>
            </ul> 
        </div>
        
        <div class="align-items-center">
            <form action="" method="POST" class="nav-item col-6">
                <select name="year" onchange="this.form.submit()" class="col-2 me-2 text-center">
                    <?php repeat_option_year(); ?>
                </select>
                
                <select name="month" onchange="this.form.submit()" class="col-3 me-2 text-center">
                    <?php repeat_option_month(); ?>
                </select>

                <button id="today" name="today" onchange="this.form.submit()" class="col-2 me-2 text-center rounded-3" type="button">Today
                    <?php  ?>
                </button>


                <!-- <select name="day" onchange="this.form.submit()" class="col-2 text-center">
                    <?php repeat_option_day(); ?>    
                </select> -->

            </form>
        </div>
        
    </header>