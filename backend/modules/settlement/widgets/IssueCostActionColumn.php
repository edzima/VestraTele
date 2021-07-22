<?php

namespace backend\modules\settlement\widgets;

use backend\helpers\Html;
use backend\helpers\Url;
use common\models\issue\IssueCost;
use common\models\issue\IssuePayCalculation;
use common\widgets\grid\ActionColumn;
use Yii;

class IssueCostActionColumn extends ActionColumn {

	public $controller = '/settlement/cost';
	public $template = '{settle} {link} {unlink} {view} {update} {delete}';
	public ?string $settleRedirectUrl = null;
	public bool $settle = true;
	public bool $unlink = false;
	public bool $link = false;

	public ?IssuePayCalculation $settlement = null;

	public function init() {
		parent::init();
		if ($this->settleRedirectUrl === null) {
			$this->settleRedirectUrl = Url::current();
		}
		if ($this->settle && !isset($this->buttons['settle'])) {
			$this->buttons['settle'] = function (string $key, IssueCost $cost): string {
				return $this->settleLink($cost);
			};
		}
		if ($this->settlement) {
			if ($this->unlink && !isset($this->buttons['unlink'])) {
				$this->buttons['unlink'] = function (string $key, IssueCost $cost): string {
					return $this->unlinkSettlementLink($cost, $this->settlement);
				};
			}
			if ($this->link && !isset($this->buttons['link'])) {
				$this->buttons['link'] = function (string $key, IssueCost $cost): string {
					return $this->linkSettlementLink($cost, $this->settlement);
				};
			}
		}
	}

	public function settleLink(IssueCost $cost): string {
		if ($cost->isSettled) {
			return '';
		}
		return Html::a(
			Html::icon('check'),
			['settle', 'id' => $cost->id, 'redirectUrl' => $this->settleRedirectUrl], [
			'title' => Yii::t('settlement', 'Settle'),
			'aria-label' => Yii::t('settlement', 'Settle'),
		]);
	}

	public function unlinkSettlementLink(IssueCost $cost, IssuePayCalculation $settlement): string {
		return Html::a(Html::icon('minus'),
			['/settlement/cost/settlement-unlink', 'id' => $cost->id, 'settlementId' => $settlement->id], [
				'data-method' => 'POST',
				'title' => Yii::t('settlement', 'Unlink with settlement'),
				'aria-label' => Yii::t('settlement', 'Unlink with settlement'),
			]);
	}

	public function linkSettlementLink(IssueCost $cost, IssuePayCalculation $settlement): string {
		return Html::a(Html::icon('plus'),
			['/settlement/cost/settlement-link', 'id' => $cost->id, 'settlementId' => $settlement->id], [
				'data-method' => 'POST',
				'title' => Yii::t('settlement', 'Link with settlement'),
				'aria-label' => Yii::t('settlement', 'Link with settlement'),
			]);
	}
}
