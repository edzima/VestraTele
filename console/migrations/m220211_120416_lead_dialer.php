<?php

use common\modules\lead\Module;
use console\base\Migration;
use yii\db\ActiveRecord;

/**
 * Class m220113_131816_lead_source-dialer_phone
 */
class m220211_120416_lead_dialer extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_status}}', 'not_for_dialer', $this->boolean()->defaultValue(1));

		$this->createTable('{{%lead_dialer_type}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'status' => $this->smallInteger()->notNull(),
			'user_id' => $this->integer()->notNull(),
		]);
		/** @var ActiveRecord $userClass */
		$userClass = Module::userClass();
		$this->addForeignKey(
			'{{%lead_dialer_type_user}}',
			'{{%lead_dialer_type}}',
			'user_id',
			$userClass::tableName(),
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->createTable('{{%lead_dialer}}', [
			'id' => $this->primaryKey(),
			'lead_id' => $this->integer()->notNull(),
			'type_id' => $this->integer()->notNull(),
			'priority' => $this->smallInteger(),
			'created_at' => $this->timestamp()->notNull(),
			'updated_at' => $this->timestamp()->notNull(),
			'status' => $this->smallInteger()->notNull(),
			'last_at' => $this->timestamp(),
			'dialer_config' => $this->json(),
		]);

		$this->addForeignKey(
			'{{%fk_lead_dialer_lead}}',
			'{{%lead_dialer}}',
			'lead_id',
			'{{%lead}}',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKey(
			'{{%fk_lead_dialer_type}}',
			'{{%lead_dialer}}',
			'type_id',
			'{{%lead_dialer_type}}',
			'id',
			'CASCADE',
			'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_status}}', 'not_for_dialer');

		$this->dropTable('{{%lead_dialer}}');
		$this->dropTable('{{%lead_dialer_type}}');
	}

}
