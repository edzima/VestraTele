<?php

use console\base\Migration;

/**
 * Class m241021_161511_lawsuit_rul
 */
class m241021_161511_lawsuit_rul extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lawsuit}}', 'url', $this->string(255)->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lawsuit}}', 'url');
	}

}
