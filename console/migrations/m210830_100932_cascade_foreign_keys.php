<?php

use console\base\Migration;

/**
 * Class m210830_100932_cascade_foreign_keys
 */
class m210830_100932_cascade_foreign_keys extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropForeignKey('fk_issue_pay_calculation_issue', '{{%issue_pay_calculation}}');
		$this->addForeignKey('{{%fk_issue_pay_calculation_issue}}', '{{%issue_pay_calculation}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {

	}

}
