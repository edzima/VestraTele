<?php

use console\base\Migration;

class m190204_131008_add_boss_and_phone_fields_for_users extends Migration {

	public function up() {
		$this->addColumn('{{%user}}', 'boss', $this->integer());
		$this->addColumn('{{%user_profile}}', 'phone', $this->string());
	}

	public function down() {
		$this->dropColumn('{{%user}}', 'boss');
		$this->dropColumn('{{%user_profile}}', 'phone');
	}

}
