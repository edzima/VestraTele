<?php

use yii\db\Migration;

/**
 * Class m201217_204211_add_provider_notified_column_to_calculation
 */
class m201217_204211_add_provider_notified_column_to_calculation extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp(): bool {
		$this->addColumn('{{%issue_pay_calculation}}', 'is_provider_notified', $this->boolean()->null());
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown(): bool {
		$this->dropColumn('{{%issue_pay_calculation}}', 'is_provider_notified');
		return true;
	}

}
