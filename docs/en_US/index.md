Description
===

This plugin allow you to manage your TP-link IoT by using the vendor platform : [Kasa](https://www.tp-link.com/us/kasa-smart/kasa.html).
Today, only HS110 connected plug is managed

Avertissements
===

Je n'ai pas la vocation d'assurer une maintenance long terme de ce plugin.
Je l'ai avant tout créé pour moi-même. Une fois stable, je l'ai partagé sur le
market afin d'en faire profiter d'autres. Cependant :

-   J'ai rarement le temps de me mettre sur des développements persos. Donc si
    vous rencontrez un problème, n'hésitez pas à participer au sujet dédié
    sur le forum. Avec un peu de chance j'aurai le temps et la motivation pour
    vous aider... mais je ne m'engage en rien.

-   Comme indiqué précédemment, ce plugin ne prend en charge que la prise HS110.
    La raison est simple : c'est tout ce que j'ai chez moi. Je serai ravi de
    traiter d'autres périphériques... mais il faudra pour cela me les offrir ;)
    Cependant, si vous tentez l'utilisation du plugin avec un autre matériel,
    n'hésitez pas à m'informer sur le forum si ça fonctionne correctement ou
    pas.

-   Ce développement est en Open Source (AGPL) et disponible sur github. Je
    serais ravi de donner la main à d'autres contributeurs. N'hésitez pas à me
    faire signe si vous êtes intéressés.

Prerequisites
===
For  moment, the plugin requires that you have correctly installed your
equipment and configured its connection with Kasa (with the Kasa mobile application).


Setup
===
After the installation and activation of the plugin, go to the page of
configuration to install the dependencies.

P.S .: For obvious reasons, sometimes dependencies are
marked "OK" when they are not. In doubt always press
"restart".

Then enter your Kasa credentials.

Save, then go to the plugin page (in the "home automation" category).

Tap "Synchronize with Kasa". Once the synchronization is successful, 
you have to refresh the page to see your equipments.

There you go !

Warning
===
The update of the values ​​(state, power, consumption) is done on request
with the "refresh" command, when saving your equipment, on the action
execution (like switch On / Off) and by the cron every 15min.
This means that a change of state directly on the equipment
or via the Kasa application will only be taken into account after 15min (or by an action
manually executed by you on Jeedom).
