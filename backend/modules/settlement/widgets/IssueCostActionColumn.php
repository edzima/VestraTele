<?php

namespace backend\modules\settlement\widgets;

use backend\helpers\Html;
use backend\helpers\Url;
use common\models\issue\IssueCost;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use common\widgets\grid\ActionColumn;
use Yii;

class IssueCostActionColumn extends ActionColumn {

	public $controller = '/settlement/cost';
	public $template = '{issue} {settle} {link} {unlink} {view} {update} {delete} {hide-on-report} {visible-on-report}';
	public ?string $redirectUrl = null;
	public bool $settle = true;
	public bool $unlink = false;
	public bool $link = false;
	public bool $issue = true;
	public bool $report = true;

	public ?string $returnUrl = null;

	public ?IssuePayCalculation $settlement = null;

	public function init() {
		if ($this->returnUrl === null) {
			$this->returnUrl = Url::current();
		}
		if ($this->urlCreator === null) {
			$this->urlCreator = function ($action, IssueCost $model, $key, $index, $column) {
				if ($this->controller) {
					$action = $this->controller . '/' . $action;
				}
				return Url::toRoute([$action, 'id' => $model->id, 'returnUrl' => $this->returnUrl]);
			};
		}

		parent::init();

		if ($this->report && !Yii::$app->user->can(User::PERMISSION_PROVISION)) {
			$this->report = false;
		}

		if ($this->redirectUrl === null) {
			$this->redirectUrl = Url::current();
		}
		if ($this->issue && !isset($this->buttons['issue'])) {
			$this->buttons['issue'] = function (string $key, IssueCost $cost): ?string {
				return $this->issueLink($cost);
			};
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

		if ($this->report) {
			if (!isset($this->buttons['hide-on-report'])) {
				$this->buttons['hide-on-report'] = function (string $key, IssueCost $cost): string {
					if (!$cost->hide_on_report) {
						return $this->hideOnReportLink($cost);
					}
					return '';
				};
			}
			if (!isset($this->buttons['visible-on-report'])) {
				$this->buttons['visible-on-report'] = function (string $key, IssueCost $cost): string {
					if ($cost->hide_on_report) {
						return $this->visibleOnReportLink($cost);
					}
					return '';
				};
			}
		}
	}

	public function settleLink(IssueCost $cost): string {
		if ($cost->getIsSettled()) {
			return '';
		}
		return Html::a(
			Html::icon('check'),
			['/settlement/cost/settle', 'id' => $cost->id, 'redirectUrl' => $this->redirectUrl], [
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

	public function hideOnReportLink(IssueCost $cost): string {
		return Html::a(Html::icon('eye-close'),
			['/settlement/cost/hide-on-report', 'id' => $cost->id], [
				'data-method' => 'POST',
				'title' => Yii::t('provision', 'Hide on Report'),
				'aria-label' => Yii::t('provision', 'Hide on Report'),
			]);
	}

	public function visibleOnReportLink(IssueCost $cost): string {
		return Html::a(Html::icon('eye-open'),
			['/settlement/cost/visible-on-report', 'id' => $cost->id], [
				'data-method' => 'POST',
				'title' => Yii::t('provision', 'Visible on Report'),
				'aria-label' => Yii::t('provision', 'Visible on Report'),
			]);
	}

	public function issueLink(IssueCost $cost): ?string {
		if ($cost->issue) {
			return Html::a(
				'<i class="fa fa-suitcase"></i>',
				Url::issueView($cost->issue_id), [
					'title' => $cost->issue->getIssueName(),
					'aria-label' => $cost->issue->getIssueName(),
				]
			);
		}
		return null;
	}
}
