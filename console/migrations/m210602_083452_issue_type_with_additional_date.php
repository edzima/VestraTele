<?php

use console\base\Migration;

/**
 * Class m210602_083452_issue_type_with_additional_date
 */
class m210602_083452_issue_type_with_additional_date extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_type}}', 'with_additional_date', $this->boolean());
		$this->renameColumn('{{%issue}}', 'accident_at', 'type_additional_date_at');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->renameColumn('{{%issue}}', 'type_additional_date_at', 'accident_at');
		$this->dropColumn('{{%issue_type}}', 'with_additional_date');
	}

}
