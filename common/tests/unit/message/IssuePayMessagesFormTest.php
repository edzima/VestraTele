<?php

namespace common\tests\unit\message;

use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssueInterface;
use common\models\issue\IssuePayInterface;
use common\models\message\IssuePayMessagesForm;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property IssuePayMessagesForm $model
 */
abstract class IssuePayMessagesFormTest extends BaseIssueMessagesFormTest {

	protected const DEFAULT_PAY_VALUE = '5999.99';
	protected const MODEL_CLASS = IssuePayMessagesForm::class;

	protected ?IssuePayInterface $pay = null;

	private SettlementFixtureHelper $settlementFixtureHelper;

	public function _before(): void {
		parent::_before();
		$this->settlementFixtureHelper = new SettlementFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return array_merge(
			parent::_fixtures(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::owner(),
			SettlementFixtureHelper::pay()
		);
	}

	protected function getFormattedPayValue(bool $replaceNonBreakSpace): string {
		$value = Yii::$app->formatter->asCurrency($this->pay->getValue());
		if ($replaceNonBreakSpace) {
			$value = str_replace(["&nbsp;", ' '], ' ', $value);
		}
		return $value;
	}

	protected function giveModel(IssueInterface $issue = null, array $config = []): void {
		$payConfig = ArrayHelper::remove($config, 'payConfig', []);
		if ($this->pay === null || !empty($payConfig)) {
			$this->givePay($payConfig);
		}
		if ($issue === null) {
			$issue = $this->pay->calculation;
			$this->issue = $issue;
		}
		parent::giveModel($issue, $config);
		$this->model->setPay($this->pay);
	}

	protected function givePay(array $config = []): void {
		$value = ArrayHelper::getValue($config, 'value', static::DEFAULT_PAY_VALUE);
		$this->pay = $this->settlementFixtureHelper->findPay(
			$this->settlementFixtureHelper->havePay($value, $config)
		);
	}
}
