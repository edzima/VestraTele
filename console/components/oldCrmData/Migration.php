<?php

namespace console\components\oldCrmData;

use yii\db\Migration as BaseMigration;

class Migration extends BaseMigration {

	public const OLD_ID_COLUMN_NAME = 'old_id';

	public function addOldIdColumn(string $table): void {
		$this->addColumn($table, static::OLD_ID_COLUMN_NAME, $this->integer());
	}

	public function dropOldIdColumn(string $table): void {
		$this->dropColumn($table, static::OLD_ID_COLUMN_NAME);
	}
}