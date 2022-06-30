<?php

use common\modules\lead\Module;
use console\base\Migration;
use yii\db\ActiveRecord;

/**
 * Class m220614_124116_lead_market
 */
class m220614_124116_lead_market extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {


		/** @var ActiveRecord $userClass */
		$userClass = Module::userClass();

		$this->createTable('{{%lead_market}}', [
			'id' => $this->primaryKey(),
			'lead_id' => $this->integer()->notNull(),
			'status' => $this->smallInteger()->notNull(),
			'details' => $this->text()->null(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'creator_id' => $this->integer()->notNull(),
			'options' => $this->json(),
		]);

		$this->createTable('{{%lead_market_user}}', [
			'market_id' => $this->integer()->notNull(),
			'user_id' => $this->integer()->notNull(),
			'status' => $this->smallInteger()->notNull(),
			'days_reservation' => $this->smallInteger()->notNull(),
			'reserved_at' => $this->date()->null(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'details' => $this->text()->null(),
		]);

		$this->addPrimaryKey('{{%PK_lead_market_user}}', '{{%lead_market_user}}', [
			'market_id',
			'user_id',
		]);

		$this->createIndex('{{%index_lead_market_status}}', '{{%lead_market}}', 'status', false);

		$this->createIndex('{{%index_lead_market_user_status}}', '{{%lead_market_user}}', 'status', false);
		$this->createIndex('{{%index_lead_market_user_reserved_at}}', '{{%lead_market_user}}', 'reserved_at', false);

		$this->addForeignKey('{{%fk_lead_market_creator}}',
			'{{%lead_market}}', 'creator_id',
			$userClass::tableName(), 'id',
			'CASCADE', 'CASCADE'
		);

		$this->addForeignKey('{{%fk_lead_market_lead}}',
			'{{%lead_market}}', 'lead_id',
			'{{%lead}}', 'id',
			'CASCADE', 'CASCADE'
		);

		$this->addForeignKey('{{%fk_lead_market_user_market}}',
			'{{%lead_market_user}}', 'market_id',
			'{{%lead_market}}', 'id',
			'CASCADE', 'CASCADE'
		);

		$this->addForeignKey('{{%fk_lead_market_user_user}}',
			'{{%lead_market_user}}', 'user_id',
			$userClass::tableName(), 'id',
			'CASCADE', 'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%lead_market_user}}');
		$this->dropTable('{{%lead_market}}');
	}

}
