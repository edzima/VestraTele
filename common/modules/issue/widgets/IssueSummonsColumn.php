<?php

namespace common\modules\issue\widgets;

use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\models\issue\IssueSearch;
use common\models\issue\Summon;
use common\widgets\grid\DataColumn;
use Yii;

class IssueSummonsColumn extends DataColumn {

	public $attribute = 'summonsStatusFilter';
	public $noWrap = true;
	public bool $contentCenter = true;
	public $format = 'html';

	public function init(): void {
		parent::init();
		if (empty($this->label)) {
			$this->label = Yii::t('issue', 'Summons');
		}
		if ($this->filter === null
			&& $this->grid->filterModel instanceof IssueSearch
			&& $this->attribute === 'summonsStatusFilter'
		) {
			$this->filter = $this->grid->filterModel::getSummonsStatusesNames();
		}
	}

	public function getDataCellValue($model, $key, $index): string {
		assert($model instanceof IssueInterface);
		$issue = $model->getIssueModel();
		$summons = $issue->summons;
		if (empty($summons)) {
			return 0;
		}
		$realized = array_filter($summons, static function (Summon $summon): bool {
			return $summon->isRealized();
		});
		if (empty($realized)) {
			return count($summons);
		}
		return Html::tag('strong', count($realized)) . ' / ' . count($summons);
	}

}
