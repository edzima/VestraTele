<?php

use yii\db\Migration;

/**
 * Class m200626_102511_remove_pay_cityh
 */
class m200626_102511_remove_pay_city extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropTable('{{%issue_pay_city}}');

		$this->dropForeignKey('fk_issue_pay_city', '{{%issue}}');
		$this->dropColumn('{{%issue}}', 'pay_city_id');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		return true;
	}

}
