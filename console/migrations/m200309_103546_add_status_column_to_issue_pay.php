<?php

use yii\db\Migration;

/**
 * Class m200309_103546_add_status_column_to_issue_pay
 */
class m200309_103546_add_status_column_to_issue_pay extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_pay}}', 'status', $this->integer()->notNull());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_pay}}', 'status');

	}

}
