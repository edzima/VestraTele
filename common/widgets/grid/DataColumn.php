<?php

namespace common\widgets\grid;

use common\assets\TooltipAsset;
use common\helpers\Html;
use common\widgets\GridView;
use Decimal\Decimal;
use kartik\grid\DataColumn as BaseDataColumn;

/**
 * Class DataColumn
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class DataColumn extends BaseDataColumn {

	public bool $contentBold = false;
	public bool $contentCenter = false;
	public bool $ellipsis = false;
	public bool $noPrint = false;

	public bool $tooltip = false;

	public bool $withTags = true;
	public bool $pageSummaryFuncAsDecimal = false;

	public function init() {
		if ($this->pageSummaryFuncAsDecimal && $this->pageSummary && empty($this->pageSummaryFunc)) {
			$this->pageSummaryFunc = static function (array $decimals): Decimal {
				$sum = new Decimal(0);
				foreach ($decimals as $decimal) {
					if (is_float($decimal)) {
						$decimal = (string) $decimal;
					}
					$sum = $sum->add($decimal);
				}
				return $sum;
			};
		}
		parent::init();
		if ($this->tooltip) {
			$this->tooltipInit();
		}
		if ($this->noPrint) {
			Html::addNoPrintClass($this->headerOptions);
			Html::addNoPrintClass($this->contentOptions);
			Html::addNoPrintClass($this->footerOptions);
		}
	}

	protected function tooltipInit(): void {
		$this->grid->on(GridView::EVENT_AFTER_RUN, function () {
			TooltipAsset::register($this->_view);
			$this->_view->registerJs(
				TooltipAsset::initScript(
					TooltipAsset::defaultSelector('#' . $this->grid->getId())
				)
			);
		});
	}

	protected function fetchContentOptions($model, $key, $index): array {
		$options = parent::fetchContentOptions($model, $key, $index);
		if ($this->contentBold) {
			Html::addCssStyle($options, 'font-weight:bold');
		}
		if ($this->contentCenter) {
			Html::addCssClass($options, 'text-center');
		}
		if ($this->ellipsis) {
			Html::addCssClass($options, 'ellipsis');
		}
		return $options;
	}

	public function getDataCellValue($model, $key, $index) {
		$value = parent::getDataCellValue($model, $key, $index);
		if ($this->withTags) {
			$tags = $this->renderTags($model, $key, $index);
			if ($tags !== null) {
				$value .= $tags;
			}
		}
		return $value;
	}

	protected function renderTags($model, $key, $index): ?string {
		return null;
	}

}
