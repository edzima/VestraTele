<?php

use console\base\Migration;

/**
 * Class m200504_115330_add_email_to_meet
 */
class m200504_115330_add_email_to_meet extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->addColumn('{{%issue_meet}}', 'email', $this->string());
		$this->alterColumn('{{%issue_meet}}', 'campaign_id', $this->integer());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_meet}}', 'email');
		$this->alterColumn('{{%issue_meet}}', 'campaign_id', $this->integer()->notNull());
	}

}
