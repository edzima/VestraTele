<?php

namespace common\widgets\grid;

use common\helpers\Html;
use common\helpers\Url;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\settlement\PayInterface;
use common\models\settlement\search\IssuePaySearch;
use common\widgets\GridView;
use kartik\grid\ExpandRowColumn;
use Yii;

class IssuePayGrid extends GridView {

	/** @var IssuePaySearch|null */
	public $filterModel;

	public ?string $settlementViewRoute = '/settlement/calculation/view';
	public ?string $payRoute = '/settlement/pay/pay';
	public ?string $updateRoute = '/settlement/pay/update';
	public ?string $statusRoute = '/settlement/pay/status';
	public ?string $deleteRoute = '/settlement/pay/delete';
	public ?string $payProvisionsRoute = '/settlement/pay/pay-provisions';
	public ?string $receivedRoute = '/settlement/pay-received/received';

	public bool $visibleStatus = true;

	public bool $visiblePayBtn = true;
	public bool $visibleUpdateBtn = true;

	public bool $visibleAgent = true;
	public bool $visibleCustomer = true;
	public bool $visiblePayAt = true;
	public bool $visibleProvisionsDetails = true;
	public bool $visibleSettlementType = true;
	public bool $visibleIssueType = true;
	public bool $rowColors = true;

	public ?int $userId = null;

	public function init(): void {
		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}

		if (empty($this->rowOptions) && $this->rowColors) {
			$this->rowOptions = static function (PayInterface $model): array {
				return Html::payStatusRowOptions($model);
			};
		}

		parent::init();
	}

	public function defaultColumns(): array {
		return [
			[
				'class' => SerialColumn::class,
				'visible' => $this->dataProvider->getTotalCount() > 1,
			],
			[
				'class' => ExpandRowColumn::class,
				'value' => function () {
					return GridView::ROW_COLLAPSED;
				},
				'detailUrl' => $this->payProvisionsRoute,
				'visible' => $this->visibleProvisionsDetails,
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'calculationType',
				'format' => 'raw',
				'value' => function (IssuePay $model): string {
					$name = $model->calculation->getTypeName();
					if ($this->settlementViewRoute === null) {
						return $name;
					}
					return Html::a(
						$name,
						Url::toRoute([$this->settlementViewRoute, 'id' => $model->calculation_id]),
						[
							'target' => '_blank',
						]
					);
				},
				'filter' => IssuePayCalculation::getTypesNames(),
				'label' => Yii::t('settlement', 'Settlement type'),
				'width' => '120px',
				'visible' => $this->visibleSettlementType,
			],
			[
				'class' => IssueTypeColumn::class,
				'attribute' => 'issueTypesIds',
				'value' => 'issue.type',
				'noWrap' => true,
				'valueType' => IssueTypeColumn::VALUE_NAME,
				'visible' => $this->visibleIssueType,
			],
			[
				'class' => AgentDataColumn::class,
				'value' => 'calculation.issue.agent.fullName',
				'visible' => $this->visibleAgent,
			],
			[
				'class' => CustomerDataColumn::class,
				'value' => 'calculation.issue.customer.fullName',
				'visible' => $this->visibleCustomer,
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'status',
				'value' => 'statusName',
				'filter' => IssuePaySearch::getStatusNames(),
				'width' => '100px',
				'visible' => $this->visibleStatus,
			],
			[
				'class' => CurrencyColumn::class,
				'pageSummary' => true,
			],
			[
				'attribute' => 'deadline_at',
				'format' => 'date',
				'width' => '140px',
			],
			[
				'attribute' => 'pay_at',
				'format' => 'date',
				'width' => '140px',
				'visible' => $this->visiblePayAt,
			],
			$this->actionColumn(),
		];
	}

	protected function actionColumn(): array {
		return [
			'class' => ActionColumn::class,
			'template' => '{pay} {status} {received} {update} {delete}',
			'visibleButtons' => [
				'pay' => function (PayInterface $pay): bool {
					return !$pay->isPayed()
						&& $this->payRoute !== null;
				},
				'update' => $this->updateRoute !== null,
				'status' => function (PayInterface $pay): bool {
					return !$pay->isPayed() && $this->statusRoute !== null;
				},
				'received' => function (PayInterface $pay): bool {
					return !$pay->isPayed() && $this->receivedRoute !== null;
				},
				'delete' => $this->deleteRoute !== null,
			],
			'buttons' => [
				'pay' => function ($url, IssuePay $model): string {
					return Html::a(
						'<span class="glyphicon glyphicon-check" aria-hidden="true"></span>',
						Url::toRoute([$this->payRoute, 'id' => $model->id]),
						[
							'title' => 'Oplac',
							'aria-label' => 'Oplac',
						]);
				},
				'status' => function ($url, IssuePay $model): string {
					return Html::a(
						'<span class="glyphicon glyphicon-flag" aria-hidden="true"></span>',
						Url::toRoute([$this->statusRoute, 'id' => $model->id]),
						[
							'title' => Yii::t('common', 'Status'),
							'aria-label' => Yii::t('common', 'Status'),
						]);
				},
				'received' => function ($url, IssuePay $model): string {
					return Html::a(
						'<span class="fa fa-car" aria-hidden="true"></span>',
						Url::toRoute([$this->receivedRoute, 'id' => $model->id]),
						[
							'title' => Yii::t('common', 'Status'),
							'aria-label' => Yii::t('common', 'Status'),
						]);
				},
				'update' => function ($url, IssuePay $model): string {
					return Html::a(
						'<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
						Url::toRoute([$this->updateRoute, 'id' => $model->id]),
						[
							'title' => Yii::t('common', 'Update'),
							'aria-label' => Yii::t('common', 'Update'),
						]);
				},
				'delete' => function ($url, IssuePay $model): string {
					return Html::a(
						'<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
						Url::toRoute([$this->deleteRoute, 'id' => $model->id]),
						[
							'title' => Yii::t('common', 'Delete'),
							'aria-label' => Yii::t('common', 'Delete'),
							'data-method' => 'post',
							'data-confirm' => Yii::t('common', 'Delete this pay'),
						]);
				},
			],
		];
	}

}
