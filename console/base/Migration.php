<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-03
 * Time: 18:30
 */

namespace console\base;

use yii\db\Expression;
use yii\db\Migration as BaseMigration;

class Migration extends BaseMigration {

	public function createTable($table, $columns, $options = null) {
		if ($this->db->driverName === 'mysql') {
			$options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}
		parent::createTable($table, $columns, $options);
	}

	protected function convertTimestampToDatetime(string $table, string $column, string $newColumn = null) {
		if ($newColumn === null) {
			$newColumn = $column . '_temp';
		}
		$this->addColumn($table, $newColumn, $this->date());
		$this->update($table, [$newColumn => null], [$newColumn => '1970-01-01']);

		$this->update($table, [$newColumn => new Expression('FROM_UNIXTIME(UNIX_TIMESTAMP(' . $column . '))')]);
		if ($newColumn === $column . '_temp') {
			$this->dropColumn($table, $column);
			$this->renameColumn($table, $newColumn, $column);
		} else {
			$this->dropColumn($table, $column);
		}
	}
}
