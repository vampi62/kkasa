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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<script>
$(".configKey[data-l1key='cloud']")
  .change(function() {
    cloud = $( ".configKey[data-l1key='cloud'] option:selected" ).val();
    if (cloud == 1)
    {
      $(".kkasa-only-cloud").show();
    } else {
      $(".kkasa-only-cloud").val("");
      $(".kkasa-only-cloud").hide();
    }
  })
  .change();
</script>
<form class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <label class="col-sm-4 control-label">{{Mode}}</label>
            <div class="col-lg-2">
                <select class="form-control configKey" data-l1key="cloud">
                  <option value="0">{{Local}}</option>
                  <option value="1">{{Cloud}}</option>
                </select>
            </div>
        </div>
        <div class="form-group kkasa-only-cloud">
            <label class="col-lg-4 control-label">{{Nom d'utilisateur Kasa}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="username" />
            </div>
        </div>
        <div class="form-group kkasa-only-cloud">
            <label class="col-lg-4 control-label">{{Mot de passe Kasa}}</label>
            <div class="col-lg-2">
                <input type="password" class="configKey form-control" data-l1key="password" />
            </div>
        </div>
      	<div class="form-group">
            <label class="col-sm-4 control-label">{{Fréquence de raffraîchissement auto}}</label>
            <div class="col-lg-2">
    						<select class="form-control configKey" data-l1key="cron_freq">
    							<option value="0">{{Aucun}}</option>
    							<option value="1">1 {{min}}</option>
    							<option value="5">5 {{min}}</option>
    							<option value="15">15 {{min}}</option>
    							<option value="30">30 {{min}}</option>
    							<option value="60">1 {{Heure}}</option>
    							<option value="3600">1 {{jour}}</option>
    						</select>
            </div>
        </div>
      	<div class="form-group">
            <label class="col-sm-4 control-label">{{Niveau de log de l'erreur "Equipement hors ligne"}}</label>
            <div class="col-lg-2">
    						<select class="form-control configKey" data-l1key="offline_log">
    							<option value="error">{{Erreur}}</option>
    							<option value="warning">{{Avertissement}}</option>
    							<option value="info">{{Info}}</option>
    							<option value="debug">{{Debug}}</option>
    						</select>
            </div>
        </div>
        <?php
  			if (intval(log::getLogLevel('kkasa')) <=100)
  			{
  				?>
      	<div class="form-group">
            <label class="col-sm-4 control-label">{{URI Kasa}}<br /><span style="color:red">{{Modifications à vos risques et périls}}</span></label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="base_uri" />
            </div>
        </div>
      <?php } ?>
  </fieldset>
</form>
