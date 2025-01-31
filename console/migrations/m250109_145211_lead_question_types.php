<?php

use common\modules\lead\models\LeadQuestion;
use console\base\Migration;

/**
 * Class m250109145211_lead_question_types
 */
class m250109_145211_lead_question_types extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_question}}', 'type', $this->string()
			->notNull()
			->defaultValue(LeadQuestion::TYPE_TEXT));
		$this->update('{{%lead_question}}', [
			'type' => LeadQuestion::TYPE_TAG,
		], [
			'placeholder' => null,
			'is_boolean' => false,
		]);
		$this->update('{{%lead_question}}', [
			'type' => LeadQuestion::TYPE_BOOLEAN,
		], [
			'is_boolean' => true,
		]);

		$this->dropColumn('{{%lead_question}}', 'is_boolean');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {

		$this->addColumn('{{%lead_question}}', 'is_boolean', $this->boolean());
		$this->update('{{%lead_question}}', [
			'is_boolean' => false,
		], [
			'type' => LeadQuestion::TYPE_TAG,
		]);
		$this->update('{{%lead_question}}', [
			'is_boolean' => true,
		], [
			'type' => LeadQuestion::TYPE_BOOLEAN,
		]);

		$this->dropColumn('{{%lead_question}}', 'type');
	}

}
