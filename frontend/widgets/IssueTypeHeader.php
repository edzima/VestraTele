<?php

namespace frontend\widgets;

use common\helpers\ArrayHelper;
use frontend\helpers\Html;
use yii\base\Widget;

class IssueTypeHeader extends Widget {

	public const CLASS_TITLE_WITH_NAV = 'title-with-nav';

	public string $content = '';
	public string $tag = 'h1';
	public array $options = [];
	public array $navOptions = [];

	public function init() {
		parent::init();
		if (empty($this->content)) {
			$this->content = $this->view->title;
		}
	}

	public function run(): string {
		$nav = $this->renderNav();
		if (empty($nav)) {
			return Html::tag($this->tag, $this->content, $this->options);
		}
		$content = $this->content;
		$options = $this->options;
		Html::addCssClass($options, static::CLASS_TITLE_WITH_NAV);
		$content .= $nav;
		return Html::tag($this->tag, $content, $options);
	}

	public function renderNav(): string {
		$nav = $this->navOptions;
		$class = ArrayHelper::remove($nav, 'class', IssueTypeNav::class);
		return $class::widget($nav);
	}
}
