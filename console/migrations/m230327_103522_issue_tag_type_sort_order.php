<?php

use console\base\Migration;

/**
 * Class m230327_103522_issue_tag_type_position
 *
 * @todo move to base issue_tag_migration before merge with master.
 */
class m230327_103522_issue_tag_type_sort_order extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_tag_type}}', 'sort_order', $this->smallInteger());
		$this->addColumn('{{%issue_tag_type}}', 'link_issues_grid_position', $this->string()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_tag_type}}', 'sort_order');
		$this->dropColumn('{{%issue_tag_type}}', 'link_issues_grid_position');
	}

}
