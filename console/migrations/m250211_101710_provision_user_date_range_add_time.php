<?php

use console\base\Migration;
use yii\db\Expression;

/**
 * Class m250211_101710_provision_user_date_range_add_time
 */
class m250211_101710_provision_user_date_range_add_time extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp(): void {
		$this->alterColumn('{{%provision_user}}', 'from_at', $this->dateTime());
		$this->alterColumn('{{%provision_user}}', 'to_at', $this->dateTime());
		$this->update('{{%provision_user}}', [
			'to_at' => new Expression("ADDTIME(to_at,'23:59:59')"),
		], 'to_at IS NOT NULL');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown(): void {
		$this->alterColumn('{{%provision_user}}', 'from_at', $this->date());
		$this->alterColumn('{{%provision_user}}', 'to_at', $this->date());
	}

}
