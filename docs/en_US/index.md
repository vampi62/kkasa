Description
===

Plugin permettant de piloter des objets connectés TP-Link.
Deux modes de fonctionnement :

- **Mode cloud (par défaut) :** communication via la plateforme [Kasa](https://www.tp-link.com/us/kasa-smart/kasa.html) (nécessaire si votre Jeedom n'est pas dans le même réseau local que vos équipements)
- **Mode local :** communication directe via le réseau local

Equipements compatibles :
===

- **Prises connectées** HS100, HS105, HS110, HS200, HS220, HS300
- **Ampoules connectées** LB100, LB120, LB130

Installation
===
Suite à l'installation et activation du plugin, rendez-vous sur la page de configuration pour installer les dépendances.

Mode Cloud
==
En mode cloud, le plugin nécessite que vous ayez correctement installé votre
équipement et configuré sa connexion avec Kasa (avec l'application mobile Kasa).

Puis renseignez vos identifiants Kasa et sélectionnez la fréquence de rafraîchissement désirée.

**Attention :** *je n'ai aucune information sur une éventuelle limite du nombre de requêtes de l'API Kasa, mais si elle existe il se pourrait que TP-Link bloque vos requêtes jugées trop fréquentes et considérées comme abusives.
Pour le moment, je me suis limité à une fréquence de rafraîchissement de 15min dans mes tests sans soucis.
Vous restez donc responsable de votre utilisation, du choix de ce paramètre et des conséquences éventuelles que cela impliquerait.*

N'oubliez pas de sauvegarder.

Mode Local
==
Il vous suffit de renseigner "Local" dans le champ "Mode" de la page de configuration.

Là aussi : n'oubliez pas de sauvegarder.

Mise à jour
===
Après une mise à jour, il est recommandé de réinstaller les dépendances.

Ajout des équipements
===
Rendez-vous sur la page du plugin (dans la catégorie "objets connectés").
Appuyez sur "Ajouter mes équipements" et vous devriez voir apparaître vos équipements.

Et voila !

Debug
===
Si vous rencontrez des soucis, vous pouvez me le remonter sur les issues github ou forum jeedom.
Merci au préalable de :
* Activez le niveau de log "debug" sur le plugin.
* Reproduisez votre erreur.
* Un nouveau bouton "Debug Infos" est apparu sur la page du plugin. Appuyez dessus.
* Puis envoyez le résultat du log kkasa.
**Obligatoire :** merci de m'indiquer les versions KKASA et KKPA utilisées (voir la page "santé" sur votre Jeedom) ainsi que votre mode d'installation (depuis le market stable ou bêta ? depuis github branche master ou develop ?)

Encore un avertissement !
===
* La mise à jour des valeurs (état, puissance, consommation) se fait sur demande
avec la commande "rafraîchir", à la sauvegarde de votre équipement, à l'appel
d'une action (switch On/Off) et via le cron en fonction de la fréquence que vous avez paramétré.
Cela signifie donc qu'une modification de l'état directement sur l'équipement
ou via l'application Kasa ne sera prise en compte qu'après votre délais de rafraîchissement (ou par action
manuellement de votre part sur Jeedom).
