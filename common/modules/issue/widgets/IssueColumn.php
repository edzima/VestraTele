<?php

namespace common\modules\issue\widgets;

use kartik\grid\DataColumn;
use Yii;
use yii\helpers\Html;

class IssueColumn extends DataColumn {

	public $attribute = 'issue_id';
	public $value = 'issue.longId';

	public string $issueAttribute = 'issue';

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
				return Html::a($model->{$this->issueAttribute}->longId, [$this->viewBaseUrl, 'id' => $model->{$this->attribute}], $this->linkOptions);
			};
		}
		parent::init();
	}
}
