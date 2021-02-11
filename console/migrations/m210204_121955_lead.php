<?php

use common\modules\lead\models\LeadStatusInterface;
use console\base\Migration;

/**
 * Class m210204_121955_lead
 */
class m210204_121955_lead extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%lead}}', [
			'id' => $this->primaryKey(),
			'date_at' => $this->dateTime()->notNull(),
			'source' => $this->string()->notNull(),
			'data' => $this->json()->notNull(),
			'phone' => $this->string(30)->null(),
			'email' => $this->string(40)->null(),
			'postal_code' => $this->string(6)->null(),
			'status_id' => $this->integer()->notNull()->defaultValue(LeadStatusInterface::STATUS_NEW),
			'type_id' => $this->integer()->notNull(),
		]);

		$this->createTable('{{%lead_type}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull(),
			'description' => $this->string()->null(),
			'sort_index' => $this->smallInteger(),
		]);

		$this->addForeignKey('{{%fk_lead_type}}', '{{%lead}}', 'type_id', '{{%lead_type}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%lead_status}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull(),
			'description' => $this->string()->null(),
			'sort_index' => $this->smallInteger(),
		]);

		$this->addForeignKey('{{%fk_lead_status_status}}', '{{%lead}}', 'status_id', '{{%lead_status}}', 'id', 'CASCADE', 'CASCADE');

		$this->batchInsert('{{%lead_status}}', ['id', 'name'], [
			[
				'id' => LeadStatusInterface::STATUS_NEW,
				'name' => 'New',
			],
			[
				'id' => LeadStatusInterface::STATUS_ARCHIVE,
				'name' => 'Archive',
			],
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%lead}}');
		$this->dropTable('{{%lead_status}}');
	}

}
