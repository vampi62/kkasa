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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    ajax::init();

  	if (init('action') == 'syncWithKasa') {
      $nb_devices = kkasa::syncWithKasa();
      if ($nb_devices>0)
      {
        ajax::success($nb_devices);
      }
      else {
        ajax::error(__("Synchronisation terminée mais aucun équipement joignable trouvé. Vérifiez l'application mobile Kasa",__FILE__));
      }
  	} elseif(init('action') == 'debugInfo') {
      kkasa::getDebugInfo();
      ajax::success();
    } elseif(init('action') == 'createCmd') {
      $id = init('id');
      $device = eqLogic::byId($id);
      $nb_cmd = $device->loadCmdFromConf(init('cmdType'),init('createcommand'));
      if ($nb_cmd < 0)
        ajax::error(__("Erreur durant l'ajout des commandes",__FILE__));
      else
        ajax::success("Cool");
    }



    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}
