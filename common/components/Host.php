<?php

namespace common\components;

use Yii;
use yii\base\BaseObject;
use yii\db\Connection;

class Host extends BaseObject {

	public string $name;
	public ?string $url = null;

	public array $db = [];

	private ?Connection $connection = null;

	public function setDb(Connection $connection) {
		$this->connection = $connection;
	}

	public function getDb(): Connection {
		if ($this->connection === null) {
			/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
			$this->connection = Yii::createObject($this->db);
		}
		return $this->connection;
	}
}
