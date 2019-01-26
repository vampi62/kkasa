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
define('TEST_FILE',__DIR__.'/../../3rparty/KKPA/autoload.php');
define('KKPA_MIN_VERSION','1.1');
require_once __DIR__  . '/../../../../core/php/core.inc.php';

/*error_reporting(-1);
ini_set('display_errors', 'On');*/

if (!class_exists('KKPA\Clients\KKPAApiClient')) {
	if (file_exists(TEST_FILE))
	{
		require_once(dirname(__FILE__) . '/../../3rparty/KKPA/autoload.php');
	}
}

class kkasa extends eqLogic {
    /*     * *************************Attributs****************************** */
    private static $_client = null;
    private $_device = null;


    /*     * ***********************Methode static*************************** */
    public static function getClient() {
      if (self::$_client == null) {
        self::$_client =  new KKPA\Clients\KKPAApiClient(array(
          'username' => config::byKey('username', 'kkasa'),
          'password' => config::byKey('password', 'kkasa'),
        ));
      }
      try
      {
        self::$_client->getAccessToken();
      }
      catch(KKPA\Exceptions\KKPAClientException $ex)
      {
        $error_msg = "An error happened  while trying to retrieve your tokens \n" . $ex->getMessage() . "\n";
        log::add('kkasa', 'debug', $error_msg);
      }
      return self::$_client;
    }

		public static function getDebugInfo() {
			$ex = null;
			$client = self::getClient();
  		$devicelist = $client->getDeviceList();
  		log::add(__CLASS__, 'debug', '*** DeviceList:');
  		log::add(__CLASS__, 'debug', print_r($client->debug_last_request(),true));
  		foreach ($devicelist as $device) {
				try {
					log::add(__CLASS__, 'debug', '***  Device '.$device->getVariable('deviceId',''));
					$device->getSysInfo();
		  		log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
					$device->getRealTime();
		  		log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
				} catch (KKPA\Exceptions\KKPASDKException $ex)
				{
					log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
				}
			}
			if ($ex != null)
			{
				throw $ex;
			}
		}

		public function getDevice() {
			if ($this->_device == null) {
        $this->_device =  new KKPA\Clients\KKPAPlugApiClient(array(
          'username' => config::byKey('username', 'kkasa'),
          'password' => config::byKey('password', 'kkasa'),
					'deviceId' => $this->getLogicalId()
        ));
      }
			return $this->_device;
		}

		public function isPowerAvailable() {
			return (substr($this->getConfiguration('model'),0,5) == 'HS110');
		}

    public static function cron() {
 		 if (strval(config::byKey('cron_freq','kkasa','15'))=='1')
 		 		self::cronExec();
    }

		public static function cron5() {
 		 if (strval(config::byKey('cron_freq','kkasa','15'))=='5')
 		 		self::cronExec();
    }

	 public static function cron15() {
		 if (strval(config::byKey('cron_freq','kkasa','15'))=='15')
		 		self::cronExec();
	 }

	public static function cron30() {
		if (strval(config::byKey('cron_freq','kkasa','15'))=='30')
			 self::cronExec();
	}

    public static function cronHourly() {
 		 if (strval(config::byKey('cron_freq','kkasa','15'))=='60')
 		 		self::cronExec();
    }

		public static function cronDaily() {
 		 if (config::byKey('cron_freq','kkasa','15')=='3600')
 		 		self::cronExec();

		 // Once a day: update the firmware version
 		 foreach (self::byType('kkasa') as $kkasa) {
 			 if ($kkasa->getIsEnable())
 			 {
				 	$device = $this->getDevice();
 					$sysinfo = $device->getSysInfo();
 					$kkasa->setConfiguration('sw_ver', $sysinfo['sw_ver']);
 					$kkasa->setConfiguration('fwId', $sysinfo['fwId']);
 					$kkasa->setConfiguration('oemId', $sysinfo['oemId']);
 					$kkasa->save();
 			 }
 		 }
		}

	 public static function cronExec() {
		 foreach (self::byType('kkasa') as $kkasa) {
			 if ($kkasa->getIsEnable())
			 {
				 $kkasa->syncRealTime();
			 }
		 }
	 }

   public static function dependancy_info() {
		  log::add(__CLASS__ . '_update','debug','Checking dependancy');
   		$return = array();
   		$return['log'] = 'kkasa_update';
   		$return['progress_file'] =  jeedom::getTmpFolder('kkasa') . '/dependancy_kkasa_in_progress';
   		if (file_exists(__DIR__.'/../../3rparty/KKPA/Clients/KKPAApiClient.php')) {
				try {
					if (version_compare(KKPA\Clients\KKPAApiClient::getVersion(),KKPA_MIN_VERSION,'<'))
					{
						log::add(__CLASS__,'error',
							__('Nouvelle version des dépendance requise. Merci de réinstaller les dépendances de kkasa',__FILE__)
						);
		   			$return['state'] = 'nok';
					} else
					{
   					$return['state'] = 'ok';
					}
				}
				catch (Exception $e)
				{
		   		$return['state'] = 'nok';
				}

   		} else {
   			$return['state'] = 'nok';
   		}
			log::add(__CLASS__,'debug','Dependancy_info: '.print_r($return,true));
   		return $return;
   	}

    public static function dependancy_install() {
  		log::remove(__CLASS__ . '_update');
      $path_3rd_party = __DIR__.'/../../3rparty/';
  		return array(
				'script' => __DIR__ . '/../../resources/install.sh ' . $path_3rd_party . ' ' . jeedom::getTmpFolder('kkasa'),
				'log' => log::getPathToLog(__CLASS__ . '_update')
			);
  	}

		public static function health() {
			$return = array();
			$result = strtoupper(self::dependancy_info()['state']);
			$return[] = array(
				'test' => __('Dépendances', __FILE__),
				'result' => $result,
				'advice' => ($result == 'OK') ? '' : __('(ré)Installer les dépendance dans la configuration du plugin',__FILE__),
				'state' => ($result == 'OK'),
			);

			try
      {
        $client = self::getClient();
				$client->getAccessToken();
				$state = true;
      }
      catch(KKPA\Exceptions\KKPAClientException $ex)
      {
				$state = false;
      }
			$return[] = array(
				'test' => __('Identification Kasa', __FILE__),
				'result' => ($state) ? "OK" : "NOK",
				'advice' => ($state) ? '' : __('Vérifier vos identifiants Kasa dans la configuration du plugin',__FILE__),
				'state' => $state,
			);

			$nb_offline = 0;
			if ($state)
			{
  			$devicelist = $client->getDeviceList();
	  		foreach ($devicelist as $device) {
					try
					{
						$device->getRealTime();
					}
					catch(KKPA\Exceptions\KKPADeviceException $ex)
					{
						if ($ex->getCode() == KKPA_DEVICE_OFFLINE || $ex->getCode() == KKPA_TIMEOUT)
							$nb_offline++;
					}
				}
				$state = ($nb_offline == 0);
			}
			$return[] = array(
				'test' => __('Prises hors ligne', __FILE__),
				'result' => $nb_offline,
				'advice' => ($state) ? '' : __('Vérifiez que vos prises sont connectées au wifi',__FILE__),
				'state' => $state,
			);

			return $return;
		}

    public static function syncWithKasa() {
  		$client = self::getClient();
  		$devicelist = $client->getDeviceList();
			$nb_devices = 0;
  		foreach ($devicelist as $device) {
				if (method_exists($device,'toString')) // Retrocompatibility. To be removed after
				{
					log::add(__CLASS__, 'debug',$device->toString());
				} else {
					log::add(__CLASS__, 'debug', print_r($device, true));
				}
				try
				{
	        $sysinfo     = $device->getSysInfo();
	  			$deviceId    = $sysinfo['deviceId'];
	  			$alias       = $sysinfo['alias'];
	  			$type  			 = $sysinfo['type'];
					$fwVer			 = $sysinfo['sw_ver'];
					$deviceName	 = $sysinfo['dev_name'];
					$deviceModel = $sysinfo['model'];
					$deviceMac	 = $sysinfo['mac'];
					$hwId				 = $sysinfo['hwId'];
					$fwId				 = $sysinfo['fwId'];
					$oemId			 = $sysinfo['oemId'];
					$deviceHwVer = $sysinfo['hw_ver'];

	  			$eqLogic = kkasa::byLogicalId($deviceId, 'kkasa');
	  			if (!is_object($eqLogic)) {
	  				$eqLogic = new self();
	                  foreach (object::all() as $object) {
	                      if (stristr($alias,$object->getName())){
	                          $eqLogic->setObject_id($object->getId());
	                          break;
	                      }
	                  }
	  				$eqLogic->setLogicalId($deviceId);
	  				$eqLogic->setName($alias);
						$eqLogic->setConfiguration('type', $type);
						$eqLogic->setConfiguration('sw_ver', $fwVer);
						$eqLogic->setConfiguration('dev_name', $deviceName);
						$eqLogic->setConfiguration('model', $deviceModel);
						$eqLogic->setConfiguration('mac', $deviceMac);
						$eqLogic->setConfiguration('hwId', $hwId);
						$eqLogic->setConfiguration('fwId', $fwId);
						$eqLogic->setConfiguration('oemId', $oemId);
						$eqLogic->setConfiguration('hw_ver', $deviceHwVer);
						$eqLogic->setConfiguration('cron_freq', 15);

	  				$eqLogic->setEqType_name('kkasa');
	  				$eqLogic->setIsVisible(1);
	  				$eqLogic->setIsEnable(1);
	  				$eqLogic->save();
	  			}
					$nb_devices++;
					$eqLogic->refreshWidget();
				} catch(KKPA\Exceptions\KKPADeviceException $ex)
				{
					if ($ex->getCode() == KKPA_DEVICE_OFFLINE || $ex->getCode() == KKPA_TIMEOUT)
					{
						log::add(__CLASS__, 'warning',
							sprintf(
								__('Equipement %s trouvé mais injoignable. Ignoré',__FILE__),
								$device->getVariable('deviceId','')
							)
						);
					}
				}
	  	}
			return $nb_devices;
  	}

		public function syncRealTime()
		{
			$attempt = 0;
			$success = false;
			$changed = false;
			$device = $this->getDevice();
			log::add('kkasa','debug','Processing refresh of '.$device->getVariable('deviceId',''));
			while((!$success) && $attempt < 3)
			{
				try
				{
		      $sysinfo = $device->getSysInfo();
					$changed = $this->setInfo('state',$sysinfo['relay_state']) || $changed;
					$this->setConfiguration('sw_ver', $sysinfo['sw_ver']);
					$this->setConfiguration('fwId', $sysinfo['fwId']);
					$this->setConfiguration('oemId', $sysinfo['oemId']);

					if ($this->isPowerAvailable())
					{
						$data = $device->getRealTime();
						foreach($data as $key => $value)
						{
							switch($key)
							{
								case 'power_mw':
									$cmd_name = 'power';
									$value = $value/1000;
									break;
								case 'power':
									$cmd_name = 'power';
									$value = $value;
									break;
								case 'voltage_mv':
									$cmd_name = 'voltage';
									$value = $value/1000;
									break;
								case 'voltage':
									$cmd_name = 'voltage';
									$value = $value;
									break;
								case 'current_ma':
									$cmd_name = 'current';
									$value = $value/1000;
									break;
								case 'current':
									$cmd_name = 'current';
									$value = $value;
									break;
								case 'total_wh':
									$cmd_name = 'consumption';
									break;
								case 'total':
									$cmd_name = 'consumption';
									break;
								default:
									$cmd_name = '';
									continue;

							}
							if ($cmd_name != '')
								$changed = $this->setInfo($cmd_name,$value) || $changed;
						}
					}
					$success = true;

					if ($changed) {
						$this->refreshWidget();
					}
				}
				catch(Exception $ex)
				{
					$attempt++;
		  		log::add(__CLASS__, 'debug', "ERROR during request - attempt #" . $attempt . "/3");
		  		log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
					if ($attempt>2) throw $ex;
				}
			}

		}

		public function setState($state)
		{
			$device = $this->getDevice();
			$success = false;
			$attempt = 0;
			while ((!$success) && $attempt < 3)
			{
				try
				{
					$state = boolval($state);
					if ($state)
					{
						$device->switchOn();
					} else {
						$device->switchOff();
					}
					sleep(1.5);
					$this->syncRealTime();
					$success = true;
				}
				catch(Exception $ex)
				{
					$attempt++;
					log::add(__CLASS__, 'debug', "ERROR during request - attempt #".$attempt . "/3");
					log::add(__CLASS__, 'debug', print_r($device->debug_last_request(),true));
					if ($attempt > 2) throw $ex;
				}
			}
		}

		public function getImgFilePath() {
			switch($this->getConfiguration('type'))
			{
				case 'IOT.SMARTPLUGSWITCH':
					return 'hs110.jpg';
					break;
			}
			return false;
		}

		public function getImage() {
			return 'plugins/kkasa/docs/assets/images/' . $this->getImgFilePath();
		}

    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {

    }

    public function postInsert() {

    }

    public function preSave() {

    }

		public function addCmd($id,$type,$subtype=NULL,$name = NULL,$isVisible=NULL,$isHistorized=NULL,$unit=NULL,$generic_type=NULL)
		{
			if (!isset($name)) $name = ucfirst($id);
			$cmd = $this->getCmd(null, $id);
			if (!is_object($cmd)) {
				$cmd = new kkasaCmd();
				$cmd->setName(__($name, __FILE__));
				if (isset($isVisible)) $cmd->setIsVisible($isVisible);
				if (isset($isHistorized)) $cmd->setIsHistorized($isHistorized);
				if (isset($generic_type)) $cmd->setDisplay('generic_type', $generic_type);
				if (isset($unit)) $cmd->setUnite($unit);
			}
			$cmd->setEqLogic_id($this->getId());
			$cmd->setLogicalId($id);
			$cmd->setType($type);
			if (isset($subtype)) $cmd->setSubType($subtype);
			$cmd->save();
		}

    public function postSave() {
			$this->addCmd('state','info','binary',__('Etat',__FILE__),1,1,null,'ENERGY_STATE');
			$this->addCmd('refresh','action','other',__('Rafraîchir',__FILE__),1);
			$this->addCmd('on','action','other',__('On',__FILE__),1,null,null,'ENERGY_ON');
			$this->addCmd('off','action','other',__('Off',__FILE__),1,null,null,'ENERGY_OFF');
			if ($this->isPowerAvailable()) {
				$this->addCmd('power','info','numeric',__('Puissance',__FILE__),1,1,'W','POWER');
				$this->addCmd('voltage','info','numeric',__('Voltage',__FILE__),0,0,'V','VOLTAGE');
				$this->addCmd('current','info','numeric',__('Intensité',__FILE__),0,0,'A',null);
				$this->addCmd('consumption','info','numeric',__('Consommation',__FILE__),1,1,'WH','CONSUMPTION');
			}
			$this->syncRealTime();
    }

    public function preUpdate() {

    }

    public function postUpdate() {

    }

    public function preRemove() {

    }

    public function postRemove() {

    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
		public function setInfo($cmd_name,$value)
		{
			$cmd = $this->getCmd(null,$cmd_name);
			$changed = $this->checkAndUpdateCmd($cmd_name, $value);
			log::add('kkasa','debug','set: '.$cmd->getName().' to '. $value);
			$cmd->event($value,null,0);
			return $changed;
		}
}

class kkasaCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
			if ($this->getType() == 'info') {
				return;
			}
			if ($this->getType() == '') {
				return '';
			}
			$eqLogic = $this->getEqLogic();
			if ($this->getLogicalId() == 'refresh') {
				$eqLogic->syncRealTime();
			}
			if ($this->getLogicalId() == 'on') {
				$eqLogic->setState(1);
			}
			if ($this->getLogicalId() == 'off') {
				$eqLogic->setState(0);
			}


    }

    /*     * **********************Getteur Setteur*************************** */
}
