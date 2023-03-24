<?php

namespace common\widgets;

use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;

class FieldsetDetailView extends Widget {

	public string $legend;
	public array $legendOptions = [];
	public string $afterLegend = '';

	public string $afterDetail = '';
	public array $htmlOptions = [];

	public array $detailConfig = [];

	public bool $toggle = true;
	public array $btnOptions = [
		'class' => 'btn toggle pull-right',
	];

	public function init() {
		parent::init();
		if ($this->toggle) {
			if (!isset($this->detailConfig['id'])) {
				$this->detailConfig['id'] = $this->getId();
			}
			$this->btnOptions['data-toggle'] = '#' . $this->detailConfig['id'];
		}
	}

	public function run(): string {
		if (isset($this->detailConfig['model']) && $this->detailConfig['model'] !== null) {
			return Html::tag('fieldset',
				$this->renderLegend() . $this->renderDetailView() . $this->afterDetail,
				$this->htmlOptions);
		}
		return '';
	}

	public function renderLegend(): string {
		$options = $this->legendOptions;
		$encode = ArrayHelper::remove($options, 'encode', true);
		$legend = $this->legend;
		if ($encode) {
			$legend = Html::encode($legend);
		}
		if ($this->toggle) {
			$legend .= $this->renderToggleBtn();
		}
		return Html::tag('legend', $legend, $options) . $this->afterLegend;
	}

	public function renderLegendContent(): string {
		if (!$this->toggle) {
			return $this->legend;
		}
		return $this->legend . $this->renderToggleBtn();
	}

	public function renderToggleBtn(): string {
		return Html::button(Html::icon('chevron-down'), $this->btnOptions);
	}

	public function renderDetailView(): string {
		$config = $this->detailConfig;
		$class = ArrayHelper::remove($config, 'class', DetailView::class);
		return $class::widget($config);
	}
}
