<?php

use yii\db\Migration;

/**
 * Handles the creation of table `comment`.
 */
class m170505_172535_create_comment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('cause_category', [
            'id' => $this->smallInteger()->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'period' =>$this->smallInteger()->notNull(),
            'color' =>$this->string(7)
        ], $tableOptions);


        $this->createTable('cause', [
            'id' => $this->primaryKey(),
            'victim_name' => $this->string(255)->notNull(),
            'author_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'date' => $this->integer()->notNull(),
            'category_id' => $this->smallInteger()->notNull(),
            'is_finished' => $this->boolean()->defaultValue(false),
         ], $tableOptions);




        //cause author

        // creates index for column `author_id`
        $this->createIndex(
            'idx-cause-author_id',
            'cause',
            'author_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-cause-author_id',
            'cause',
            'author_id',
            'user',
            'id',
            'CASCADE'
        );


        // creates index for column `category_id`
        $this->createIndex(
            'idx-cause-category_id',
            'cause',
            'category_id'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-cause-category_id',
            'cause',
            'category_id',
            'cause_category',
            'id',
            'CASCADE'
        );

    }

    /**
     * @inheritdoc
     */
    public function down()
    {

        // drops foreign key for table `category`
        $this->dropForeignKey(
            'fk-cause-category_id',
            'cause'
        );

        // drops index for column `category_id`
        $this->dropIndex(
            'idx-cause-category_id',
            'cause'
        );
        $this->dropTable('cause');
        $this->dropTable('cause-category');
    }
}
