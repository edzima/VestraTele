<?php

use console\base\Migration;
use edzima\teryt\models\Simc;

/**
 * Class m221107_104116_potential_client
 */
class m221107_104116_potential_client extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%potential_client}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull(),
			'details' => $this->text()->null(),
			'city_id' => $this->integer()->null(),
			'birthday' => $this->date()->null(),
			'status' => $this->smallInteger(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'owner_id' => $this->integer()->notNull(),
			'updater_id' => $this->integer(),
		]);

		$this->addForeignKey('{{%fk_potential_client_owner}}', '{{%potential_client}}', 'owner_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_potential_client_updater}}', '{{%potential_client}}', 'updater_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_potential_client_city}}', '{{%potential_client}}', 'city_id', Simc::tableName(), 'id', 'CASCADE', 'CASCADE');
		$this->createIndex('{{%index_potential_client_status}}', '{{%potential_client}}', 'status');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%potential_client}}');
	}

}
