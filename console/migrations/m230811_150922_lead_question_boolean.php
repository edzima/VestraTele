<?php

use console\base\Migration;

/**
 * Class m230811_150922_lead_question_boolean
 */
class m230811_150922_lead_question_boolean extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->addColumn('{{%lead_question}}',
			'is_boolean',
			$this->boolean()->notNull()->defaultValue(0),
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_question}}',
			'is_boolean',
		);
	}
}
