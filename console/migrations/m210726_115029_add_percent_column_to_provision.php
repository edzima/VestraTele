<?php

use console\base\Migration;

/**
 * Class m210726_115029_add_percent_column_to_provision
 */
class m210726_115029_add_percent_column_to_provision extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%provision}}', 'percent', $this->decimal(5, 2)->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%provision}}', 'percent');
	}

}
