<?php

use yii\db\Migration;

/**
 * Class m200225_152043_add_date_end_at_to_meet
 */
class m200225_152043_add_date_end_at_to_meet extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_meet}}', 'date_end_at', $this->dateTime());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_meet}}', 'date_end_at');
	}

}
