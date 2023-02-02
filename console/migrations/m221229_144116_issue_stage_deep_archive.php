<?php

use common\models\issue\IssueStage;
use console\base\Migration;

/**
 * Class m221229_144116_issue_stage_deep_archive
 */
class m221229_144116_issue_stage_deep_archive extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->upsert('{{%issue_stage}}', [
			'id' => IssueStage::ARCHIVES_DEEP_ID,
			'name' => 'Deep Archive',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {

	}
}
