<?php

use yii\db\Migration;

/**
 * Class m200204_111518_issue_type_meet_flag
 */
class m200204_111518_issue_type_meet_flag extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_type}}', 'meet', $this->boolean());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_type}}', 'meet');
	}

}
