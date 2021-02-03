<?php

use common\models\issue\Summon;
use console\base\Migration;

/**
 * Class m210203_134216_add_deadline_to_summon
 */
class m210203_134216_add_deadline_to_summon extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%summon}}', 'deadline_at', $this->dateTime());
		foreach (Summon::find()->andWhere('term IS NOT NULL')->all() as $summon) {
			$summon->deadline_at = date('Y-m-d', strtotime($summon->start_at . " + {$summon->term} days"));
			$summon->save();
		}
		$this->dropColumn('{{%summon}}', 'term');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%summon}}', 'deadline_at');
		$this->addColumn('{{%summon}}', 'term', $this->smallInteger());
	}

}
