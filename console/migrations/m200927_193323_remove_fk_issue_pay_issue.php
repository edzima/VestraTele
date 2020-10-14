<?php

use yii\db\Migration;

/**
 * Class m200927_193323_remove_fk_issue_pay_issue
 */
class m200927_193323_remove_fk_issue_pay_issue extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropForeignKey('fk_issue_pay_issue', '{{%issue_pay}}');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->addForeignKey('fk_issue_pay_issue', '{{%issue_pay}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');
	}
}
