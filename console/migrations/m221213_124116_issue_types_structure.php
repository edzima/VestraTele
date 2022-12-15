<?php

use console\base\Migration;

/**
 * Class m221213_124116_issue_types_structure
 */
class m221213_124116_issue_types_structure extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_type}}', 'parent_id', $this->integer());

		$this->dropColumn('{{%issue_type}}', 'meet');
		$this->dropColumn('{{%issue_type}}', 'provision_type');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->addColumn('{{%issue_type}}', 'provision_type', $this->smallInteger());
		$this->addColumn('{{%issue_type}}', 'meet', $this->boolean());

		$this->dropColumn('{{%issue_type}}', 'parent_id');
	}

}
