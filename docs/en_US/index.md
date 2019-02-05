Description
===

Plugin for controlling TP-Link connected objects via the manufacturer's platform: [Kasa](https://www.tp-link.com/us/kasa-smart/kasa.html).
Today, only the connected plugs HS100 and HS110 (v1 & v2) are processed.

Warnings
===

-   A ce jour, seules les prises HS100 et HS110 (v1 & v2) sont prises en charge.
-   Si vous rencontrez un problème, n'hésitez pas à participer au sujet dédié sur le forum ou sur les "issues" Github.
    Merci de regarder la section [Debug](#Debug) ci-dessous pour m'envoyer les bonnes informations.

Prérequis
===
Pour le moment, le plugin nécessite que vous ayez correctement installé votre
équipement et configuré sa connexion avec Kasa (avec l'application mobile Kasa).


Installation
===
Suite à l'installation et activation du plugin, rendez-vous sur la page de configuration pour installer les dépendances.

Puis renseigner vos identifiants Kasa et sélectionnez la fréquence de rafraîchissement désirée.

**Attention :** je n'ai aucune information sur une éventuelle limite du nombre de requêtes de l'API Kasa, mais si elle existe il se pourrait que TP-Link bloque vos requêtes jugées trop fréquentes et considérées comme abusives.
Pour le moment, je me suis limité à une fréquence de rafraîchissement de 15min dans mes tests sans soucis.

Vous restez donc responsable de votre utilisation, du choix de ce paramètre et des conséquences éventuelles que cela impliquerait.

Sauvegardez, puis rendez-vous sur la page du plugin (dans la catégorie "objets
connectés").

Appuyez sur "Synchroniser avec Kasa" et vous devriez voir apparaître vos équipements.

Et voila !

Debug
===
Si vous rencontrez des soucis, vous pouvez me le remonter sur les issues github ou forum jeedom.
Merci au préalable de :
* activer le niveau de log "debug" sur le plugin.
* Reproduisez votre erreur.
* Un nouveau bouton "Debug Infos" est apparu sur la page du plugin. Appuyer dessus.
* Puis envoyer le résultat du log kkasa.
**Obligatoire :** merci de m'indiquer les versions KKASA et KKPA utilisées (voir la page "santé" sur votre jeedom) ainsi que votre mode d'installation (depuis le market stable ou bêta ? depuis github branche master ou develop ?)

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
