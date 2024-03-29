<?php

namespace common\models\query;

use yii\db\Expression;

trait PhonableQueryTrait {

	protected function getPhoneColumns(): array {
		return [
			'phone',
		];
	}

	public function withPhoneNumber(string $value): self {
		[$table, $alias] = $this->getTableNameAndAlias();
		$i = 0;
		foreach ($this->getPhoneColumns() as $phoneColumn) {
			$phoneColumn = "$alias.$phoneColumn";
			if ($i === 0) {
				$this->andWhere(['like', $this->preparePhoneExpression($phoneColumn), $value]);
			} else {
				$this->orWhere(['like', $this->preparePhoneExpression($phoneColumn), $value]);
			}
			$i++;
		}
		return $this;
	}

	protected function preparePhoneExpression(string $phoneColumn): Expression {
		return new Expression(
			"REPLACE( REPLACE($phoneColumn, ' ', '') , '-', '')"
		);
	}
}
