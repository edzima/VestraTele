<?php

namespace common\models\issue;

/**
 * This is the ActiveQuery class for [[IssueEntityResponsible]].
 *
 * @see IssueEntityResponsible
 */
class IssueEntityResponsibleQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return IssueEntityResponsible[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return IssueEntityResponsible|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
