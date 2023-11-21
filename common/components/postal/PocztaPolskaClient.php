<?php

namespace common\components\postal;

use common\components\postal\models\Cause;
use common\components\postal\models\Entity;
use common\components\postal\models\EntityDetails;
use common\components\postal\models\Events;
use common\components\postal\models\EventsList;
use common\components\postal\models\Shipment;
use common\components\postal\models\ShipmentDetails;
use SoapClient;
use SoapHeader;
use WsdlToPhp\WsSecurity\WsSecurity;
use yii\base\InvalidConfigException;

class PocztaPolskaClient extends SoapClient {

	public const CLIENT_TYPE_BUSINESS = 'business';
	public const CLIENT_TYPE_INDIVIDUAL = 'individual';

	public string $username = 'sledzeniepp';
	public string $password = 'PPSA';

	protected static $classMap = [
		'Przyczyna' => Cause::class,
		'Jednostka' => Entity::class,
		'SzczDaneJednostki' => EntityDetails::class,
		'Zdarzenie' => Events::class,
		'ListaZdarzen' => EventsList::class,
		'DanePrzesylki' => ShipmentDetails::class,
		'Przesylka' => Shipment::class,
	];

	public function __construct(?string $wsdl, array $options = null) {
		$options['classmap'] = static::$classMap;
		parent::__construct($wsdl, $options);
	}

	public function setWsHeader(): void {
		$this->__setSoapHeaders($this->getWsHeader());
	}

	public function checkShipment(string $number): Shipment {
		return $this->__soapCall('sprawdzPrzesylke', ['parameters' => ['numer' => $number]])->return;
	}

	public function version(): string {
		return $this->__soapCall('wersja', [])->return;
	}

	public static function getWsdlUrl(string $type = self::CLIENT_TYPE_INDIVIDUAL): string {
		switch ($type) {
			case self::CLIENT_TYPE_INDIVIDUAL:
				return 'https://tt.poczta-polska.pl/Sledzenie/services/Sledzenie?wsdl';
			case self::CLIENT_TYPE_BUSINESS:
				return 'https://ws.poczta-polska.pl/Sledzenie/services/Sledzenie?wsdl';
			default:
				throw new InvalidConfigException('Invalid Client type. Individual or Business.');
		}
	}

	public function getWsHeader(): SoapHeader {
		return WsSecurity::createWsSecuritySoapHeader(
			$this->username,
			$this->password,
		);
	}
}
