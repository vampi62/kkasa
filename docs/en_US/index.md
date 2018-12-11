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
If you have problems, you can tell me on github issues or jeedom forum dedicated topic.
Thank you in advance to activate the "debug" log level for the plugin. A new "Debug Infos" button will appear on the plugin page. Press it. Reproduce your mistake. Then send the result of the  kkasa log.

Another warning!
===
* The information "current" sometimes seems inconsistent. However, the plugin only display the data as they are sent by the Kasa server. It is therefore rather adviser to trust the "power" data.

* The updating of the values ​​(state, power, consumption) is done on request
with the "refresh" command, when saving your equipment, on the call
of an action (switch On / Off) and via the cron every 15min.
This means that a change of state directly from physical device 
or via the Kasa application will only be taken into account after 15min (or per manual action 
 from you on Jeedom).
