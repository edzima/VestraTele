<?php

namespace common\modules\issue\actions;

use common\models\issue\IssueNote;
use Yii;
use yii\base\Action;
use yii\db\Expression;
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
			$titles = IssueNote::find()
				->select('title')
				->where(['like', 'title', $term])
				->andWhere(['is_template' => true])
				->distinct()
				->limit($this->limit)
				->addOrderBy(new Expression(
					'CASE'
					. " WHEN title like :title THEN 1"
					. " WHEN title like :titleBegin THEN 2"
					. " WHEN title like :titleBetween THEN 4"
					. " ELSE 3 END"
					, [
					'title' => $term,
					'titleBegin' => $term . '%',
					'titleBetween' => '%' . $term . '%',
				]))
				->column();
			sort($titles);
			return $titles;
		}
        return [];
    }
}
