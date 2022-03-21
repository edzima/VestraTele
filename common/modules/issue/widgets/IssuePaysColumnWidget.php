<?php

namespace common\modules\issue\widgets;

use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\widgets\grid\DataColumn;
use Yii;

class IssuePaysColumnWidget extends DataColumn {

	public $noWrap = true;
	public bool $contentCenter = true;
	public $format = 'html';

	public ?string $allPaidClass = 'success';
	public ?string $delayedClass = 'warning';

	public function init(): void {
		parent::init();
		if (empty($this->label)) {
			$this->label = Yii::t('settlement', 'Pays');
		}
	}

	public function getDataCellValue($model, $key, $index): string {
		assert($model instanceof IssueInterface);
		$issue = $model->getIssueModel();
		if (empty($issue->pays)) {
			return 0;
		}
		$paid = Yii::$app->pay->payedFilter($issue->pays);
		return count($paid) . ' / ' . count($issue->pays);
	}

	protected function fetchContentOptions($model, $key, $index): array {
		assert($model instanceof IssueInterface);
		$options = parent::fetchContentOptions($model, $key, $index);
		$pays = $model->getIssueModel()->pays;
		if (!empty($pays)) {
			$paid = Yii::$app->pay->payedFilter($pays);
			if (count($paid) === count($pays)) {
				if (!empty($this->allPaidClass)) {
					Html::addCssClass($options, $this->allPaidClass);
				}
			} else {
				if (!empty($this->delayedClass)) {
					foreach ($pays as $pay) {
						if ($pay->isDelayed()) {
							Html::addCssClass($options, $this->delayedClass);
							break;
						}
					}
				}
			}
		}

		return $options;
	}

}
