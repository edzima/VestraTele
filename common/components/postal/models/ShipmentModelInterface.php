<?php

namespace common\components\postal\models;

use yii\db\ActiveRecordInterface;

interface ShipmentModelInterface extends ActiveRecordInterface {

	public function getShipment(): ?Shipment;

}
