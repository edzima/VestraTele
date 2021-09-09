<?php

use console\base\Migration;
use yii\db\Expression;

/**
 * Class m210820_113512_issue_note_as_datetime
 */
class m210820_113512_issue_note_publish_pinned_and_template extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%issue_note}}', 'description', $this->text()->null());
		$this->alterColumn('{{%issue_note}}', 'publish_at', $this->dateTime());
		$this->update('{{%issue_note}}', [
			'publish_at' => new Expression('updated_at'),
		], ['publish_at' => null]);
		$this->addColumn('{{%issue_note}}', 'is_pinned', $this->boolean()->notNull()->defaultValue(0));
		$this->addColumn('{{%issue_note}}', 'is_template', $this->boolean()->notNull()->defaultValue(0));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('{{%issue_note}}', 'description', $this->text()->notNull());
		$this->dropColumn('{{%issue_note}}', 'is_pinned');
		$this->dropColumn('{{%issue_note}}', 'is_template');
	}

}
