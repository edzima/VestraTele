<?php

namespace common\modules\issue\widgets;

use common\assets\TooltipAsset;
use common\helpers\Html;
use common\models\issue\IssueClaim;
use common\models\issue\IssueInterface;
use common\models\user\Worker;
use common\widgets\grid\DataColumn;
use Yii;
use function array_filter;

class IssueClaimCompanyColumn extends DataColumn {

	public $noWrap = true;
	public bool $contentCenter = true;
	public $format = 'html';
	public bool $tooltip = true;

	public string $notEmptyContentClass = 'success';

	public function init(): void {
		parent::init();
		if (empty($this->label)) {
			$this->label = Yii::t('issue', 'Company Claim Trying Value');
		}
		$this->visible = Yii::$app->user->can(Worker::PERMISSION_ISSUE_CLAIM);
		if ($this->visible) {
			$hasCompanyClaims = !empty(array_filter($this->grid->dataProvider->getModels(), static function (IssueInterface $issue) {
				$claims = $issue->getIssueModel()->claims;
				return !empty(array_filter($claims, static function (IssueClaim $claim) {
					return $claim->isCompany() && !empty($claim->trying_value);
				}));
			}));
			$this->visible = $hasCompanyClaims;
		}
	}

	public function getDataCellValue($model, $key, $index): ?string {
		assert($model instanceof IssueInterface);
		$issue = $model->getIssueModel();
		if (empty($issue->claims)) {
			return null;
		}
		$values = [];
		foreach ($model->claims as $claim) {
			if ($claim->isCompany()) {
				if (!empty($claim->trying_value)) {
					$values[] = Yii::$app->formatter->asCurrency($claim->trying_value);
				}
			}
		}
		if (empty($values)) {
			return null;
		}

		return implode(', ', $values);
	}

	protected function fetchContentOptions($model, $key, $index): array {
		assert($model instanceof IssueInterface);
		$options = parent::fetchContentOptions($model, $key, $index);
		$claims = array_filter($model->getIssueModel()->claims, static function (IssueClaim $claim): bool {
			return $claim->isCompany() && !empty($claim->trying_value);
		});

		if (!empty($claims)) {
			Html::addCssClass($options, $this->notEmptyContentClass);
			$tooltip = [];
			foreach ($claims as $claim) {
				$tooltip[] = $claim->entityResponsible->name;
			}
			$options[TooltipAsset::DEFAULT_ATTRIBUTE_NAME] = Html::encode(
				implode(', ', $tooltip)
			);
		}

		return $options;
	}

}
