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

Puis renseigner vos identifiants Kasa et sélectionnez la fréquence de rafraîchissement désirée.

**Attention :** je n'ai aucune information sur une éventuelle limite du nombre de requêtes de l'API Kasa, mais si elle existe il se pourrait que TP-Link bloque vos requêtes jugées trop fréquentes et considérées comme abusives.
Pour le moment, je me suis limité à une fréquence de rafraîchissement de 15min dans mes tests sans soucis.

Vous restez donc responsable de votre utilisation, du choix de ce paramètre et des conséquences éventuelles que cela impliquerait.

Sauvegardez, puis rendez-vous sur la page du plugin (dans la catégorie "objets
connectés").

Appuyez sur "Synchroniser avec Kasa" et vous devriez voir apparaitre vos équipements.

Et voila !

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
* Les possesseurs de HS110 ont peut-être remarqué que la puissance n'était pas toujours égale au produit du voltage et de l'intensité. La raison se trouve dans la différence entre puissance active et apparente.

Plus d'information sur la [page wikipedia](https://fr.wikipedia.org/wiki/Puissance_en_r%C3%A9gime_alternatif) ou ce [petit dessin animé très bien fichu](https://www.youtube.com/watch?v=IURKavCBUkE).

* La mise à jour des valeurs (état, puissance, consommation) se fait sur demande
avec la commande "rafraîchir", à la sauvegarde de votre équipement, à l'appel
d'une action (switch On/Off) et via le cron en fonction de la fréquence que vous avez paramétré.
Cela signifie donc qu'une modification de l'état directement sur l'équipement
ou via l'application Kasa ne sera prise en compte qu'après votre délais de rafraîchissement (ou par action
manuellement de votre part sur Jeedom).
