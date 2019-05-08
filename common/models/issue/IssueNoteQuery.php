<?php

namespace common\models\issue;

/**
 * This is the ActiveQuery class for [[IssueNote]].
 *
 * @see IssueNote
 */
class IssueNoteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return IssueNote[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return IssueNote|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
