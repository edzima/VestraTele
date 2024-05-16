<?php

use console\base\Migration;

/**
 * Class m240308_143415_provision_type_name_length
 */
class m240308_143415_provision_type_name_length extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%provision_type}}', 'name', $this->string(255)->unique());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
	}
}
