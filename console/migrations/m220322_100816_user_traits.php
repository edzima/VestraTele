<?php

use console\base\Migration;

/**
 * Class m220322_120416_issue_relation
 */
class m220322_100816_user_traits extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->renameTable('{{%customer_trait}}', '{{%user_trait_assign}}');
		$this->createTable('{{%user_trait}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(50)->unique(),
			'show_on_issue_view' => $this->boolean(),
		]);

		$this->batchInsert('{{%user_trait}}', ['id', 'name'], [
			[
				100,
				'Antyvindication',
			],
			[
				150,
				'Bailiff',
			],
			[
				200,
				'Commission Refund',
			],
			[
				300,
				'Disability result of case',
			],
		]);

		$this->addForeignKey('{{%fk_trait_user}}', '{{%user_trait_assign}}',
			'trait_id', '{{%user_trait}}', 'id',
			'CASCADE',
			'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%fk_trait_user}}', '{{%user_trait_assign}}');
		$this->dropTable('{{%user_trait}}');
		$this->renameTable('{{%user_trait_assign}}', '{{%customer_trait}}');
	}

}
