<?php

use console\base\Migration;

/**
 * Class m220418_160416_note_template_index
 */
class m220418_160416_note_template_index extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('{{%issue_note_template_index}}', '{{%issue_note}}', 'is_template');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%issue_note_template_index}}', '{{%issue_note}}');
    }

}
