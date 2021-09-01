<?php

use console\base\Migration;
use yii\db\Expression;

/**
 * Class m210820_113512_issue_note_as_datetime
 */
class m210820_113512_issue_note_as_datetime extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%issue_note}}', 'publish_at', $this->dateTime());
		$this->update('{{%issue_note}}', [
			'publish_at' => new Expression('updated_at'),
		], ['publish_at' => null]);
		$this->addColumn('{{%issue_note}}', 'is_pinned', $this->boolean()->notNull()->defaultValue(0));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_note}}', 'is_pinned');
	}

}
