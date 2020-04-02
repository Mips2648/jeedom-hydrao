<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */

use hydraoapi\Client;

require_once __DIR__ . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../../resources/vendor/autoload.php';

class hydrao extends eqLogic {

	private static function getCommandsConfig($file) {
		$return = array();
		$path = dirname(__FILE__) . "/../config/{$file}";
		$content = file_get_contents($path);
		if (is_json($content)) {
			$return += json_decode($content, true);
		} else {
			log::add(__CLASS__, 'error', __('Fichier de configuration non trouvé:', __FILE__).$path);
		}

		return $return;
	}

	public function createCmdFromDef($commandsDef) {
		$link_cmds = array();
		foreach ($commandsDef as $cmdDef){
			$cmd = $this->getCmd(null, $cmdDef["logicalId"]);
			if (!is_object($cmd)) {
				log::add(__CLASS__, 'debug', 'create:'.$cmdDef["logicalId"].'/'.$cmdDef["name"]);
				$cmd = new hydraoCmd();
				$cmd->setLogicalId($cmdDef["logicalId"]);
				$cmd->setEqLogic_id($this->getId());
				$cmd->setName(__($cmdDef["name"], __FILE__));
				if(isset($cmdDef["isHistorized"])) {
					$cmd->setIsHistorized($cmdDef["isHistorized"]);
				}
				if(isset($cmdDef["isVisible"])) {
					$cmd->setIsVisible($cmdDef["isVisible"]);
				}
				if (isset($cmdDef['template'])) {
					foreach ($cmdDef['template'] as $key => $value) {
						$cmd->setTemplate($key, $value);
					}
				}
			}
			$cmd->setType($cmdDef["type"]);
			$cmd->setSubType($cmdDef["subtype"]);
			if(isset($cmdDef["generic_type"])) {
				$cmd->setGeneric_type($cmdDef["generic_type"]);
			}
			if (isset($cmdDef['display'])) {
				foreach ($cmdDef['display'] as $key => $value) {
					if ($key=='title_placeholder' || $key=='message_placeholder') {
						$value = __($value, __FILE__);
					}
					$cmd->setDisplay($key, $value);
				}
			}
			if(isset($cmdDef["unite"])) {
				$cmd->setUnite($cmdDef["unite"]);
			}

			if (isset($cmdDef['configuration'])) {
				foreach ($cmdDef['configuration'] as $key => $value) {
					$cmd->setConfiguration($key, $value);
				}
			}

			if (isset($cmdDef['value'])) {
				$link_cmds[$cmdDef["logicalId"]] = $cmdDef['value'];
			}

			$cmd->save();

			if (isset($cmdDef['initialValue'])) {
				$cmdValue = $cmd->execCmd();
				if ($cmdValue=='') {
					$this->checkAndUpdateCmd($cmdDef["logicalId"], $cmdDef['initialValue']);
				}
			}
		}

		foreach ($link_cmds as $cmd_logicalId => $link_logicalId) {
			$cmd = $this->getCmd(null, $cmd_logicalId);
			$linkCmd = $this->getCmd(null, $link_logicalId);

			if (is_object($cmd) && is_object($linkCmd)) {
				$cmd->setValue($linkCmd->getId());
				$cmd->save();
			}
		}
	}

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
		$client = new Client($apikey);
		$client->setLogger(log::getLogger(__CLASS__));
		if (!$client->login($username, $password)) {
			throw new Exception("Login failed");
		}
		return $client;
	}

	public static function syncDevices() {
		log::add(__CLASS__, 'debug', "syncDevices");
		$client = hydrao::getClient();
		$result = $client->getUserInfo();
		if ($result->isSuccess()) {
			$response = $result->getResponse();
			log::add(__CLASS__, 'debug', 'client connected:'.$response['email']);
		} else {
			log::add(__CLASS__, 'warning', "client not connnected: ({$result->getHttpStatusCode()}){$result->getError()}");
		}
		return true;
	}

	public function createCommands() {
		$commands = self::getCommandsConfig('commands.json');

		$this->createCmdFromDef($commands['common']);

		foreach ($commands['specific'] as $commandForDevice) {
			if (in_array($this->getConfiguration('DevicetypeId', 0), $commandForDevice['deviceId'])) {
				$this->createCmdFromDef($commandForDevice['commands']);
			}
		}
		return true;
	}

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
