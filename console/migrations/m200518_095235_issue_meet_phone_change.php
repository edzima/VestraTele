<?php

use yii\db\Migration;

/**
 * Class m200518_095235_issue_meet_phone_change
 */
class m200518_095235_issue_meet_phone_change extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%issue_meet}}', 'phone', $this->string(20)->null());
		$this->alterColumn('{{%issue}}','client_phone_1', $this->string(20)->null());
		$this->alterColumn('{{%issue}}','client_phone_2', $this->string(20)->null());
		$this->alterColumn('{{%issue}}','victim_phone', $this->string(20)->null());

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {

	}

}
