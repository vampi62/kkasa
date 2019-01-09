Description
===

Plugin for controlling TP-Link connected objects via the manufacturer's platform: [Kasa](https://www.tp-link.com/us/kasa-smart/kasa.html).
Today, only the connected plugs HS100 and HS110 (v1 & v2) are processed.

warnings
===

I do not have the vocation to ensure a long-term maintenance of this plugin.
I first created it for myself. Once stable, I shared it on the
market to benefit others. However:

-   I rarely have time to put myself on personal developments. So if
    you encounter a problem, do not hesitate to participate on the dedicated topic
    of the forum or on "issues" Github page. Please see the [Debug] (# Debug) section below to send me the correct information.
    Hopefully I'll have the time and motivation to
    help you ... but I do not engage in anything.

-   As mentioned before, this plugin only supports the HS100 and HS110 (v1 & v2) plugs.
    The reason is simple: it's all I have at home. I would be delighted to
    to deal with other devices ... but you will have to offer them to me (thanks by advance!) ;)
    However, if you try to use the plugin with other hardware,
    do not hesitate to inform me on the forum if it works properly or
    not.

-   This development is in Open Source (AGPL) and available on github. I
    would be happy to join other contributors. Do not hesitate to
    notify if you are interested.

Prerequisites
===
For the moment, the plugin requires that you have correctly installed your
equipment and configured its connection with Kasa (with the Kasa mobile application).


Setup
===
Following the installation and activation of the plugin, go to the page of
configuration to install the dependencies.

P.S .: For some unknown reason, sometimes the dependencies are
marked "OK" when they are not. In doubt always press
"Reinstall".

Then enter your Kasa credentials.

Save, then go to the plugin page (in the "connected devices" category).

Press "Synchronize with Kasa". Once the synchronization is successful, you have to refresh the page to see your equipment.

There you go!

Debug
===
Si vous rencontrez des soucis, vous pouvez me le remonter sur les issues github ou forum jeedom.
Merci au préalable de :
* activer le niveau de log "debug" sur le plugin. 
* Reproduisez votre erreur.
* Un nouveau bouton "Debug Infos" est apparu sur la page du plugin. Appuyer dessus. 
* Puis envoyer le résultat du log kkasa.

Encore un avertissement !
===
* L'information "intensité" (current) semble parfois incohérentes. Cependant le plugin ne fait que remonter les informations remontées par le serveur Kasa. Il est donc plutôt conseiller se fier à l'information "puissance" (power).

* La mise à jour des valeurs (état, puissance, consommation) se fait sur demande
avec la commande "rafraîchir", à la sauvegarde de votre équipement, à l'appel
d'une action (switch On/Off) et via le cron toutes les 15min.
Cela signifie donc qu'une modification de l'état directement sur l'équipement
ou via l'application Kasa ne sera prise en compte qu'après 15min (ou par action
manuellement de votre part sur Jeedom).
