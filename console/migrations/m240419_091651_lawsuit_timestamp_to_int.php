<?php

use console\base\Migration;

/**
 * Class m240419_091651_lawsuit_timestamp_to_int
 *
 */
class m240419_091651_lawsuit_timestamp_to_int extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%lawsuit}}', 'created_at', $this->timestamp()->defaultExpression('current_timestamp()'));
		$this->alterColumn('{{%lawsuit}}', 'updated_at', $this->timestamp()->defaultExpression('current_timestamp()'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
	}
}
