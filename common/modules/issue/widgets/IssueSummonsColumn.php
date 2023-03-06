<?php

namespace common\modules\issue\widgets;

use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\models\issue\IssueSearch;
use common\models\issue\Summon;
use common\widgets\grid\DataColumn;
use common\widgets\GridView;
use kartik\select2\Select2;
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
			$this->filterType = GridView::FILTER_SELECT2;
			if (empty($this->filterWidgetOptions)) {
				$this->filterWidgetOptions = [
					'options' => [
						'multiple' => true,
						'placeholder' => $this->label,
					],
					'size' => Select2::SIZE_SMALL,
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
				];
			}
		}
	}

	public function getDataCellValue($model, $key, $index): string {
		assert($model instanceof IssueInterface);
		$summons = $model->getIssueModel()->summons;

		$content = $this->summonCountContent($summons);
		$docs = $this->docsCountContent($summons);
		if (!empty($docs)) {
			$content .= '<br>' . $docs;
		}
		return $content;
	}

	public function summonCountContent(array $summons): string {
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

	/**
	 * @param Summon[] $summons
	 * @return string
	 */
	private function docsCountContent(array $summons): ?string {
		$docsCount = 0;
		$confirmedCount = 0;
		foreach ($summons as $summon) {
			foreach ($summon->docsLink as $docLink) {
				$docsCount++;
				if ($docLink->isConfirmed()) {
					$confirmedCount++;
				}
			}
		}
		if (!$docsCount) {
			return null;
		}
		return $confirmedCount . ' / ' . $docsCount;
	}

}
