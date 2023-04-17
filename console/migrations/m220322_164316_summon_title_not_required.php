<?php

use console\base\Migration;

/**
 * Class m300322_164316_summon_title_not_required
 */
class m220322_164316_summon_title_not_required extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%summon}}', 'title', $this->string(255)->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('{{%summon}}', 'title', $this->string(255)->notNull());
	}

}
