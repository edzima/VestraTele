<?php

namespace common\modules\issue\actions;

use common\models\issue\IssueNote;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class NoteDescriptionListAction extends Action {

	public int $minLength = 3;
	public int $limit = 50;

	public function init(): void {
		parent::init();
		Yii::$app->response->format = Response::FORMAT_JSON;
	}

	public function run(string $q = null): array {
		$out = ['results' => []];
		if (strlen($q) >= $this->minLength) {
			$descriptions = IssueNote::find()
				->select('description')
				->where(['like', 'description', $q])
				->distinct()
				->limit($this->limit)
				->addOrderBy(['created_at' => SORT_DESC])
				->column();

			foreach ($descriptions as $description) {
				$out['results'][] = [
					'id' => $description,
					'text' => $description,
				];
			}
		}
		return $out;
	}
}
