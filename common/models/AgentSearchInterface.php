<?php

namespace common\models;

use yii\db\QueryInterface;

interface AgentSearchInterface {

	public function getAgentsNames(): array;

	public function applyAgentsFilters(QueryInterface $query): void;
}
