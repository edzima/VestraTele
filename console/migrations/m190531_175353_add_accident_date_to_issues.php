<?php

use console\base\Migration;

/**
 * Class m190531_175353_add_accident_date_to_issues
 */
class m190531_175353_add_accident_date_to_issues extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue}}', 'accident_at', $this->timestamp());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue}}', 'accident_at');
	}

}
