<?php

class Task extends Api {

  public static function _get($id = null) {
    $userdata = User::requireAuth();
    
    if (!empty($id)) {
      $query = self::$pdo->prepare('select * from tasks where id = ? and user_id = ?');
      $query->execute(array($id, $userdata['id']));
      return $query->fetch(PDO::FETCH_ASSOC);
    }
    else {
      $query = self::$pdo->prepare('select * from tasks where user_id = ? limit 1000');
      $query->execute(array($userdata['id']));
      return $query->fetchAll(PDO::FETCH_ASSOC);
    }
  }  
  
  public static function _put($id = null) {
    return self::_post($id);
  }
  
  public static function _post($id = null) {
    $userdata = User::requireAuth();
    
    if (!empty($id)) {
      return self::update($id, Router::$post, $userdata['id']);
    }
    else {
      return self::create(Router::$post, $userdata['id']);
    }
  }
  
  public static function _delete($id) {
    $userdata = User::requireAuth();
    
    $query = self::$pdo->prepare('delete from tasks where id = ? and user_id = ?');
    $result = $query->execute(array($id, $userdata['id']));
    
    if ($result) {
      return array('status' => 'Ok');
    }
    else {
      return array('error' => 'Database error');
    }
  }
  
  private static function update($id, $data, $user_id) {
    $query = self::$pdo->prepare('update tasks set text = ?, due_date = ?, completed = ?, priority = ? where id = ? and user_id = ?');
    $result = $query->execute(array(
      (($data['text'])?$data['text']:''),
      (($data['due_date'])?$data['due_date']:'NOW()'),
      (($data['completed'])?$data['completed']:0),
      (($data['priority'])?$data['priority']:0),
      $id, 
      $user_id
    ));

    if ($result) {
      return self::_get($id);
    }
    else {
      return array('error' => 'Database error');
    }
  }
  
  private static function create($data, $user_id) {
    $query = self::$pdo->prepare('insert into tasks (text, due_date, completed, priority, user_id) values (?, ?, ?, ?, ?)');
    $result = $query->execute(array(
      (($data['text'])?$data['text']:''),
      (($data['due_date'])?$data['due_date']:0),
      (($data['completed'])?$data['completed']:0),
      (($data['priority'])?$data['priority']:0), 
      $user_id
    ));
    
    if ($result) {
      return self::_get(self::$pdo->lastInsertId('tasks_id_seq'));
    }
    else {
      return array('error' => 'Database error');
    }
  }  
  
}

?>