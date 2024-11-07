<?php

use console\base\Migration;
use yii\db\Query;

/**
 * Class m241106_102810_cost_creator
 */
class m241106_102810_cost_creator extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$exist = (new Query())
			->from('{{%issue_cost}}')
			->exists();

		$this->addColumn('{{%issue_cost}}', 'creator_id',
			$exist
				? $this->integer()->null()
				: $this->integer()->notNull()
		);
		$this->addForeignKey(
			'{{%fk_issue_cost_creator}}',
			'{{%issue_cost}}',
			'creator_id',
			'{{%user}}',
			'id',
			'CASCADE',
			'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey(
			'{{%fk_issue_cost_creator}}',
			'{{%issue_cost}}'
		);
		$this->dropColumn('{{%issue_cost}}', 'creator_id');
	}

}
