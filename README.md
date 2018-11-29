# CQured WebApi Framework 2.0-alpha1

This is a PHP 7.2+ Web API to partner client-side applications like Angular, Ionic, etc.
![alt text](https://raw.githubusercontent.com/air-Design/airUI-Design-and-Media-.Tutorial-Example/media/apis.JPG)

- Simple and Easy to use.
- PSR - PHP Standard Requirement
- Extensible with composer
- Use JWT the easy way.
- EntityModel Class available in the box for Database queries.

## Getting Started

###Install with Composer
We recommend you create this project with a PHP composer CLI:
```shell
    composer create-project aiir/cqured your-api-name
```

###Download Directory from repo
- Download the the API from this repo, unzip it and place it at the
  directory of your PHP Server. eg. For XAMPP, place it at the 'htdocs' folder.
  You can rename the folder if you desire.
- I have a sample database in the 'asset/\_test_database' folder called coreDB,
  - Install this database to test out the PersonController
  - http://localhost/cqured/person
- Setting Up: Setting this framework up is very easy. Simply open the 'config.php' file; this file
  contains the configuration for the API.

```php
<?php

class Config
{
    // General
    public $enableProdMode = false;

    public $offline = false;
    public $offline_message = 'This site is down for maintenance.<br />Please check back again soon.';
    public $display_offline_message = '1';
    public $offline_image = '';
    public $sitename = 'airCore';
    public $captcha = '0';
    public $list_limit = '20';
    public $access = '1';


    // Header & Cross Origin Setting
    public $allow_origin = "http://localhost:4200";
    public $allow_methods ='GET, POST, PUT, DELETE, OPTIONS';
    public $max_age='86400'; //cache for 1 day
    public $content_type='json'; //return a json or xml result

    // Routing
    public $secret = 'Pi1gS3vrtWvNq3O0';
    public $routerPath="./api/ApiRouter.php";
}
```

- The General Section are for handling the production mode and the offline mode of the api
  (similar to coreFramework's config file );

- Headers & Cross Origin Settings: This is to set the client
  apps to allow, the REQUEST_METHODS to accepts and the
  expire of the connection.

- Routing Section: this is to point the routing file for the
  navigation of the api to get to the controls.

## Controllers

Controllers represents the routes that are called when a request is made.
for example, if i get users from my client side app, the uri to the
api would probably be http://api.com/person.

```javascript
getPerson():Observable<IUsers>{
    return this.http.get('http://api.com/person')
    .map(response => response.json());
}
```

- For the above example, the request is sent to the api via
  http://api.com
- The '/person' here is used in web-api for routing to the a controller
  which will then return a response.
- Routing of the url to a controller is set at the 'ApiRouter.php' file (which can be changed at the config.php)

```php
<?php

$apiRoutes = [
    [
      'path'=>'person',
      'controller'=>'PersonController'
      ],
    [
      'path'=>'user',
      'controller'=>'UserController'
      ]
    ];


$apiRouterModule = Program::getInstance('Routes');
$apiRouterModule->setRouter($apiRoutes);
```

- The 'path' represents the url path and its CASE-SENSITIVE
- Hence, https://api.com/person is not the same as https://api.com/Person
- 'Controller' is the controller to point to when the path mathes the url.
  - For example, the 'UserController.php' will be called if path is 'user'; i.e. http://api.com/user

## Default

By default, we only have a ValuesController in the controllers folder,
and in the ApiRouter.php file, we have mapped the path values to the controller.

- To view this in action, run your php server and open your browser.
- enter the api path and the router path:
  - http://localhost/cqured/values

You should see

```json
["value1", "value2"]
```

That value is returned from the 'httpGet()' method in the controller.

## Controller Structure

```php
<?php
namespace Api\Controllers;

class ValuesController
{
    /**
     * The Method httpGet() called to handle a GET request
     * URI: POST: https://api.com/values
     * URI: POST: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id
     * to the methodUndocumented function
     *
     * @param integer ...$id
     * @return array|null
     */
    public function httpGet(int...$id): ?array
    {
        // --- use this if you are connected to the Databases ---
        // if (count($id)) {
        //     $users = Cqured\Entity\EntityModel::table('user')
        //                     ->where('id', $id[0])
        //                     ->single();
        // } else {
        //     $users = Cqured\Entity\EntityModel::table('users')->get();
        // }

        // return ['data'=>$users,'totalCount'=>count($users)];

        return [
            'value1',
            'value2'
        ];
    }

    /**
     * The Method httpPost() called to handle a POST request
     * This method requires a body(json) which is passed as the var array $form
     * URI: POST: https://api.com/values
     *
     * @param array $form
     * @return array|null
     */
    public function httpPost(array $form): ?array
    {
        $postId = null;
        // --- use this if you are connected to the Databases ---
        // if (Cqured\Entity\EntityModel::table('values')->add($form)) {
        //     $alert = 'Succesfully saved';
        //      $postId = Cqured\Entity\EntityModel::$postId;
        // } else {
        //     $alert = 'Could not be saved. Please try again';

        // }

        // code here
        return [
            'success' => true,
            'alert' =>
            'We have it at post',
            'id' => $postId
        ];
    }

    /**
     * The Method httpPut() called to handle a PUT request
     * This method requires a body(json) which is passed as the var array $form and
     * An id as part of the uri.
     * URI: POST: https://api.com/values/2 the number 2 in
     * the uri is passed as int $id to the method
     *
     * @param array $form
     * @param integer $id
     * @return array|null
     */
    public function httpPut(array $form, int $id): ?array
    {

        // --- use this if you are connected to the Databases ---
        // if (Cqured\Entity\EntityModel::table('values')->where('id',$id)->update($form)) {
        //     $alert = 'Succesfully updated';
        //      $success = true;
        // } else {
        //     $alert = 'Could not be saved. Please try again';
        //      $success = false;

        // }

        // code here
        return [
            'success' => true,
            'alert' => 'We have it at put'
        ];
    }

    /**
     * The Method httpDelete() called to handle a DELETE request
     * URI: POST: https://api.com/values/2 ,the number 2 in
     * the uri is passed as int ...$id to the method
     *
     * @param integer $id
     * @return array|null
     */
    public function httpDelete(int $id): ?array
    {
        // code here
        return ['id' => $id];
    }
}
```

- Each time a request is sent to a controller, the api checks if the request method is allowed in the api (see config.php; Headers & Cross Origin Setting).
- Then it check the ApiRouter.php to map the url path to the controller.
- When its found, the controller checks if it has a method to handle that Requested Method, else, an error is returned.

## API/CONTROLLERS FOLDER

The 'api/controllers' folder contains all the controllers for the API. These controllers represent the individual uri(s) for data of the API.

Controllers are created in the controllers folder in as a single file.

## Creating a Controller

- Open the controllers folder and create a new php file. The file name should match the name of the controller you want to create. (Let us consider creating a controller for users.) Hence the controller file should be named UserController.php.

- The UserController.php file here handles all the php logics and variable which are made available for the client app as JSON. It could be considered as the controller of the MVC framework.

This is how the UserController.php will look like.

- The class name must match the name of the controller and exists in the Api\Controllers namespace (PSR4).

```php
<?php
namespace Api\Controllers;

class UsersController
{


  // method called to handle a GET request

  function httpGet(int ...$id): ?array
  {

      return ['value1','value2'];
  }


  // method called to handle a POST request
  function httpPost(array $form): ?array
  {
      $postId=null;

    // code here
      return ['success'=>true,'alert'=>'We have it at post','id'=>$postId];
  }


  // method called to handle a PUT request
  function httpPut(array $form, int $id): ?array
  {


    // code here
      return ['success'=>true,'alert'=>'We have it at put'];
  }


  // method called to handle a DELETE request
  function httpDelete(int $id): ?array
  {
    // code here
      return ['id'=>2];
  }
}
```

## Routing

Though the users controller is created, we have not created any route to get to that controller. To do so, we open the ApiRouter.php file at the root directory of the framework.

- The ApiRouter.php file registers a url to a controller, so that whenever the url matches the one registered, the binding controller is rendered.

- Create a variable called $users. The variable must be an array with members

  - path: the url to bind the component to. In this case, input 'users' as the value for this array member.
  - controller: this should correspond to the controller name in the controllers folder. In this case, the controller name is 'users'

  - The $users variable should look like:
    - $users = ['path'=>'user', 'controller'=>'UserController'];

- The above variable is not yet registered as a route. To register the variable, add it as a member to the '$appRoutes' array . Like so:
  - $appRoutes=[..., $users];

```PHP
 $users = [
            'path'=>'user', // http://127.0.0.1/users the router looks if the url matches the path, hence users. i.e http://api.com/{{path}}
            'controller'=>'UserController' // Controller to go when the path matches the url given
            ];

$appRouter = [
  [
    'path'=>'values',
    'controller'=>'ValueController'
    ],
 $users

];


$apiRouterModule = Program::getInstance('Routes'); // creates an instance of the router class
$apiRouterModule->setRouter($apiRoutes); // registers the routers
```

- Now the users controller is available to the router to view in the browser. Enter the server name to the App and add the path of the controller ('users'). I.e
  - http://localhost/api/user.
  - This should display the users controller data as JSON from the httpGet() method.

### AUTHGUARD

- Authentication and Authorization is key in Application development and cQured Web API already has this feature implemented for you.
- 'authguard' is also a member of the routes and takes arrays of models name as strings which are used to authorize clients to access the controller.
- example:

```PHP
$appRouter = [
  //Values Controller does not contain any Authorization
  [
    'path'=>'values',
    'controller'=>'ValueController'
    ],

  //Users Controller contains Authorization
  [
  'path'=>'user',
  'controller'=>'UserController',
  'authguard'=>['AuthenticationModel'] // authguard checks a method 'canActivate():bool' in the model authenticate.model
  ];

];

$appRouterModule = Programe::getInstance('Router'); //creates an instance of the router class
$appRouterModule->setRouter($appRouter); //registers the routers
```

- The authguard member checks the method 'canActivate():bool' in the model AuthenticateModel which expects a boolean return.
  - If the canActivate method returns true, then the controller loads
  - if false, controller does not loads and the developer has the freedom to redirect the user to a differenct controller or show a 404 error.
- Example of the authenticate model is as follows:

```php
<?php
namespace Api\Models;

/**
 * AuthenticationModel Class exists in the Api\Models namespace
 * This class to Authourized Access to Controller
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */

class AuthenticationModel implements \Cqured\Router\CanActivate
{
    /**
     * Method Used to Auhtourize Access to Controller,
     * Method excepts a boolean return
     * Return false, to denied access or true to allow
     */
  function canActivate(string $url):bool{
    if (CoreSession::IsLoggedIn()) {
      return true;
    }else{
      // echo \json_encode(['error'=>'access denied']);
        return false;
    }
  }


}
```

- The $url parameter of the method is automatically passed in by cQured API: That is, the path you are trying to access.

- CoreSession::IsLoggedIn() is a static method in cQured used to check if a users is LoggedIn.
- This method specifically checks if the $\_SESSION['id'] isset.

## MODELS FOLDER

This folder contains model files for database queries.
For legibility and separation of concerns, use the models for database related scripts and the controller file for logics.

- Every model must contain a [name]Model.php (where [name] represent the name of the model or controller the model is related to).

- Example of UserModel.php.

```php
<?php

namespace Api\Models;

class UserModel
{

    private $_table='users';

    function getUsers()
    {
        return DB::table($this->table)
                    ->get();
    }

    function getUser(int $id)
    {
        return DB::table($this->table)
                    ->where('id', $id)
                    ->get();
    }
}
```

- To get to an instance of the model in the component,
  - Create a private varible in the component called '$dataModel'
  - In the 'constructor()' method, get the model with 'CORE::getModel('user');

```php
namespace Api\Models;

use Cqured\Entity\EntityModel;

class UsersComponent
{
private $_table = 'User';


    protected $airCoreDB;

    /**
     * Connect to database in constructor
     */
    public function __construct()
    {
        $dsn = 'mysql:dbname=coreDB;host=127.0.0.1';
        $user = 'root';
        $password = 'my_password';
        $this->airCoreDB = new EntityModel($dsn, $user, $password);
        $this->onInit();
    }

    /**
     * Get All Data
     *
     * @return array
     */
    public function getUsers(): ?array
    {
        return $this->airCoreDB
            ->table($this->_table)
            ->get();
    }

    /**
     * Get Single Person
     *
     * @param integer $id
     * @return object
     */
    public function getUser(int $id): ?object
    {
        return $this->airCoreDB
            ->table($this->_table)
            ->where('id', $id)
            ->single();
    }
}
```

- Now that we have the model in the component, we can easily get the data of all users or a single user from the model in the component.

```php
namespace Api\Controllers;

use Api\Models\UserModel;

class UsersController
{

  // method called to handle a GET request

  function httpGet(int ...$id): ?array
  {
      $this->_userModel = new UserModel;
      // Check if it has a routing parameter
        // i.e https://api.com/person/2
        // where 2 in the uri represents $id[0]
        if (count($id)) {
            // Since this method(httpGet) returns and array,
            // we will have to cast the object returned from (_userModel->getPerson)
            // to an array
            return (array) $this->_userModel->getPerson((int) $id[0]);

        } else {
            $data = $this->_userModel->getPersons();

        }

        return [
            'data' => $data,
            'total' => count($data)
        ];
  }
```

- Rather than using the '$\_GET' global variable, CQured has a class called ActivatedRoute, which stores any POST or GET sanitized in it.
- A more pleasing way would be to use the class.
  - Create private variable called $params, then instantiate it at the CORE::getInstance('params');

```php
namespace Api\Controllers;

use Api\Models\UserModel;
use Cqured\Router\ActivatedRoute;

class UsersController
{
    private $_userModel;
    private $_params;
    //create a variable to hold data
    public $data;

    function httpGet(): array
    {
      // instantiate the model
      $this->_userModel = new UserModel;

      // the ActivatedRoute class contains values of $_GET global
        // Hence, $this->params->foo gets my_security($_GET['foo']).
        // if $_GET['foo'] does not exist, null is returned
        $this->params = new ActivatedRoute;

      if(!is_null($this->params->id)){

        return $this->getSingleData($this->params->id);

      }else{

        // get data
        return $this->getData();
      }
    }

    function getData(): ?array
    {
      $result = $this->_userModel->getUsers();
      return ['data'=>$result];
    }

    function getSingleUser(int $id): ?array
    {
      $result = $this->_userModel->getUser($id);;
      return ['data'=>$result];

    }

}
```

- There are other features, such as caliing a routing to a Controller under another namespace, but we will make
  a crash course on it when its finally released

- To query the table
  \*\* These returns a row of objects

- 'DB' here is the alias or represents the 'EntityModel' class

- Query any SQL statement :

```php
DB::sql('SELECT * FROM users t WHERE u.age > 45 LIMIT 10 ORDER BY u.name')
        ->get();
```

- SELECT All Users :

```php
DB::table('user')
          ->get();
```

- SELECT All Users DISTINCT:

```php
DB::table('user')
          ->distinct();
```

- Count All Users :

```php
DB::table('user')
         ->count();
```

- SELECT All Users with only id and name Fields:

```php
DB::table('user')
          ->fields('id, name')
          ->get();
```

- SELECT All Users with only id and name Fields 'AS' username:

```php
DB::table('user')
          ->fields('id, name AS username')
          ->get();
```

- SELECT All Users Limit to 10 :

```php
 DB::table('user')
          ->limit(10)
          ->get();
```

- SELECT All Users Limit to 10 Offset 100:

```php
DB::table('user')
         ->limit(10)
         ->offset(100);
```

- SELECT First User in Users row :

```php
DB::table('user')->first();
```

- SELECT Last User in Users row :

```php
DB::table('user')->last();
```

- SELECT All Users Order by name DESCENDING :

```php
DB::table('user')
       ->orderBy('name')
       ->get();
```

- SELECT All Users Order by ASCENDING :

```php
DB::table('user')
          ->orderBy('name',2)
           ->get();
```

- SELECT User with id == 3 :

```php
DB::table('user')
        ->where('id',3)
    	  ->get();
```

- SELECT User with id != 3 :

```php
DB::table('user')
        ->where('id','!=',3)
        ->get();
```

- SELECT User with id < 3 :

```php
DB::table('user')
        ->where('id','<',3)
        ->get();
```

- SELECT User with id > 3 :

```php
DB::table('user')
          ->where('id','>',3)
          ->get();
```

- SELECT User with id <= 3 :

```php
DB::table('user')
          ->where('id','<=',3)
          ->get();
```

- SELECT User with name LIKE 'kel' :

```php
DB::table('user')
          ->where('id','LIKE','%kel%')
          ->get();
```

\*\* These returns a single object

- SELECT A Single User with id == 3 :

```php
DB::table('user')
          ->where('id',3)
          ->single();
```

- SELECT Users with id == 3 or name = 'kelvin' :

```php
DB::table('user')
          ->where('id',3)
          ->orWhere('name','kelvin')
          ->get();
```

- SELECT Users with id == 3 and name = 'kelvin' :

```php
DB::table('user')
          ->where('id',3)
          ->andWhere('name','kelvin')
          ->get();
```

\*\* These returns a true or false if its sucessfull or failed

- INSERT New User:

```php
$data = [
  'name'=>'kelvin',
  'email'=>'kelvin@air.com',
  'gender'=>'male'
  ];
DB::table('user')
          ->add($data);
```

- UPDATE User with id = 3 :

```php
$data = [
  'name'=>'kelvin',
  'email'=>'kelvin@air.com',
  'gender'=>'male'
  ];
DB::table('user')
          ->where('id',3)
          ->update($data);
```

- DELETE User with id = 3 :

```php
DB::table('user')
          ->where('id',3)
          ->delete();
```

- JOIN queries
  - SELECT \* FROM Users u and INNER JOIN comments c ON u.id == c.userId

```php
DB::table('user u')
        ->join('comment','c')
        ->on('u.id','c.userId')
        ->get();
```

- SELECT \* FROM Users u and LEFT JOIN comments c ON u.id == c.userId

```php
DB::table('user u')
    ->leftJoin('comment','c')
    ->on('u.id','c.userId')
    ->get();
```

- SELECT \* FROM Users u and RIGHT JOIN comments c ON u.id == c.userId

```php
DB::table('user u')
    ->rightJoin('comment','c')
    ->on('u.id','c.userId')
    ->get();
```

- The letter 'u' in the table method after the table name 'user' is the alias of the table.
- There also is the rightJoin, leftJoin, innerJoin, fullJoin

- GROUP BY
- SELECT Users u and RIGHT JOIN comments c ON u.id == c.userId GROUP BY u.id

```php
DB::table('user u')
    ->rightJoin('comment','c')
    ->on('u.id','c.userId')
    ->groupBy('t.id')
    ->get();
```

- MULTI DATABASE
- SELECT identityDB.Users u and INNER JOIN blogDB.comments c ON u.id

```php
DB::table('identityDB.user u')
    ->join('blogDB.comment','c')
    ->on('u.id','c.userId')
    ->groupBy('t.id')
    ->get();
```

## Authentication and Authorization.

You should see the 'vendor' folder at the root directory of the api. This is in relation to 'composer.json'.
Hence you can add other libraries and packages to the api for both security and manipulations of data.
