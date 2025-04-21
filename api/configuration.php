<?php
## It's for your settings
### variables
#### database connection
define('DB_HOST', 'MySQL-8.0');
define('DB_NAME', 'StudWork');
define('DB_USER', 'root');
define('CHARSET', 'utf8mb4');
define('DB_PASS', '');
#### settings
define('ALLOW_MISSING_FIELDS', true);
define('IGNORE_VALIDATION', false);
define('HASH_ALGORITHM', PASSWORD_DEFAULT);
define('HASH_FIELD', 'password');
define('TOKEN_ENTITY_HOLDER','users');
define('TOKEN_FIELD_HOLDER','token');
define('LOGIN_FIELD','email');
define('ALLOW_GET_ALL', false);
define('ALLOW_GET', true);
define('ROLE_FIELD','role');
define('IGNORE_ROLES', true);
define('METHOD_GENERATE_TOKEN_FOR_ALL', true);
define('ENTITIES_NEED_TIME', true);
define('LOGGING', true);
define('DEV_MODE', true);
### validations
$validations = [];
$validations[] = ['entity' => 'users', 'field' => 'email' , 'expression' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'];
$validations[] = ['entity' => 'users', 'field' => 'password' , 'expression' => '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d@$&\-_\s]{8,}$/'];
#$validations[] = ['entity' => 'users', 'field' => 'number' , 'expression' => '/^\d{10}$/'];
$validations[] = ['entity' => 'users', 'field' => 'full_name' , 'expression' => '/^[А-Яа-яЁё\s\-]+$/u'];
#$validations[] = ['entity' => 'users', 'field' => 'full_name' , 'expression' => '/^[A-Za-z\s\-]+$/'];
$validations[] = ['entity' => '@any', 'field' => 'birthday' , 'expression' => '/^\d{4}-\d{2}-\d{2}$/'];
$validations[] = ['entity' => '@any', 'field' => 'time' , 'expression' => '/^(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d$/'];
$validations[] = ['entity' => '@any', 'field' => 'datetime' , 'expression' => '/^\d{4}-\d{2}-\d{2} (?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d$/'];
#$validations[] = ['entity' => 'users', 'field' => 'link' , 'expression' => '/^(https?:\/\/)?([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,}(:\d+)?(\/[^\s]*)?$/'];
$validations[] = ['entity' => 'users', 'field' => 'about', 'expression' => '@text'];
### roles
### existing methods: getOne, getAll, getFiltered, add, delete, put, patch, search, generateToken, auth
# $roles = [];
# $roles[] = ['role' => 'student', 'allowed methods' => ['chats' => ['getFiltered', 'add'], 'favorites_list' => ['getFiltered', 'add', 'put'], 'response' => ['add', 'delete', 'getFiltered'], 'resume' => ['getFiltered', 'add'], 'vacancy' => ['getFiltered'], 'users' => ['getFiltered','auth']]];
# $roles[] = ['role' => 'employer', 'allowed methods' => ['chats' => ['getFiltered', 'add'], 'favorites_list' => ['getFiltered', 'add', 'put'], 'response' => ['put', 'getFiltered'], 'resume' => ['getFiltered'], 'vacancy' => ['getFiltered', 'add', 'delete'], 'users' => ['auth']]];
# $roles[] = ['role' => 'admin', 'allowed methods' => '@any'];
### timeneeders
### you can add '@any' if you need all
$timeneeders = ['users']; 
## Don't touch that, please
# $roles[] = ['role' => 'kernel', 'allowed methods' => '@any'];
function ec(bool $value):string
{
    if ($value) return 'true'; else return 'false';
}