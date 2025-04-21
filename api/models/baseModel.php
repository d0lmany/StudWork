<?php
class baseModel
{
	private $pdo;
	private $entity;

	public function __construct($pdo, $entity)
	{
		$this->pdo = $pdo;
		$this->entity = $entity;
	}

	public function getById($id)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM `{$this->entity}` WHERE id = ?");
		try {
			$stmt->execute([$id]);
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			if ($e instanceof PDOException) {
				return ["error" => $e->errorInfo];
			}
			return ["error" => "unexpected error occurred: " . $e->getMessage()];
		}
	}

	public function getAll()
	{
		$stmt = $this->pdo->query("SELECT * FROM `{$this->entity}`");
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getFiltered($filters)
	{
		$sql = "SELECT * FROM `{$this->entity}`";
		$where = [];
		$params = [];
		if (!empty($filters)) {
			foreach ($filters as $field => $value) {
				$where[] = "$field = :$field";
				$params[":$field"] = $value;
			}
			if (!empty($where)) {
				$sql .= " WHERE " . implode(' AND ', $where);
			}
		}
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function add($data)
	{
		if (isset($data[HASH_FIELD])) {
			$data[HASH_FIELD] = password_hash($data[HASH_FIELD], HASH_ALGORITHM);
		}
		global $timeneeders;
		if (ENTITIES_NEED_TIME && (in_array($this->entity, $timeneeders) || in_array('@any', $timeneeders))) {
			$time = date('Y-m-d H:i:s');
			$data['created_at'] = $time;
			$data['updated_at'] = $time;
		}
		$columns = implode(',', array_keys($data));
		$placeholders = rtrim(str_repeat('?,', count($data)), ',');
		$stmt = $this->pdo->prepare("INSERT INTO {$this->entity} ($columns) VALUES ($placeholders)");
		$stmt->execute(array_values($data));
		return ["message" => "was added", "id" => $this->pdo->lastInsertId()];
	}

	public function remove($id)
	{
		$stmt = $this->pdo->prepare("DELETE FROM `{$this->entity}` WHERE id = ?");
		$stmt->execute([$id]);
		return $stmt->rowCount();
	}

	public function put($data)
	{
		if (isset($data[HASH_FIELD])) {
			$data[HASH_FIELD] = password_hash($data[HASH_FIELD], HASH_ALGORITHM);
		}
		global $timeneeders;
		if (ENTITIES_NEED_TIME && (in_array($this->entity, $timeneeders) || in_array('@any', $timeneeders))) {
			$time = date('Y-m-d H:i:s');
			$data['updated_at'] = $time;
		}
		$fields = [];
		$params = [':id' => $data['id']];
		foreach ($data as $key => $value) {
			$fields[] = "`$key` = :$key";
			$params[":$key"] = $value;
		}
		$sql = "UPDATE `{$this->entity}` SET " . implode(',', $fields) . " WHERE id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->rowCount();
	}

	public function patch($data)
	{
		if (isset($data[HASH_FIELD])) {
			$data[HASH_FIELD] = password_hash($data[HASH_FIELD], HASH_ALGORITHM);
		}
		global $timeneeders;
		if (ENTITIES_NEED_TIME && (in_array($this->entity, $timeneeders) || in_array('@any', $timeneeders))) {
			$time = ", `updated_at` = '".date('Y-m-d H:i:s')."'";
		} else {$time = "";}
		$key = array_keys($data)[0];
		$value = array_values($data)[0];
		$stmt = $this->pdo->prepare("UPDATE `{$this->entity}` SET `$key` = :value $time WHERE id = :id");
		$stmt->execute(["value" => $value, "id" => $data['id']]);
		return $stmt->rowCount();
	}

	public function search($query, $fields)
	{
		$conditions = [];
		foreach ($fields as $field) {
			$conditions[] = "$field LIKE :query";
		}
		$sql = "SELECT * FROM `{$this->entity}` WHERE " . implode(' OR ', $conditions);
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(['query' => "%$query%"]);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function generateToken($login, $password)
	{
		$stmt = $this->pdo->prepare("SELECT id, " . LOGIN_FIELD . ", " . HASH_FIELD . " FROM " . TOKEN_ENTITY_HOLDER . " WHERE " . LOGIN_FIELD . " = :login");
		$stmt->execute(["login" => $login]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($user && password_verify($password, $user['password'])) {
			$token = bin2hex(random_bytes(32));
			$stmt = $this->pdo->prepare("UPDATE " . TOKEN_ENTITY_HOLDER . " SET " . TOKEN_FIELD_HOLDER . " = :token WHERE id = :id");
			$stmt->execute([':token' => $token, ':id' => $user['id']]);
			return ["token" => $token];
		} else {
			return ["error" => "failed"];
		}
	}

	public function auth($token)
	{
		$stmt = $this->pdo->prepare("SELECT * FROM " . TOKEN_ENTITY_HOLDER . " WHERE " . TOKEN_FIELD_HOLDER . " = :token");
		$stmt->execute(['token' => $token['token']]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($user) {
			return $user;
		} else {
			return false;
		}
	}
}
