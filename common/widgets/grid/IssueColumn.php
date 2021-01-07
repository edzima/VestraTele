<?php

namespace common\widgets\grid;

use common\models\issue\IssueInterface;
use Yii;
use yii\helpers\Html;

class IssueColumn extends DataColumn {

	public $noWrap = true;

	public ?string $issueAttribute = 'issue';
	public $attribute = 'issue_id';

	public array $linkOptions = [
		'target' => '_blank',
	];

	public ?string $viewBaseUrl = null;

	public function init(): void {
		if (empty($this->label)) {
			$this->label = Yii::t('common', 'Issue');
		}
		if (!empty($this->viewBaseUrl) && !empty($this->linkOptions)) {
			$this->format = 'raw';
			$this->value = function (IssueInterface $model): string {
				return Html::a(
					$model->getIssueName(),
					[$this->viewBaseUrl, 'id' => $model->getIssueId()],
					$this->linkOptions);
			};
		}
		$this->options['style'] = 'width:100px';
		parent::init();
	}

	public function getLinkText(IssueInterface $model): string {
		return $model->getIssueName();
	}

	public function getId(IssueInterface $model): int {
		return $model->getIssueId();
	}
}
