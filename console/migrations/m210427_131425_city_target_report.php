<?php

use console\base\Migration;
use edzima\teryt\models\Simc;

/**
 * Class m210427_131425_city_target_report
 */
class m210427_131425_city_target_report extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp(): void {
		$this->createTable('{{%hint_city}}', [
			'id' => $this->primaryKey(),
			'user_id' => $this->integer()->notNull(),
			'city_id' => $this->integer()->notNull(),
			'type' => $this->string(20)->notNull(),
			'status' => $this->string(30)->notNull(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'details' => $this->text(),
		]);

		$this->createIndex('{{%index_hint_city_status}}', '{{%hint_city}}', 'status');
		$this->createIndex('{{%index_hint_city_type}}', '{{%hint_city}}', 'type');

		$this->addForeignKey('{{%fk_hint_city_city}}', '{{%hint_city}}', 'city_id', Simc::tableName(), 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_hint_city_user}}', '{{%hint_city}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%hint_source}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'short_name' => $this->string()->notNull()->unique(),
			'is_active' => $this->boolean(),
		]);

		$this->createTable('{{%hint_city_source}}', [
			'source_id' => $this->integer()->notNull(),
			'hint_id' => $this->integer()->notNull(),
			'phone' => $this->string(50)->notNull(),
			'rating' => $this->string(50)->notNull(),
			'status' => $this->string(30)->notNull(),
			'details' => $this->text(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
		]);

		$this->createIndex('{{%index_hint_city_source_rating}}', '{{%hint_city_source}}', 'rating');
		$this->createIndex('{{%index_hint_city_source_status}}', '{{%hint_city_source}}', 'status');

		$this->addPrimaryKey('{{%pk_hint_city_source}}', '{{%hint_city_source}}', ['hint_id', 'source_id']);
		$this->addForeignKey('{{%fk_hint_city_hint}}', '{{%hint_city_source}}', 'hint_id', '{{%hint_city}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_hint_city_source}}', '{{%hint_city_source}}', 'source_id', '{{%hint_source}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown(): void {
		$this->dropTable('{{%hint_city_source}}');
		$this->dropTable('{{%hint_source}}');
		$this->dropTable('{{%hint_city}}');
	}

}
