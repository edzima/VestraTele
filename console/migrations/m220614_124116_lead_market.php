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
		$this->createTable('{{%lead_market}}', [
			'id' => $this->primaryKey(),
			'lead_id' => $this->integer()->notNull(),
			'status' => $this->smallInteger()->notNull(),
			'created_at' => $this->timestamp(),
			'updated_at' => $this->timestamp(),
			'options' => $this->json(),
		]);

		$this->createTable('{{%lead_market_user}}', [
			'id' => $this->primaryKey(),
			'market_id' => $this->integer()->notNull(),
			'lead_id' => $this->integer()->notNull(),
			'user_id' => $this->integer()->notNull(),
			'status' => $this->smallInteger()->notNull(),
			'created_at' => $this->timestamp(),
			'updated_at' => $this->timestamp(),
		]);

		$this->addForeignKey('{{%fk_lead_market_lead}}',
			'{{%lead_market}}', 'lead_id',
			'{{%lead}}', 'id',
			'CASCADE', 'CASCADE'
		);

		$this->addForeignKey('{{%fk_lead_market_user_market}}',
			'{{%lead_market_user}}', 'lead_id',
			'{{%lead_market}}', 'id',
			'CASCADE', 'CASCADE'
		);

		/** @var ActiveRecord $userClass */
		$userClass = Module::userClass();
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
