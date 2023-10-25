<?php

namespace frontend\widgets;

use frontend\helpers\Html;
use yii\base\Widget;
use yii\bootstrap\Nav;

class IssueParentTypeHeader extends Widget {

	protected const CLASS_WITH_FAVORITE = 'nav-with-favorite';

	public bool $withFavorite = true;

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
			if ($this->withFavorite) {
				$this->parentsMenuConfig['withFavorite'] = true;
			}
			$this->parentsMenuItems = Html::issueMainTypesItems($this->parentsMenuConfig);
		}
		if (empty($this->content)) {
			$this->content = $this->view->title;
		}
		if ($this->withFavorite) {
			Html::addCssClass($this->parentsNavConfig['options'], static::CLASS_WITH_FAVORITE);
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
