<?php
require_once './models/baseModel.php';

class baseController
{
    private $model;
    private $entity;
    private $validations;

    public function __construct($pdo, $entity, $validations)
    {
        $this->model = new baseModel($pdo, $entity);
        $this->entity = $entity;
        $this->validations = $validations;
    }

    public function getOne($id)
    {
        if (ALLOW_GET) {
            $permissionCheck = $this->checkPermission('getOne');
            if ($permissionCheck !== true) {
                return $permissionCheck;
            }
            $result = $this->model->getById($id);
            if ($result) {
                return $result;
            } else {
                return ["error" => "not found"];
            }
        } else {
            return ["error" => "forbidden"];
        }
    }

    public function getAll()
    {
        if (ALLOW_GET_ALL) {
            $permissionCheck = $this->checkPermission('getAll');
            if ($permissionCheck !== true) {
                return $permissionCheck;
            }
            $result = $this->model->getAll();
            if ($result) {
                return $result;
            } else {
                return ["message" => "is clear"];
            }
        } else {
            return ["error" => "forbidden"];
        }
    }

    public function getFiltered($data)
    {
        $permissionCheck = $this->checkPermission('getFiltered');
        if ($permissionCheck !== true) {
            return $permissionCheck;
        }
        try {
            return $this->model->getFiltered($data);
        } catch (Exception $e) {
            if ($e instanceof PDOException) {
                return ["error" => $e->errorInfo];
            }
            return ["error" => "unexpected error occurred: " . $e->getMessage()];
        }
    }

    public function add($data)
    {
        if (empty($data)) {
            return ["error" => "no data"];
        }
        $permissionCheck = $this->checkPermission('add');
        if ($permissionCheck !== true) {
            return $permissionCheck;
        }
        try {
            $valid = $this->validate($data);
            if ($valid !== true) {
                return $valid;
            }
            $result = $this->model->add($data);
            return $result;
        } catch (Exception $e) {
            if ($e instanceof PDOException) {
                return ["error" => $e->errorInfo];
            }
            return ["error" => "unexpected error occurred: " . $e->getMessage()];
        }
    }

    public function delete($id)
    {
        $permissionCheck = $this->checkPermission('delete');
        if ($permissionCheck !== true) {
            return $permissionCheck;
        }
        $result = $this->model->remove($id);
        if ($result > 0) {
            return ["message" => "is deleted"];
        } else {
            return ["error" => "not found"];
        }
    }

    public function put($id, $data)
    {
        if (empty($data)) {
            return ["error" => "no data"];
        }
        $permissionCheck = $this->checkPermission('put');
        if ($permissionCheck !== true) {
            return $permissionCheck;
        }
        $data['id'] = $id;
        try {
            $valid = $this->validate($data);
            if ($valid !== true) {
                return $valid;
            }
            $result = $this->model->put($data);
            if ($result > 0) {
                return ["message" => "is updated"];
            }
            return ["error" => "not found"];
        } catch (Exception $e) {
            if ($e instanceof PDOException) {
                return ["error" => $e->errorInfo];
            }
            return ["error" => "unexpected error occurred: " . $e->getMessage()];
        }
    }

    public function patch($id, $data)
    {
        if (empty($data)) {
            return ["error" => "no data"];
        }
        $permissionCheck = $this->checkPermission('patch');
        if ($permissionCheck !== true) {
            return $permissionCheck;
        }
        $data['id'] = $id;
        try {
            $valid = $this->validate($data);
            if ($valid !== true) {
                return $valid;
            }
            $result = $this->model->patch($data);
            if ($result > 0) {
                return ["message" => "is updated"];
            }
            return ["error" => "not found"];
        } catch (Exception $e) {
            if ($e instanceof PDOException) {
                return ["error" => $e->errorInfo];
            }
            return ["error" => "unexpected error occurred: " . $e->getMessage()];
        }
    }

    private function validate($data)
    {
        if (IGNORE_VALIDATION) {
            return true;
        } else {
            $validations = [];
            foreach ($this->validations as $row) {
                if ($row['entity'] == $this->entity || $row['entity'] == '@any') {
                    $validations[] = $row;
                }
            }
            if (empty($validations)) {
                return true;
            }
            foreach ($validations as $row) {
                $field = $row['field'];
                $expression = $row['expression'];
                if (!array_key_exists($field, $data)) {
                    if (ALLOW_MISSING_FIELDS) {
                        continue;
                    } else {
                        return ["error" => "field '$field' is missing"];
                    }
                }
                if ($expression != '@text') {
                    if (!preg_match($expression, $data[$field])) {
                        return ["error" => "string '{$data[$field]}' was not validated for field '$field'"];
                    }
                } else {
                    $data[$field] = htmlspecialchars($data[$field]);
                }
            }
            return true;
        }
    }

    public function search($data)
    {
        if (empty($data)) {
            return ["error" => "no data"];
        }
        $permissionCheck = $this->checkPermission('search');
        if ($permissionCheck !== true) {
            return $permissionCheck;
        }
        try {
            return $this->model->search($data['query'], $data['fields'] ?? ['name', 'description']);
        } catch (Exception $e) {
            if ($e instanceof PDOException) {
                return ["error" => $e->errorInfo];
            }
            return ["error" => "unexpected error occurred: " . $e->getMessage()];
        }
    }

    public function generateToken($data)
    {
        if (empty($data) || (!isset($data[LOGIN_FIELD]) || !isset($data[HASH_FIELD]))) {
            return ["error" => "no data"];
        }
        if (!METHOD_GENERATE_TOKEN_FOR_ALL) {
            $permissionCheck = $this->checkPermission('generateToken');
            if ($permissionCheck !== true) {
                return $permissionCheck;
            }
        }
        try {
            $result = $this->model->generateToken($data[LOGIN_FIELD], $data[HASH_FIELD]);
            if ($result) {
                return $result;
            } else {
                return ["error" => "not found"];
            }
        } catch (Exception $e) {
            if ($e instanceof PDOException) {
                return ["error" => $e->errorInfo];
            }
            return ["error" => "unexpected error occurred: " . $e->getMessage()];
        }
    }

    public function auth($token)
    {
        if (empty($token)) {
            return ["error" => "no data"];
        }
        $permissionCheck = $this->checkPermission('auth');
        if ($permissionCheck !== true) {
            return $permissionCheck;
        }
        try {
            $result = $this->model->auth($token);
            if ($result) {
                return $result;
            } else {
                return ["error" => "unauthorized"];
            }
        } catch (Exception $e) {
            if ($e instanceof PDOException) {
                return ["error" => $e->errorInfo];
            }
            return ["error" => "unexpected error occurred: " . $e->getMessage()];
        }
    }

    private function getTokenFromHeader()
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    private function checkPermission($method)
    {
        if (IGNORE_ROLES) {
            return true;
        }
        $entity = $this->entity;
        $token = $this->getTokenFromHeader();
        if (empty($token)) {
            return ["error" => "unauthorized", "token" => $token, "reason" => "need a token"];
        }
        $user = $this->model->auth($token);
        if (!$user) {
            return ["error" => "unauthorized", "reason" => "token holder not found"];
        }
        $role = $user[ROLE_FIELD];
        global $roles;

        foreach ($roles as $roleConfig) {
            if ($roleConfig['role'] === $role) {
                if ($roleConfig['allowed methods'] === '@any') {
                    return true;
                }
                if (isset($roleConfig['allowed methods'][$entity])) {
                    if (in_array($method, $roleConfig['allowed methods'][$entity])) {
                        return true;
                    }
                }
                break;
            }
        }
        return ["error" => "forbidden"];
    }
}
