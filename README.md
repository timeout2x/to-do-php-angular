to-do-php-angular
=======================

Test to-do application with PHP RESTful backend and angular frontend


## Demo
Demo is available here: http://morning-fortress-6226.herokuapp.com

Rest API can be tested via curl (you can find supply curl.php file). It doesnt want to run on heroku - i believe curl is forbidden there on free instances.

## REST description:
Accepts GET, PUT, POST, DELETE methods

It is also possible to call a JSONP request with supplied JSONP GET parameter, i.e. http://morning-fortress-6226.herokuapp.com/task?JSONP=myfunc

All requests (except user creation) require Basic Auth (password is transferred as SHA1 Hash)

### Users

##### Create user: PUT /user
* name - user name;
* login - user login;
* password - SHA1 hashed user password;


##### Login (get userinfo): GET /user/[login]
Requires Basic Auth:
* login - login that was used during registration;
* password - SHA1 hashed password;

### Tasks

##### Get Tasks: GET /task/[id]
* id - optional id parameter to get just 1 task;

##### Create Task: PUT/POST /task/[id]
* id - optional id parameter to change existing task;
* text - task text;
* priority - task priority (0-4);
* due_date - Due date (yyyy-mm-dd);
* completed - Marker if the task is completed(0/1);

##### Delete Task: DELETE /task/id
* id - task id;
