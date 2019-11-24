<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 14.11.2019
 * Time: 18:18
 */

require_once 'vendor\autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

$serviceAccount = ServiceAccount::fromJsonFile('secret/examservicedatabase-88ff116bf2b0.json');
$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

$database = $factory->createDatabase();
//die(print_r($database));