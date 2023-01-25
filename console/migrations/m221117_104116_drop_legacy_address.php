<?php

use console\base\Migration;
use yii\helpers\Console;

/**
 * Class m221117_104116_drop_legacy_address
 */
class m221117_104116_drop_legacy_address extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->tryDrop('miasta');
		$this->tryDrop('wojewodztwa');
		$this->tryDrop('powiaty');
		$this->tryDrop('terc');
	}

	private function tryDrop(string $table): void {
		try {
			$this->dropTable($table);
		} catch (Exception $e) {
			Console::output($e->getMessage());
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
	}

}
