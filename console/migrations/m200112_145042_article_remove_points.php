<?php

use yii\db\Migration;

/**
 * Class m200112_145042_article_remove_points
 */
class m200112_145042_article_remove_points extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropColumn('{{%article}}', 'point');
		$this->dropColumn('{{%article}}','start_at');
		$this->dropColumn('{{%article}}','finish_at');

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->addColumn('{{%article}}', 'point', $this->decimal(8, 2));
	}

}
