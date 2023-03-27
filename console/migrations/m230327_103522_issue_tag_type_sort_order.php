<?php

use console\base\Migration;

/**
 * Class m230327_103522_issue_tag_type_position
 */
class m230327_103522_issue_tag_type_sort_order extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_tag_type}}', 'sort_order', $this->smallInteger());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_tag_type}}', 'sort_order');
	}

}
