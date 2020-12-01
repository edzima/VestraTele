<?php

use console\base\Migration;

/**
 * Class m201119_123859_convert_timestamp_columns
 */
class m201119_123859_convert_timestamp_columns extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->convertTimestampToDatetime('{{%issue}}', 'date', 'signing_at');
		$this->convertTimestampToDatetime('{{%issue}}', 'accident_at');
		$this->convertTimestampToDatetime('{{%issue}}', 'stage_change_at');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		return true;
	}

}
