<?php

namespace common\widgets;

use common\assets\CopyToClipboardAsset;
use common\assets\TooltipAsset;
use common\helpers\Html;
use Yii;
use yii\base\Widget;
use yii\helpers\Json;

class CopyToClipboardBtn extends Widget {

	public ?string $content = null;

	public array $options = [
		'class' => 'btn btn-info',
	];

	public string $copyText;
	public ?string $tooltipAsset = TooltipAsset::class;
	public string $copyToClipboardAsset = CopyToClipboardAsset::class;

	public int $timeout = 2000;
	public ?string $tooltipContent = null;

	public string $tag = 'button';

	public array $tooltipOptions = [
		'trigger' => 'focus',
		'content' => "SKOPIOWANO DO SCHOWKA!",
		'arrow' => false,
		'placement' => 'right',
		'duration' => 1000,
		'followCursor' => false,
		'theme' => 'success',
		'showOnCreate' => true,
	];

	public function init(): void {
		if ($this->tooltipContent === null) {
			$this->tooltipContent = Yii::t('common', 'Copied to Clipboard');
		}
		if (!isset($this->tooltipOptions)) {
			$this->tooltipOptions['content'] = $this->tooltipContent;
		}
		if (!isset($this->options['title'])) {
			$this->options['title'] = Yii::t('common', 'Copy to Clipboard');
			$this->options['aria-label'] = Yii::t('common', 'Copy to Clipboard');
		}
		if ($this->content === null) {
			$this->content = Html::icon('copy');
		}
		$this->options['onclick'] = $this->getOnClickScript();

		parent::init();
	}

	public function run(): string {
		if ($this->tooltipAsset) {
			$this->view->registerAssetBundle($this->tooltipAsset);
		}
		$this->view->registerAssetBundle($this->copyToClipboardAsset);
		$this->view->registerCss($this->getCssStyle());
		return $this->renderTag();
	}

	protected function renderTag(): string {
		switch ($this->tag) {
			case 'button':
				return Html::button($this->content, $this->options);
			case 'a':
				return Html::a($this->content, '#', $this->options);
			default:
				return Html::tag($this->tag, $this->content, $this->options);
		}
	}

	protected function getOnClickScript(): string {
		$tooltipOptions = Json::encode($this->tooltipOptions);
		return <<<JS
			copyToClipboard('{$this->copyText}');
			let tippyInstance =  tippy(this, $tooltipOptions)
			setTimeout(function (){
				tippyInstance.destroy();
			},{$this->timeout})
		JS;
	}

	private function getCssStyle(): string {
		return <<<CSS
			.tippy-tooltip.success-theme {
				background-color: forestgreen;
				color: white;
			}
		CSS;
	}

}
