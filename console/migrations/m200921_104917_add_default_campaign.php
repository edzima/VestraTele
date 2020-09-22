<?php

use console\base\Migration;

/**
 * Class m200921_104917_add_default_campaign
 */
class m200921_104917_add_default_campaign extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%campaign}}', 'default', $this->boolean());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%campaign}}', 'default');
	}

}
