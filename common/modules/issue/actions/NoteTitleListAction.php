<?php

namespace common\modules\issue\actions;

use common\models\issue\IssueNote;
use Yii;
use yii\base\Action;
use yii\web\Response;

class NoteTitleListAction extends Action
{

    public int $minLength = 3;
    public int $limit = 50;

    public function init(): void
    {
        parent::init();
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function run(string $term = null): array
    {
        if (strlen($term) >= $this->minLength) {
            return IssueNote::find()
                ->select('title')
                ->where(['like', 'title', $term . '%', false])
                ->andWhere(['is_template' => true])
                ->distinct()
                ->limit($this->limit)
                ->addOrderBy(['created_at' => SORT_DESC])
                ->column();

        }
        return [];
    }
}
