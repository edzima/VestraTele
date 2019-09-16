<?php

namespace common\models\benefit\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\benefit\BenefitAmount]].
 *
 * @see \common\models\benefit\BenefitAmount
 */
class BenefitAmountQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\benefit\BenefitAmount[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\benefit\BenefitAmount|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
