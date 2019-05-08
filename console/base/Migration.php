<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-03-03
 * Time: 18:30
 */

namespace console\base;

use yii\db\Migration as BaseMigration;

class Migration extends BaseMigration {

	public function createTable($table, $columns, $options = null) {
		if ($this->db->driverName === 'mysql') {
			$options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}
		parent::createTable($table, $columns, $options);
	}
}