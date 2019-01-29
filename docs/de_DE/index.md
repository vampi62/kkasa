Description
===

Plugin permettant de piloter des objets connectés TP-Link via la plateforme
du fabriquant : [Kasa](https://www.tp-link.com/us/kasa-smart/kasa.html).
A ce jour, seule les prises connectées HS100 et HS110 (v1&v2) sont traités.

Avertissements
===

Je n'ai pas la vocation d'assurer une maintenance long terme de ce plugin.
Je l'ai avant tout créé pour moi-même. Une fois stable, je l'ai partagé sur le
market afin d'en faire profiter d'autres. Cependant :

-   J'ai rarement le temps de me mettre sur des développements persos. Donc si
    vous rencontrez un problème, n'hésitez pas à participer au sujet dédié
    sur le forum ou sur les "issues" Github. Merci de regarder la section [Debug](#Debug) ci-dessous pour m'envoyer les bonnes informations.
    Avec un peu de chance j'aurai le temps et la motivation pour
    vous aider... mais je ne m'engage en rien.

-   Comme indiqué précédemment, ce plugin ne prend en charge que les prises HS100 et HS110 (v1 & v2).
    La raison est simple : c'est tout ce que j'ai chez moi. Je serai ravi de
    traiter d'autres périphériques... mais il faudra pour cela me les offrir ;)
    Cependant, si vous tentez l'utilisation du plugin avec un autre matériel,
    n'hésitez pas à m'informer sur le forum si ça fonctionne correctement ou
    pas.

-   Ce développement est en Open Source (AGPL) et disponible sur github. Je
    serais ravi de donner la main à d'autres contributeurs. N'hésitez pas à me
    faire signe si vous êtes intéressés.

Prérequis
===
Pour le moment, le plugin nécessite que vous ayez correctement installé votre
équipement et configuré sa connexion avec Kasa (avec l'application mobile Kasa).


Installation
===
Suite à l'installation et activation du plugin, rendez-vous sur la page de
configuration pour installer les dépendances.

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
