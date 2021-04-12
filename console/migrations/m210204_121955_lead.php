<?php

use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\Module;
use console\base\Migration;
use yii\db\ActiveRecord;

/**
 * Class m210204_121955_lead
 */
class m210204_121955_lead extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		/** @var ActiveRecord $userClass */
		$userClass = Module::userClass();
		$this->createTable('{{%lead}}', [
			'id' => $this->primaryKey(),
			'date_at' => $this->dateTime()->notNull(),
			'data' => $this->json()->notNull(),
			'phone' => $this->string(30)->null(),
			'email' => $this->string(40)->null(),
			'postal_code' => $this->string(6)->null(),
			'status_id' => $this->integer()->notNull()->defaultValue(LeadStatusInterface::STATUS_NEW),
			'source_id' => $this->integer()->notNull(),
			'type_id' => $this->integer()->notNull(),
			'owner_id' => $this->integer(),
		]);

		$this->addForeignKey('{{%fk_lead_owner}}', '{{%lead}}', 'owner_id', $userClass::tableName(), 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%lead_type}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull(),
			'description' => $this->string()->null(),
			'sort_index' => $this->smallInteger(),
		]);

		$this->addForeignKey('{{%fk_lead_type}}', '{{%lead}}', 'type_id', '{{%lead_type}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%lead_source}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(100)->notNull(),
			'url' => $this->string(255),
			'sort_index' => $this->smallInteger(),
			'owner_id' => $this->integer(),
		]);
		$this->addForeignKey('{{%fk_lead_source_owner}}', '{{%lead_source}}', 'owner_id', $userClass::tableName(), 'id', 'CASCADE', 'CASCADE');

		$this->addForeignKey('{{%fk_lead_source_source}}', '{{%lead}}', 'source_id', '{{%lead_source}}', 'id', 'CASCADE', 'CASCADE');

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
		$this->dropTable('{{%lead_source}}');
		$this->dropTable('{{%lead_status}}');
		$this->dropTable('{{%lead_type}}');
	}

}
