Description
===

Plugin for controlling TP-Link connected objects via the manufacturer's platform: [Kasa](https://www.tp-link.com/us/kasa-smart/kasa.html).
Today, only the connected plugs HS100 and HS110 (v1 & v2) are processed.

Warnings
===

-   Up to now, only the HS100 and HS110 (v1 & v2) devices are supported.
-   If you encounter an issue, feel free to participate in the dedicated topic on the forum or on "issues" Github.
    Please see the [Debug] (# Debug) section below to send me the required details.

Prerequisites
===
Up to now, the plugin requires that you have correctly installed your
equipment and configured its connection with Kasa (with the Kasa mobile application).


Setup
===
After installing and activating the plugin, go to the configuration page to install the dependencies.

Then enter your Kasa credentials and select the desired refresh rate.

**Warning:** I have no information about a possible limitation on the number of Kasa API requests, but if it exists TP-Link may be blocking your requests deemed too frequent and considered abusive.
For the moment, I have limited myself to a refresh rate of 15min in my tests without worries.

Therefore you remain responsible for your use, the choice of this parameter and the possible consequences that this would imply.

Save, then go to the plugin page (in the "IoT" category).

Press "Synchronize with Kasa" and you should see your devices appear.

Et voila!

Debug
===
If you encounter any problems, you can put it back on github issues or forum jeedom.
Thank you in advance for:
* enable log level "debug" on the plugin.
* Reproduce your issue.
* A new "Debug Info" button appeared on the plugin page. Press it.
* Then send the result of the kkasa log.
**Required:** Please indicate to me the KKASA and KKPA versions used (see the "health" page on your jeedom) as well as your installation method (from the stable market or beta? From github branch master or develop? )

Another warning!
===
* Owners of HS110 may have noticed that the power was not always equal to the product of the voltage and the intensity. The reason lies in the difference between active and apparent power.

More information on the [page wikipedia] (https://en.wikipedia.org/wiki/Power_in_r%C3%A9gime_alternatif) or this [little cartoon very well done] (https://www.youtube.com/watch? v = IURKavCBUkE).

* The update of the values (state, power, consumption) is done on request
with the "refresh" command, when saving your equipment, on the call
an action (switch On / Off) and via the cron according to the frequency you have set.
This means that a change of state directly on the device
or via the Kasa mobile app will only be taken into account after your refresh period (or 
manually from you on Jeedom).
