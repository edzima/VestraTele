<?php

namespace common\widgets\grid;

use common\models\issue\Issue;
use kartik\grid\DataColumn;
use Yii;
use yii\base\Model;
use yii\helpers\Html;

class IssueColumn extends DataColumn {

	public $attribute = 'issue_id';
	public $value = 'issue.longId';

	public ?string $issueAttribute = 'issue';

	public array $linkOptions = [
		'target' => '_blank',
	];

	public string $viewBaseUrl;

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('common', 'Issue');
		}
		if (!empty($this->linkOptions)) {
			$this->format = 'raw';
			$this->value = function ($model): string {
				return Html::a(
					$this->getLinkText($model),
					[$this->viewBaseUrl, 'id' => $this->getId($model)],
					$this->linkOptions);
			};
		}
		$this->options['style'] = 'width:100px';
		parent::init();
	}

	public function getLinkText(Model $model): string {
		if ($this->issueAttribute === null) {
			/** @var $model Issue */
			return $model->longId;
		}
		return $model->{$this->issueAttribute}->longId;
	}

	public function getId(Model $model): int {
		if ($this->issueAttribute === null) {
			/** @var $model Issue */
			return $model->id;
		}
		return $model->{$this->attribute};
	}
}
