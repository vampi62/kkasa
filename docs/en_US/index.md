Description
===

Plugin for controlling TP-Link connected objects via the manufacturer's platform: [Kasa](https://www.tp-link.com/us/kasa-smart/kasa.html).
Today, only the connected plugs HS100 and HS110 (v1 & v2) are processed.

Warnings
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
    to deal with other devices... but you will have to offer them to me (thanks by advance!) ;)
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

Then enter your Kasa credentials and select the desired refresh rate.

**Warning:** I have no information about a possible limit on the number of Kasa API requests, but if it exists TP-Link may be blocking your requests deemed too frequent and considered abusive.
For the moment, I have limited myself to a refresh rate of 15min during my tests without worries.

You therefore remain responsible for your use, the choice of this parameter and the possible consequences that this would imply.

Save, then go to the plugin page (in the "connected objects" category).

Press "Synchronize with Kasa" and you should see your equipment.

Et voila!

Debug
===
If you encounter any issue, you can report it back on github issues or jeedom forum.
Thank you in advance for:
* enable "debug" log level on the plugin.
* Reproduce the issue.
* A new "Debug Info" button appeared on the plugin page. Press it.
* Then send the result of the log kkasa.

Another warning!
===
* Owners of HS110 may have noticed that the power is not always equal to the product of the voltage and the intensity. The reason lies in the difference between active and apparent power.

More information on the [page wikipedia] (https://en.wikipedia.org/wiki/AC_power). __

* The values update (state, power, consumption) is done on request
with the "refresh" command, when saving your equipment, on the action call
 (switch On / Off) and via the cron according to the refresh frequency you have set.
This means that a change of state directly from the equipment
or from the Kasa app will only be taken into account after your refresh period (or by action call 
from you on Jeedom).
