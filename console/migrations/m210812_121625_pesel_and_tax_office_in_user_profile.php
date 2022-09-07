<?php

use console\base\Migration;

/**
 * Class m210812_121625_pesel_and_tax_office_in_user_profile
 */
class m210812_121625_pesel_and_tax_office_in_user_profile extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%user_profile}}', 'tax_office', $this->string(100));
		$this->addColumn('{{%user_profile}}', 'pesel', $this->string(11));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%user_profile}}', 'tax_office');
		$this->dropColumn('{{%user_profile}}', 'pesel');
	}

}
