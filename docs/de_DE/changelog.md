Version 2.0 "Poisson d'avril" - 01/04/2019
===
**Evolutions :**
* Gestion des ampoules connectées TP-Link (LB100, LB120, LB130)
* Gestion du mode local (Connexion au cloud non-obligatoire)
* Gestion des LED
* Rafraîchissement des commandes
* Auto-détection des adresses IP
* Configuration du niveau de log pour les équipements offline

Version 1.1 "Chandeleur" - 04/02/2019
===
**Evolutions :**
* Plusieurs tentatives avant échec de la requête
* Gestion des fréquences de rafraîchissement auto
* Rafraîchissement de page automatique après synchro Kasa
* Ajout force du signal
* Compatibilité avec KKPA v2

**Corrections :**
* HS100 : désactivation des requêtes de consommation (réduction des erreurs)
* Ignore les prises offline lors de la synchro Kasa
* Corrections de la page santé

Version 1.0 "Galette des rois" - 08/01/2019
===
**Evolutions :**
* Passage en stable sur le market Jeedom
* Améliorations du debug

**Corrections :**
* Correction de la page "santé"

Version 0.9 "Release candidate" - 11/12/2018
===
**Evolutions :**
* Amélioration des messages debug
* Vérification de la version des dépendances

**Corrections :**
* Gestion des équipements multiples
* Délais de raffraichissement du widget

Version 0.8 "Fête des lumières" - 09/12/2018
===
**Evolutions :**
* Mode debug : activez le niveau de log "debug" sur le plugin pour obtenir un nouveau bouton qui inscrira les informations nécessaires sur le log kkasa

**Corrections :**
* Anonymisation des informations username / password / latitude / longitude dans les logs
* Correction bug lorsque plusieurs prises sont configurées
* Corrections HS110 v1 (remontée de la puissance / conso)
* Prise en charge des exceptions

Version initiale - 18/11/2018
===
