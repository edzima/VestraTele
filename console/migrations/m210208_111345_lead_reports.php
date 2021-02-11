<?php

use common\modules\lead\Module;
use console\base\Migration;
use yii\db\ActiveRecord;

/**
 * Class m210208_111345_lead_reports
 */
class m210208_111345_lead_reports extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%lead_report_schema}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(255)->notNull(),
			'placeholder' => $this->string(255)->null(),
		]);

		$this->createTable('{{%lead_report_schema_status_type}}', [
			'schema_id' => $this->integer()->notNull(),
			'status_id' => $this->integer()->notNull(),
			'type_id' => $this->integer()->notNull(),
		]);

		$this->addPrimaryKey('{{%PK_lead_report_schema_status_type}}', '{{%lead_report_schema_status_type}}', ['schema_id', 'status_id', 'type_id']);

		$this->addForeignKey('{{%lead_report_schema_status_type_schema}}', '{{%lead_report_schema_status_type}}', 'schema_id', '{{%lead_report_schema}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%lead_report_schema_status_type_status}}', '{{%lead_report_schema_status_type}}', 'status_id', '{{%lead_status}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%lead_report_schema_status_type_type}}', '{{%lead_report_schema_status_type}}', 'type_id', '{{%lead_type}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%lead_report}}', [
			'id' => $this->primaryKey(),
			'lead_id' => $this->integer()->notNull(),
			'owner_id' => $this->integer()->notNull(),
			'status_id' => $this->integer()->notNull(),
			'old_status_id' => $this->integer()->notNull(),
			'schema_id' => $this->integer()->notNull(),
			'details' => $this->string(255)->null(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
		]);

		$this->addForeignKey('{{%fk_lead_report_lead}}', '{{%lead_report}}', 'lead_id', '{{%lead}}', 'id', 'CASCADE', 'CASCADE');
		/** @var ActiveRecord $userClass */
		$userClass = Module::userClass();
		$this->addForeignKey('{{%fk_lead_report_owner}}', '{{%lead_report}}', 'owner_id', $userClass::tableName(), 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_lead_report_status}}', '{{%lead_report}}', 'status_id', '{{%lead_status}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_lead_report_old_status}}', '{{%lead_report}}', 'old_status_id', '{{%lead_status}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_lead_report_schema}}', '{{%lead_report}}', 'schema_id', '{{%lead_report_schema}}', 'id', 'CASCADE', 'CASCADE');

		$this->addColumn('{{%lead}}', 'owner_id', $this->integer()->null());
		$this->addForeignKey('{{%lead_owner}}', '{{%lead}}', 'owner_id', $userClass::tableName(), 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%lead_owner}}', '{{%lead}}');
		$this->dropColumn('{{%lead}}', 'owner_id');

		$this->dropTable('{{%lead_report}}');
		$this->dropTable('{{%lead_report_schema_status_type}}');
		$this->dropTable('{{%lead_report_schema}}');
	}

}
