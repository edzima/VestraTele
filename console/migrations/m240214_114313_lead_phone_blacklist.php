<?php

use common\modules\lead\Module;
use console\base\Migration;
use yii\db\ActiveRecord;

/**
 * Class m240214114313_lead_phone_blacklist
 */
class m240214_114313_lead_phone_blacklist extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		/** @var ActiveRecord $userClass */
		$userClass = Module::userClass();
		$this->createTable('{{%lead_phone_blacklist}}', [
			'phone' => $this->string(15)->notNull()->unique(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'user_id' => $this->integer(),
		]);
		$this->addPrimaryKey('{{%lead_phone_blacklist_PK}}', '{{%lead_phone_blacklist}}', 'phone');
		$this->addForeignKey('{{%fk_lead_phone_blacklist_user}}', '{{%lead_phone_blacklist}}', 'user_id', $userClass::tableName(), 'id', 'SET NULL', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%lead_phone_blacklist}}');
	}
}
