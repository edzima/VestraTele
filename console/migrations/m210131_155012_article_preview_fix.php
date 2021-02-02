<?php

use yii\db\Migration;

/**
 * Class m210131_155012_article_preview_fix
 */
class m210131_155012_article_preview_fix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%article}}', 'preview', $this->text()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		return true;
    }

}
