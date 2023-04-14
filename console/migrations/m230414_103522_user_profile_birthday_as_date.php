<?php

use console\base\Migration;

/**
 * Class m230414_103522_user_profile_birthday_as_date
 */
class m230414_103522_user_profile_birthday_as_date extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%user_profile}}', 'birthday', $this->date()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('{{%user_profile}}', 'birthday', $this->integer(11)->null());
	}

}
