<?php

require_once __DIR__ . '/../../../../core/php/core.inc.php';
require_once __DIR__ . '/../../resources/vendor/autoload.php';

use Mips\HydraoClient\Client;

class hydrao extends eqLogic {
	use MipsEqLogicTrait;

	public static function cron() {
		try {
			hydrao::syncDevices();
		} catch (\Throwable $th) {
		}
	}

	public static function getClient() {
		$username = config::byKey('username', __CLASS__);
		$password = config::byKey('password', __CLASS__);
		$apikey = config::byKey('apikey', __CLASS__);
		$client = new Client($apikey, log::getLogger(__CLASS__));
		if (!$client->login($username, $password)) {
			throw new Exception("Login failed");
		}
		return $client;
	}

	public static function syncDevices() {
		log::add(__CLASS__, 'debug', "syncDevices");
		$client = hydrao::getClient();

		// $result = $client->Users()->me();
		// if ($result->isSuccess()) {
		// 	log::add(__CLASS__, 'debug', 'client connected:'.$result->getData()->getEmail());
		// } else {
		// 	log::add(__CLASS__, 'warning', "client not connnected: ({$result->getHttpStatusCode()}){$result->getHttpError()}");
		// }

		// $result = $client->Advice()->get();
		// if ($result->isSuccess()) {
		// 	$advice = $result->getData()->getDescription();
		// 	log::add(__CLASS__, 'info', "Advice:{$result->getData()->getTitle()} - {$result->getData()->getDescription()}");
		// }

		$result = $client->UserStats()->get();
		if ($result->isSuccess()) {
			$userStats = $result->getData();
			log::add(__CLASS__, 'debug', 'userStats:'.$userStats);
		}

		$result = $client->ShowerHeads()->get();
		if ($result->isSuccess()) {
			foreach ($result->getData() as $showerHead) {
				log::add(__CLASS__, 'debug', 'showerHead:'.$showerHead);
				$logicalId = $showerHead->getDeviceUUID();
				$eqLogic = eqLogic::byLogicalId($logicalId, __CLASS__);
				if (!is_object($eqLogic)) {
					log::add(__CLASS__, 'info', "Creating new showerHead with logicalId={$logicalId}");
					$eqLogic = new self();
					$eqLogic->setLogicalId($logicalId);
					$eqLogic->setEqType_name(__CLASS__);
					$eqLogic->setIsEnable(1);
					$eqLogic->setIsVisible(1);
				}
				$eqLogic->setConfiguration('type', $showerHead->getType());
				$eqLogic->setConfiguration('mac_address', $showerHead->getMACAddress());
				$eqLogic->setConfiguration('hw_version', $showerHead->getHWVersion());
				$eqLogic->setConfiguration('fw_version', $showerHead->getFWVersion());

				$eqLogic->setName($showerHead->getLabel());
				$eqLogic->save(true);

				$eqLogic->createCommandsFromConfigFile(__DIR__ . '/../config/commands.json', 'common');
				$eqLogic->checkAndUpdateCmd('lastSyncDate', $showerHead->getLastSyncDate());

				$result = $client->ShowerHeads()->showerHead($logicalId)->showers(10);
				// if ($result->isSuccess()) {
					foreach ($result->getData() as $shower) {
						log::add(__CLASS__, 'debug', 'shower:'.$shower);
					}
				// }
				$result = $client->ShowerHeads()->showerHead($logicalId)->stats();
				// if ($result->isSuccess()) {
				log::add(__CLASS__, 'debug', 'stats:'.$result->getData());
				// }
			}
		} else {
			log::add(__CLASS__, 'warning', "getShowerHeads: ({$result->getHttpStatusCode()}){$result->getHttpError()}");
		}

		return true;
	}

	// public function refresh(Client $client) {
	// 	$result = $client->UserStats()->get();
	// 	if ($result->isSuccess()) {
	// 		$userStats = $result->getData();
	// 		log::add(__CLASS__, 'debug', 'userStats:'.$userStats);
	// 		$this->checkAndUpdateCmd('average_volume', $userStats->getAverageVolume());
	// 		$this->checkAndUpdateCmd('average_duration', $userStats->getAverageDuration());
	// 		$this->checkAndUpdateCmd('total_energy_saved', $userStats->get);
	// 		$this->checkAndUpdateCmd('total_volume_saved', $userStats->);
	// 		$this->checkAndUpdateCmd('total_money_saved', $userStats->);
	// 	}
	// }

	public function preInsert() {

	}

	public function postInsert() {

	}

	public function preSave() {

	}

	public function postSave() {

	}

	public function preUpdate() {

	}

	public function postUpdate() {

	}

	public function preRemove() {

	}

	public function postRemove() {

	}
}

class hydraoCmd extends cmd {

	public function execute($_options = array()) {
		$eqLogic = $this->getEqLogic();
		log::add('hydrao', 'debug', "action:{$this->getLogicalId()} on {$eqLogic->getLogicalId()}-{$eqLogic->getName()}");
		switch ($this->getLogicalId()) {
			case 'refresh':

				break;
			default:
				log::add(__CLASS__, 'warning', __('Commande inconnue:', __FILE__) . $this->getLogicalId());
		}
	}
}
