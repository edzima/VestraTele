<?php

namespace common\models\query;

use yii\db\Expression;

trait PhonableQueryTrait {

	protected function getPhoneColumns(): array {
		return [
			'phone',
		];
	}

	public function withPhoneNumber($value): self {
		[$table, $alias] = $this->getTableNameAndAlias();
		foreach ($this->getPhoneColumns() as $phoneColumn) {
			$phoneColumn = "$alias.$phoneColumn";
			$this->orWhere(['like', $this->preparePhoneExpression($phoneColumn), $value]);
		}
		return $this;
	}

	protected function preparePhoneExpression(string $phoneColumn): Expression {
		return new Expression(
			"REPLACE( REPLACE($phoneColumn, ' ', '') , '-', '')"
		);
	}
}
