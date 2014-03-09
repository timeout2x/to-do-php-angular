<?php

$user = new User('name', 'login', 'password');

echo '<strong>Register user: </strong><br/>'.$user->register().'<br/><br/>';
echo '<strong>Login: </strong><br/>'.$user->login().'<br/><br/>';

$tasks = new Tasks($user);
echo '<strong>List tasks: </strong><br/>'.$tasks->get().'<br/><br/>';

$task = $tasks->create(array(
  'text' => 'A new task',
  'due_date' => '2013-11-01',
  'priority' => 2,
  'completed' => 0,
));
echo '<strong>Create task: </strong><br/>'.$task.'<br/><br/>';

$task = json_decode($task, 1);
if (!empty($task['id'])) {
  echo '<strong>Get task that we created: </strong><br/>'.$tasks->get($task['id']).'<br/><br/>';
  echo '<strong>Delete that we created: </strong><br/>'.$tasks->remove($task['id']).'<br/><br/>';
}


class User {
  public static $name = '';
  public static $login = '';
  public static $password = '';
  
  public $host = 'http://morning-fortress-6226.herokuapp.com/user';
  
  public function __construct($name, $login, $password) {
    self::$name = $name;
    self::$login = $login;
    self::$password = $password;
  }
  
  public function register() {
    $ch = curl_init($this->host);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HEADER, 0); 

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
      'name' => self::$name,
      'login' => self::$login,
      'password' => sha1(self::$password),
    )));

    return curl_exec($ch);
  }
  
  public function login() {
    $ch = curl_init($this->host.'/'.self::$name);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HEADER, 0); 

    curl_setopt($ch, CURLOPT_USERPWD, self::$login.':'.sha1(self::$password));

    return curl_exec($ch);
  }
  
}

class Tasks {
  private static $user;
  
  public $host = 'http://morning-fortress-6226.herokuapp.com/task';
  
  public function __construct(User $user) {
    self::$user = $user;
  }
  
  public function get($id = null) {
    if (!empty($id)) {
      $ch = curl_init($this->host.'/'.$id);
    }
    else {
      $ch = curl_init($this->host);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HEADER, 0); 

    $user = self::$user;
    curl_setopt($ch, CURLOPT_USERPWD, $user::$login.':'.sha1($user::$password));

    return curl_exec($ch);
  }
  
  public function create($data) {
    $ch = curl_init($this->host);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_HEADER, 0); 

    $user = self::$user;
    curl_setopt($ch, CURLOPT_USERPWD, $user::$login.':'.sha1($user::$password));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    return curl_exec($ch);
  }
  
  public function remove($id) {
    $ch = curl_init($this->host.'/'.$id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HEADER, 0); 

    $user = self::$user;
    curl_setopt($ch, CURLOPT_USERPWD, $user::$login.':'.sha1($user::$password));

    return curl_exec($ch);
  }
  
}


?>