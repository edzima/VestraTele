<?php

use console\base\Migration;

/**
 * Class m231221_203222_lead_question_order
 */
class m240102_120300_lead_question_order extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_question}}', 'order', $this->smallInteger());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_question}}', 'order');
	}
}
