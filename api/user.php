<?php

class User extends Api {

  public static function _get($login) {
    $userdata = self::requireAuth();
    return $userdata;
  }  
  
  public static function _put($id = null) {
    return self::_post($id);
  }
  
  public static function _post($id = null) {
    return self::create(Router::$post);
  }
  
  private static function create($data) {
    if (empty($data['login']) || empty($data['name']) || empty($data['password'])) {
      return array('error' => 'Not enough data');
    }

    if (self::exists($data['login'])) {
      return array('error' => 'User exists');
    }

    $query = self::$pdo->prepare('insert into users (name, login, password) values (?, ?, ?)');
    $result = $query->execute(array(
      $data['name'],
      $data['login'],
      $data['password'],
    ));
    
    if ($result) {
      return array('status' => 'Ok');
    }
    else {
      return array('error' => 'Database error');
    }
  }
  
  private function exists($login) {
    $query = self::$pdo->prepare('select id from users where login = ?');
    $query->execute(array($login));
    return ($query->fetchColumn())?true:false;
  }
  
  private function login($login, $password) {
    $query = self::$pdo->prepare('select id, login, name from users where login = ? and password = ?');
    $query->execute(array($login, $password));
    return $query->fetch(PDO::FETCH_ASSOC);
  }
  
  public static function requireAuth() {
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
      header('HTTP/1.0 401 Unauthorized');
      exit;
    }
    
    $userdata = self::login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
    if (empty($userdata)) {
      header('HTTP/1.0 401 Unauthorized');
      exit;
    }

    return $userdata; 
  }  

}

?>