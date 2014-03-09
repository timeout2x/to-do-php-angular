<?php

class Router {
  public static $method = 'GET';
  public static $object = '';
  public static $id = 0;
  public static $post = array();
  public static $JSONP = null;
  
  public function __construct() {
    
    // Get Request Method
    self::$method = $_SERVER['REQUEST_METHOD'];
    
    // Get Request URL
    $request = $_SERVER['REQUEST_URI'];
    if (strpos($request, '?')) $request = substr($request, 0, strpos($request, '?'));
    $request = explode('/', $request);
    
    // Get object/id based on request; validate method/object                  
    if (!empty($request[1])) {
      $object = ucfirst(preg_replace('/[^A-Za-z0-9]/', '', $request[1]));
      if (method_exists($object, '_is_api')) {
        if (method_exists($object, '_'.self::$method)) {
          self::$object = $object;
        }
        else {
          header('HTTP/1.1 405 Method Not Allowed');
          header('Allow: GET, PUT, POST, DELETE');
          exit;
        }
      }
      else {
        header('HTTP/1.1 404 Not Found');
        exit;
      }
      
      if (empty(self::$object)) {
        header('HTTP/1.1 400 Bad Request'); 
        exit();
      }
    }
    
    // Get ID if exists
    if (!empty($request[2])) {
      self::$id = preg_replace('/[^0-9]/', '', $request[2]);
    }
    
    // Are we using JSONP?
    if (!empty($_GET['JSONP'])) {
      self::$JSONP = $_GET['JSONP'];
    }
    
    // Get POST Data
    self::$post = self::getPostData();
  }
  
  // Attempt to get post data from different type of requests and encodings
  private static function getPostData() {
    if (!empty($_POST)) {
      return $_POST;
    }
    else {
      $postdata = file_get_contents("php://input");
      
      if (!empty($postdata)) {
        $jsonSymbol = substr($postdata, 0, 1);
        if ($jsonSymbol == '[' || $jsonSymbol == '{') {
          return json_decode($postdata, true);
        }
        
        parse_str($postdata, $post_vars);
        if (!empty($post_vars) && is_array($post_vars)) {
          return $post_vars;
        }
      }
    }
    return array();
  }
  
  public function performApiCall(PDO $pdo) {
    $object = new self::$object($pdo); 
    $result = call_user_func(array(self::$object, '_'.self::$method), self::$id);
    
    if (!empty($result)) {
      
      if (empty($result['error'])) {
        if (self::$method == 'POST' && empty(self::$id)) {
          header('HTTP/1.1 201 Created');
        }
        else {
          header('HTTP/1.1 200 OK');
        }
      }
      else {
        header('HTTP/1.1 400 Bad Request');  
      }
    }
    
    if (empty(self::$JSONP)) {
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($result);
    }
    else {
      header('Content-Type: application/javascript; charset=utf-8');
      echo self::$JSONP.'('.json_encode($result).');';
    }
  }
  
}

?>