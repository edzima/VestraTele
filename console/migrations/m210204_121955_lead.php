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


		$this->createTable('{{%lead}}', [
			'id' => $this->primaryKey(),
			'date_at' => $this->dateTime()->notNull(),
			'data' => $this->json()->notNull(),
			'status_id' => $this->integer()->notNull()->defaultValue(LeadStatusInterface::STATUS_NEW),
			'source_id' => $this->integer()->notNull(),
			'phone' => $this->string(30)->null(),
			'email' => $this->string(40)->null(),
			'postal_code' => $this->string(6)->null(),
			'provider' => $this->string(25),
			'campaign_id' => $this->integer(),
			'owner_id' => $this->integer(),
		]);

		$this->createTable('{{%lead_user}}', [
			'lead_id' => $this->integer()->notNull(),
			'user_id' => $this->integer()->notNull(),
			'type' => $this->string(25)->notNull(),
		]);

		$this->addPrimaryKey('{{%lead_user_PK}}', '{{%lead_user}}', ['lead_id', 'user_id', 'type']);
		/** @var ActiveRecord $userClass */
		$userClass = Module::userClass();
		$this->addForeignKey('{{%fk_lead_user}}', '{{%lead_user}}', 'user_id', $userClass::tableName(), 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_lead_lead}}', '{{%lead_user}}', 'lead_id', '{{%lead}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%lead_type}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(100)->notNull()->unique(),
			'description' => $this->string()->null(),
			'sort_index' => $this->smallInteger(),
		]);

		$this->createTable('{{%lead_source}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(100)->notNull()->unique(),
			'phone' => $this->string(30)->null(),
			'type_id' => $this->integer()->notNull(),
			'url' => $this->string(255),
			'owner_id' => $this->integer(),
			'sort_index' => $this->smallInteger(),
		]);

		$this->addForeignKey('{{%fk_lead_source_type}}', '{{%lead_source}}', 'type_id', '{{%lead_type}}', 'id', 'CASCADE', 'CASCADE');

		$this->addForeignKey('{{%fk_lead_source_owner}}', '{{%lead_source}}', 'owner_id', $userClass::tableName(), 'id', 'CASCADE', 'CASCADE');

		$this->addForeignKey('{{%fk_lead_source_source}}', '{{%lead}}', 'source_id', '{{%lead_source}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%lead_status}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'description' => $this->string()->null(),
			'sort_index' => $this->smallInteger(),
		]);

		$this->addForeignKey('{{%fk_lead_status_status}}', '{{%lead}}', 'status_id', '{{%lead_status}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%lead_campaign}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'sort_index' => $this->smallInteger(),
			'owner_id' => $this->integer(),
			'parent_id' => $this->integer(),
		]);

		$this->addForeignKey('{{%fk_lead_campaign}}', '{{%lead}}', 'campaign_id', '{{%lead_campaign}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_lead_campaign_owner}}', '{{%lead_campaign}}', 'owner_id', $userClass::tableName(), 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_lead_campaign_parent}}', '{{%lead_campaign}}', 'parent_id', '{{%lead_campaign}}', 'id', 'CASCADE', 'CASCADE');

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
		$this->dropTable('{{%lead_user}}');
		$this->dropTable('{{%lead_status}}');
		$this->dropTable('{{%lead_campaign}}');
		$this->dropTable('{{%lead_source}}');
		$this->dropTable('{{%lead_type}}');
	}

}
