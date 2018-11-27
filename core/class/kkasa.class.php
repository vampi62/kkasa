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
define('TEST_FILE',__DIR__.'/../../3rparty/KKPA/Clients/KKPAApiClient.php');
require_once __DIR__  . '/../../../../core/php/core.inc.php';

error_reporting(-1);
ini_set('display_errors', 'On');

if (!class_exists('KKPA\Clients\KKPAApiClient')) {
	if (file_exists(TEST_FILE))
	{
		require_once(dirname(__FILE__) . '/../../3rparty/KKPA/Clients/KKPAApiClient.php');
		require_once(dirname(__FILE__) . '/../../3rparty/KKPA/Clients/KKPAPlugApiClient.php');
	}
}

class kkasa extends eqLogic {
    /*     * *************************Attributs****************************** */
    private static $_client = null;
    private static $_device = null;


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
      catch(NAClientException $ex)
      {
        $error_msg = "An error happened  while trying to retrieve your tokens \n" . $ex->getMessage() . "\n";
        log::add('kkasa', 'debug', $error_msg);
      }
      return self::$_client;
    }

		public function getDevice() {
			if (self::$_device == null) {
        self::$_device =  new KKPA\Clients\KKPAPlugApiClient(array(
          'username' => config::byKey('username', 'kkasa'),
          'password' => config::byKey('password', 'kkasa'),
					'deviceId' => $this->getLogicalId()
        ));
      }
			return self::$_device;
		}
    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {

      }
     */
	 public static function cron15() {
		 foreach (self::byType('kkasa') as $kkasa) {
			 $kkasa->syncRealTime();
		 }
	 }


   public static function dependancy_info() {
		  log::add(__CLASS__ . '_update','debug','Checking dependancy');
   		$return = array();
   		$return['log'] = 'kkasa_update';
   		$return['progress_file'] =  jeedom::getTmpFolder('kkasa') . '/dependancy_kkasa_in_progress';
   		if (file_exists(__DIR__.'/../../3rparty/KKPA/Clients/KKPAApiClient.php')) {
   			$return['state'] = 'ok';
   		} else {
   			$return['state'] = 'nok';
   		}
			log::add(__CLASS__ . '_update','debug','Dependancy_info: '.print_r($return,true));
   		return $return;
   	}

    public static function dependancy_install() {
  		log::remove(__CLASS__ . '_update');
      $path_3rd_party = __DIR__.'/../../3rparty/';
  		return array('script' => __DIR__ . '/../../resources/install.sh ' . $path_3rd_party . ' ' . jeedom::getTmpFolder('kkasa'), 'log' => log::getPathToLog(__CLASS__ . '_update'));
  	}

		public static function health() {
			$return = array();
			$result = self::dependancy_info(true)['state'];
			$return[] = array(
				'test' => __('Dépendances', __FILE__),
				'result' => strtoupper($result),
				'advice' =>  '{{Installer les dépendance dans la configuration du plugin}}',
				'state' => (self::dependancy_info() == 'ok'),
			);
			return $return;
		}

    public static function syncWithKasa() {
  		$client = self::getClient();
  		$devicelist = $client->getDeviceList();
  		log::add(__CLASS__, 'debug', print_r($devicelist, true));
  		foreach ($devicelist as $device) {
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

  				$eqLogic->setEqType_name('kkasa');
  				$eqLogic->setIsVisible(1);
  				$eqLogic->setIsEnable(1);
  				$eqLogic->save();
  			}
				$eqLogic->refreshWidget();
  		}
  	}

		public function syncRealTime()
		{
			$changed = false;
			$device = $this->getDevice();
			$data = $device->getRealTime();
      $sysinfo = $device->getSysInfo();
			foreach($data as $key => $value)
			{
				switch($key)
				{
					case 'power_mw':
						$cmd_name = 'power';
						$value = $value/1000;
						break;
					case 'voltage_mv':
						$cmd_name = 'voltage';
						$value = $value/1000;
						break;
					case 'current_ma':
						$cmd_name = 'current';
						$value = $value/1000;
						break;
					case 'total_wh':
						$cmd_name = 'consumption';
						break;
					default:
						$cmd_name = '';
						continue;

				}
				if ($cmd_name != '')
					$changed = $this->setInfo($cmd_name,$value) || $changed;
			}
			$changed = $this->setInfo('state',$sysinfo['relay_state']) || $changed;
			if ($changed) {
				$this->refreshWidget();
			}

		}

		public function setState($state)
		{
			$device = $this->getDevice();
			$state = boolval($state);
			if ($state)
			{
				$device->switchOn();
			} else {
				$device->switchOff();
			}
			sleep(0.5);
			$this->syncRealTime();
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
			log::add('kkasa','debug','Processing refresh');
			$this->addCmd('state','info','binary','State',1,1,null,'ENERGY_STATE');
			$this->addCmd('refresh','action','other','Rafraîchir',1);
			$this->addCmd('on','action','other','On',1,null,null,'ENERGY_ON');
			$this->addCmd('off','action','other','Off',1,null,null,'ENERGY_OFF');
			if (substr($this->getConfiguration('model'),0,5) == 'HS110') {
				$this->addCmd('power','info','numeric','Power',1,1,'W','POWER');
				$this->addCmd('voltage','info','numeric','Voltage',0,0,'V','VOLTAGE');
				$this->addCmd('current','info','numeric','Current',0,0,'A',null);
				$this->addCmd('consumption','info','numeric','Consumption',1,1,'WH','CONSUMPTION');
				$this->syncRealTime();
			}
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
