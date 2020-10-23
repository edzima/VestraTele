<?php

use yii\db\Migration;

/**
 * Class m201014_121144_add_signature_act_column_to_issue
 */
class m201014_121144_add_signature_act_column_to_issue extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue}}', 'signature_act', $this->string(30)->unique());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue}}', 'signature_act');
	}

}
