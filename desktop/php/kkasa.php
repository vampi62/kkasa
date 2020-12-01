<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
include_file('plugins/kkasa/desktop', 'kkasa', 'css');

$plugin = plugin::byId('kkasa');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>
<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
<?php
foreach ($eqLogics as $eqLogic) {
	$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
	echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '" style="' . $opacity .'"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
}
?>
           </ul>
       </div>
   </div>

	 <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
		 <legend><i class="fa fa-cog"></i>  {{Gestion}}</legend>
		 <div class="eqLogicThumbnailContainer">
      <div class="cursor eqLogicAction" id="btSync" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
      	<i class="fa fa-search" style="font-size : 6em;color:#767676;"></i>
    		<br />
    		<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Ajouter mes équipements}}</span>
  		</div>
      <div class="cursor eqLogicAction" data-action="gotoPluginConf" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
      	<i class="fa fa-wrench" style="font-size : 6em;color:#767676;"></i>
    		<br />
    		<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Configuration}}</span>
  		</div>
			<?php
			if (intval(log::getLogLevel('kkasa')) <=100)
			{
				?>
      <div class="cursor eqLogicAction" id="btDebug" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
      	<i class="fa fa-bug" style="font-size : 6em;color:#767676;"></i>
    		<br />
    		<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Debug Infos}}</span>
  		</div>
			<div class="cursor eqLogicAction" id="btDeleteAll" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
				<i class="fa fa-trash" style="font-size : 6em;color:#767676;"></i>
				<br />
				<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Supprimer tout}}</span>
			</div>
<?php } ?>
		</div>
		<legend><i class="fa fa-table"></i> {{Mes périphériques}}</legend>
		<div class="eqLogicThumbnailContainer">
<?php
foreach ($eqLogics as $eqLogic) {
	$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
	echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="text-align: center; background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
	echo '<img src="' . $eqLogic->getImage() . '" height="105" width="95" />';
	echo "<br>";
	echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;">' . $eqLogic->getHumanName(true, true) . '</span>';
	echo '</div>';
}
?>
		</div>
	</div>

	<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
		<a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
	  <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
	  <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
	  <ul class="nav nav-tabs" role="tablist">
	    <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
	    <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
	    <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
	  </ul>
	  <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
	    <div role="tabpanel" class="tab-pane active" id="eqlogictab">
	      <br/>
				<div class="col-lg-8">
			    <form class="form-horizontal">
		        <fieldset>
		            <div class="form-group">
		                <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
		                <div class="col-sm-6">
		                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
		                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}"/>
		                </div>
		            </div>
		            <div class="form-group">
		                <label class="col-sm-3 control-label" >{{Objet parent}}</label>
		                <div class="col-sm-6">
		                    <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
		                        <option value="">{{Aucun}}</option>
	<?php
		foreach (jeeObject::all() as $object) {
			echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
		}
	?>
		                   </select>
		               </div>
		           </div>
						   <div class="form-group">
					          <label class="col-sm-3 control-label">{{Catégorie}}</label>
					          <div class="col-sm-6">
					           <?php
					              foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
					              echo '<label class="checkbox-inline">';
					              echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
					              echo '</label>';
					              }
					            ?>
					         </div>
					     </div>
							<div class="form-group">
								<label class="col-sm-3 control-label"></label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
								</div>
							</div>
					  	<div class="form-group">
					        <label class="col-sm-3 control-label">{{Identifiant}}</label>
					        <div class="col-sm-6">
								      <input disabled class="eqLogicAttr configuration form-control" data-l1key="logicalId"/>
					        </div>
					    </div>
							<?php
							if (intval(log::getLogLevel('kkasa')) <=100)
							{
								?>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{DeviceId}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="deviceId"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{ChildId}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="child_id"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{Type}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="type"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{Local IP}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="local_ip"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{Local port}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="local_port"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{Software version}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="sw_ver"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{Device name}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="dev_name"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{Model}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="model"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{MAC}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="mac"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{Hardware ID}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="hwId"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{fwId}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="fwId"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{OEM ID}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="oemId"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{Hardware version}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="hw_ver"/>
								 </div>
							</div>
							<div class="form-group">
								 <label class="col-sm-3 control-label">{{Features}}</label>
								 <div class="col-sm-6">
										<input disabled class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="features"/>
								 </div>
							</div>
							<?php } ?>
						</fieldset>
					</form>
				</div>
				<div class="col-lg-4">
					<form class="form-horizontal">
						<fieldset>
							<div class="form-group">
								<label class="control-label">{{Recharger les commandes}}</label>
							</div>
							<div class="form-group">
								<a class="btn btn-danger bt_kkasaCreateCmd" dataCmdType="all">
									<i class="fa fa-search"></i> {{Charger toutes les commandes}}
								</a>
							</div>
							<div class="form-group">
								<a class="btn btn-danger bt_kkasaCreateCmd" dataCmdType="basic">
									<i class="fa fa-search"></i> {{Recharger commandes Basiques}}
								</a>
							</div>
							<div class="form-group">
								<a class="btn btn-danger bt_kkasaCreateCmd" dataCmdType="plug">
									<i class="fa fa-search"></i> {{Recharger commandes Prise}}
								</a>
							</div>
							<div class="form-group">
								<a class="btn btn-danger bt_kkasaCreateCmd" dataCmdType="power">
									<i class="fa fa-search"></i> {{Recharger commandes Energie}}
								</a>
							</div>
							<div class="form-group">
								<a class="btn btn-danger bt_kkasaCreateCmd" dataCmdType="led">
									<i class="fa fa-search"></i> {{Recharger commandes LED}}
								</a>
							</div>
							<div class="form-group">
								<a class="btn btn-danger bt_kkasaCreateCmd" dataCmdType="bulb">
									<i class="fa fa-search"></i> {{Recharger commandes lumière}}
								</a>
							</div>
							<div class="form-group">
								<a class="btn btn-danger bt_kkasaCreateCmd" dataCmdType="temp">
									<i class="fa fa-search"></i> {{Recharger commandes température}}
								</a>
							</div>
							<div class="form-group">
								<a class="btn btn-danger bt_kkasaCreateCmd" dataCmdType="color">
									<i class="fa fa-search"></i> {{Recharger commandes couleur}}
								</a>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a><br/><br/>
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>{{Nom}}</th>
							<th>{{Type}}</th>
							<th>{{Paramètres}}</th>
							<th>{{Options}}</th>
							<th>{{Action}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php include_file('desktop', 'kkasa', 'js', 'kkasa');?>
<?php include_file('core', 'plugin.template', 'js');?>
