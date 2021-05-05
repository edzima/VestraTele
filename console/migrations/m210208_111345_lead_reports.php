<?php

use common\modules\lead\models\ReportSchemaInterface;
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
	public function safeUp(): void {


		$this->createTable('{{%lead_report_schema}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(255)->notNull(),
			'placeholder' => $this->string(255)->null(),
			'is_required' => $this->boolean(),
			'show_in_grid' => $this->boolean()->defaultValue(false),
			'type_id' => $this->integer(),
			'status_id' => $this->integer(),
		]);

		$this->addForeignKey('{{%lead_report_schema_status}}', '{{%lead_report_schema}}', 'status_id', '{{%lead_status}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%lead_report_schema_type}}', '{{%lead_report_schema}}', 'type_id', '{{%lead_type}}', 'id', 'CASCADE', 'CASCADE');

		$this->batchInsert('{{%lead_report_schema}}', [
			'id',
			'name',
			'placeholder',
			'show_in_grid',
			'is_required',
		], [
			[
				'id' => ReportSchemaInterface::FIRSTNAME_ID,
				'name' => 'Firstname',
				'placeholder' => 'Firstname',
				'show_in_grid' => true,
				'is_required' => false,
			],
			[
				'id' => ReportSchemaInterface::LASTNAME_ID,
				'name' => 'Lastname',
				'placeholder' => 'Lastname',
				'show_in_grid' => true,
				'is_required' => false,

			],
		]);

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
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown(): void {
		$this->dropTable('{{%lead_report}}');
		$this->dropTable('{{%lead_report_schema}}');
	}

}
