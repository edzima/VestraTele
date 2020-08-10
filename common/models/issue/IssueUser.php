<?php

namespace app\models;

use common\models\issue\Issue;
use common\models\User;
use Yii;

/**
 * This is the model class for table "issue_user".
 *
 * @property int $user_id
 * @property int $issue_id
 * @property string $type
 *
 * @property Issue $issue
 * @property User $user
 */
class IssueUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'issue_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'issue_id', 'type'], 'required'],
            [['user_id', 'issue_id'], 'integer'],
            [['type'], 'string', 'max' => 255],
            [['user_id', 'issue_id'], 'unique', 'targetAttribute' => ['user_id', 'issue_id']],
            [['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::className(), 'targetAttribute' => ['issue_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'issue_id' => 'Issue ID',
            'type' => 'Type',
        ];
    }

    /**
     * Gets query for [[Issue]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIssue()
    {
        return $this->hasOne(Issue::className(), ['id' => 'issue_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
