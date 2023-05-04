<?php

use console\base\Migration;

/**
 * Class m230426_153522_potential_client_add_phone_column
 */
class m230426_153522_potential_client_add_phone_column extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%potential_client}}', 'phone', $this->string(20)->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%potential_client}}', 'phone');
	}

}
