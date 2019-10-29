<?php

namespace common\models\entityResponsible;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[IssueEntityResponsible]].
 *
 * @see EntityResponsible
 */
class EntityResponsibleQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return EntityResponsible[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return EntityResponsible|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
