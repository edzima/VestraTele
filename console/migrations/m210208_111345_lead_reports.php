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
	public function safeUp(): void {

		$this->createTable('{{%lead_question}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(255)->notNull(),
			'placeholder' => $this->string(255)->null(),
			'is_required' => $this->boolean(),
			'is_active' => $this->boolean(),
			'show_in_grid' => $this->boolean()->defaultValue(false),
			'type_id' => $this->integer(),
			'status_id' => $this->integer(),
		]);

		$this->addForeignKey('{{%lead_question_status}}', '{{%lead_question}}', 'status_id', '{{%lead_status}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%lead_question_type}}', '{{%lead_question}}', 'type_id', '{{%lead_type}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%lead_report}}', [
			'id' => $this->primaryKey(),
			'lead_id' => $this->integer()->notNull(),
			'owner_id' => $this->integer()->notNull(),
			'status_id' => $this->integer()->notNull(),
			'old_status_id' => $this->integer()->notNull(),
			'details' => $this->text(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
		]);

		$this->addForeignKey('{{%fk_lead_report_lead}}', '{{%lead_report}}', 'lead_id', '{{%lead}}', 'id', 'CASCADE', 'CASCADE');
		/** @var ActiveRecord $userClass */
		$userClass = Module::userClass();
		$this->addForeignKey('{{%fk_lead_report_owner}}', '{{%lead_report}}', 'owner_id', $userClass::tableName(), 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_lead_report_status}}', '{{%lead_report}}', 'status_id', '{{%lead_status}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_lead_report_old_status}}', '{{%lead_report}}', 'old_status_id', '{{%lead_status}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%lead_answer}}', [
			'report_id' => $this->integer()->notNull(),
			'question_id' => $this->integer()->notNull(),
			'answer' => $this->string()->null(),
		]);

		$this->addPrimaryKey('{{%PK_lead_answer}}', '{{%lead_answer}}', ['report_id', 'question_id']);
		$this->addForeignKey('{{%fk_lead_answer_report}}', '{{%lead_answer}}', 'report_id', '{{%lead_report}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_lead_answer_question}}', '{{%lead_answer}}', 'question_id', '{{%lead_question}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown(): void {
		$this->dropTable('{{%lead_answer}}');

		$this->dropTable('{{%lead_report}}');
		$this->dropTable('{{%lead_question}}');
	}

}
