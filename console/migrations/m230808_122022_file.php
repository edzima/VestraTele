<?php

use console\base\Migration;

/**
 * Class m230808_122022_file
 */
class m230808_122022_file extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%file}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull(),
			'hash' => $this->string()->notNull(),
			'size' => $this->integer()->notNull(),
			'type' => $this->string()->notNull(),
			'mime' => $this->string()->notNull(),
			'file_type_id' => $this->integer()->notNull(),
			'created_at' => $this->timestamp()->notNull(),
			'updated_at' => $this->timestamp()->notNull(),
			'owner_id' => $this->integer()->notNull(),
			'path' => $this->string()->notNull(),
		]);

		$this->createTable('{{%file_type}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'is_active' => $this->boolean()->notNull()->defaultValue(1),
			'visibility' => $this->string()->notNull(),
			'validator_config' => $this->json()->notNull(),
		]);

		$this->createTable('{{%file_access}}', [
			'file_id' => $this->integer()->notNull(),
			'user_id' => $this->integer()->notNull(),
			'access' => $this->string()->notNull(),
		]);

		$this->addPrimaryKey('{{%PK_file_access}}', '{{%file_access}}', [
			'file_id',
			'user_id',
		]);

		$this->addForeignKey('{{%FK_file_type}}', '{{%file}}',
			'file_type_id', '{{%file_type}}', 'id',
			'CASCADE',
			'CASCADE');

		$this->addForeignKey('{{%FK_file_owner}}', '{{%file}}',
			'owner_id', '{{%user}}', 'id',
			'RESTRICT',
			'CASCADE');

		$this->addForeignKey('{{%FK_file_access_user}}', '{{%file_access}}',
			'user_id', '{{%user}}', 'id',
			'CASCADE',
			'CASCADE');

		$this->addForeignKey('{{%FK_file_access_file}}', '{{%file_access}}',
			'file_id', '{{%file}}', 'id',
			'CASCADE',
			'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%file_access}}');
		$this->dropTable('{{%file}}');
		$this->dropTable('{{%file_type}}');
	}
}
