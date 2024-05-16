<?php

use console\base\Migration;

/**
 * Class m240405_163515_issue_lawsuit_presence_of_plaintiff
 *
 */
class m240405_163515_issue_lawsuit_presence_of_claimant extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lawsuit}}', 'presence_of_the_claimant', $this->smallInteger()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lawsuit}}', 'presence_of_the_claimant');
	}
}
