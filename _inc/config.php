<?php 

// require stuff
require_once 'vendor/autoload.php';

// /*show all errors, nastavenie zobrazenia chyb, na programatorske ucely, pri nasadeni na zivy server sa to zmaze alebo prepne do off/0*/
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
error_reporting(E_ALL &~E_NOTICE);

// constants & settings

define ('BASE_URL', 'http://localhost/attendance_app');
define ('APP_PATH', realpath(__DIR__ . '/../'));


// require functions
require_once 'functions-general.php';
require_once 'functions-data.php';

//-------GLOBAL DATABASE CONFIG
$config = [
    'db' => [
        'type' =>       'mysql',
        'server' =>     'localhost',
        'name' =>       'attendance_app',
        'username' =>   'root',
        'password' =>   'root',
        'charset' =>    'utf8'
    ]
];

$db = new PDO("{$config['db']['type']}:host={$config['db']['server']};dbname={$config['db']['name']};charset={$config['db']['charset']}", $config['db']['username'], $config['db']['password']);

$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
$db->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );

// sessions start + flash messages

if( !session_id()) @session_start();

//@ pred funkciou v php znamená, že aj keď nasledujúci príkaz vypíše error, tak ho nezobrazí.

use \Tamtamchik\SimpleFlash\Flash;