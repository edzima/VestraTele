<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property string $comment
 * @property int $author_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $cause_id
 *
 * @property User $author
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment', 'author_id', 'cause_id'], 'required'],
            [['author_id', 'created_at', 'updated_at', 'cause_id'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'comment' => 'Comment',
            'author_id' => 'Author ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'cause_id' => 'Cause ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }
}
