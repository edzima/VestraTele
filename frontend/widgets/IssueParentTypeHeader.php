<?php

namespace frontend\widgets;

use frontend\helpers\Html;
use yii\base\Widget;
use yii\bootstrap\Nav;

class IssueParentTypeHeader extends Widget {

	public string $content = '';
	public string $tag = 'h1';
	public array $options = [];
	public array $parentsMenuItems = [];
	public array $parentsMenuConfig = [];
	public array $parentsNavConfig = [
		'options' => [
			'class' => 'nav nav-pills',
		],
	];

	public function init() {
		parent::init();
		if (empty($this->parentsMenuItems)) {
			$this->parentsMenuItems = Html::issueParentTypeItems($this->parentsMenuConfig);
		}
		if (empty($this->content)) {
			$this->content = $this->view->title;
		}
	}

	public function run(): string {
		if (empty($this->parentsMenuItems)) {
			return Html::tag($this->tag, $this->content, $this->options);
		}
		$content = $this->content;
		$options = $this->options;
		Html::addCssClass($options, 'title-with-nav');
		$navConfig = $this->parentsNavConfig;
		$navConfig['items'] = $this->parentsMenuItems;
		$content .= Nav::widget($navConfig);
		return Html::tag($this->tag, $content, $options);
	}
}
