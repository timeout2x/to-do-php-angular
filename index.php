<?php


require_once('router.php');
require_once('api/task.php');
require_once('api/user.php');
$router = new Router;

//$DATABASE_URL = $_ENV['DATABASE_URL'];
$DATABASE_URL = 'pgsql:host=ec2-23-21-196-147.compute-1.amazonaws.com;port=5432;dbname=d4r0b4797fisic;user=znbypnddnbnikb;password=0v5FWAP_YlFBsw3ZTzr3jZD2og';
$pdo = new PDO($DATABASE_URL);

$router->performApiCall($pdo);

class Api {
  protected static $pdo;
  
  final public function __construct(PDO $pdo) {
    self::$pdo = $pdo;
  }
  
  public static function _is_api() {
    return true;
  }
  
}
?>