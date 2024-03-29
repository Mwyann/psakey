This guide will let you make an Raspberry Pi Zero W act like a connected key from PSA
(commercial names are Peugeot Connect Apps - Citroën Multicity Connect). The service will be/is discontinued
since March 2018, although the system on the car still works perfectly.

The car's system consists of a simple Internet Explorer 6 Mobile, so it is able to display HTML pages with
images, CSS support, Javascript (jQuery 1.x works). This project could lead to a new kind of connected
service, open-source and community-driven.

Of course, this project isn't aimed at enabling Peugeot/Citroën commercial services for free,
its purpose is to enable people to make their own services by developing (or installing) them on their own hardware.

Join our Discord server: https://discord.gg/yPcR2ca6xW

Quick install from scratch
==========================

- Download and flash latest _Raspberry Pi OS Lite_ on a sdcard: https://www.raspberrypi.org/software/operating-systems/
- If you want to use keyboard and HDMI:
  - Start your RPi with HDMI and a keyboard plugged in via USB OTG
  - Setup your username/password
- OR if you want to use an USB Ethernet adapter:
  - Before starting up your newly flashed sd card, mount the boot partition
  - At the root of that partition, create a new empty file named `ssh`
  - At the root of that partition, create a new file named `userconf.txt`, containing the following line:
    - `psa:$6$2/fgsJ9WYJIfog91$p7ABUrTQIxnlKUbzH5A6Y0NR6d2nYY/UnJ2trtEM8bzLzUMc0XqKAepCLLx0ij0KFxObp6w2Yz1Y.d04rboRD.`
  - This will create the user `psa`, with password `psa` (you can change it later).
  - Plug everything up, start up your RPi, find its IP and connect to it with `ssh psa@192.168.X.Y`
- Execute `sudo raspi-config`, and follow these steps (updated as of Raspberry Pi OS Lite May 3rd 2023):
  - Localisation Options > Keyboard > Choose your keyboard layout (if you're using a keyboard)
  - Localisation Options > Timezone
  - System Options -> Wireless LAN > Enter SSID/passphrase
  - System Options -> Password (if needed)
  - System Options -> Network at Boot -> No
  - Interface Options -> SSH > Yes
  - Advanced Options -> Expand Filesystem
  - Performance Options -> GPU Memory > 16
  - Finish and reboot the RPi
- Grab the RPi's Wifi IP and make sure you can connect to it with SSH, like `ssh psa@192.168.X.Y`
- Once connected to SSH:
  - `sudo apt-get update`
  - `sudo apt-get upgrade`
  - `sudo apt-get -y install git`
  - `git clone https://github.com/Mwyann/psakey.git`
  - `cd psakey`
  - `sudo bash install_psakey.sh`
  - `sudo poweroff`
- Plug the RPi's Micro-USB connector (data, not power) on your car using a standard Micro-USB cable.
- On the SMEG+ screen, select the Internet menu, it should look like this:

![SMEG+ Screen](/helloworld.jpg)


More information
================

Before installation
-------------------

This script will enable the USB gadget feature on the Raspberry Pi, thus it will disable the standard USB OTG.
If you wish to connect to the Raspberry while it's plugged into your car (for testing purposes or anything), you should
configure the WLAN by filling the `/etc/wpa_supplicant/wpa_supplicant.conf` file correctly.

If you forgot to do this after running the installation script, you have two options to get back into your RPi:
- Remove the SD card, put it into another Linux computer, mount the root partition and make the changes.
- Connect the RPi into another Linux computer, you'll see an usb0 connection showing. Configure it to be IP 192.168.0.1
  (for example `ifconfig usb0 192.168.0.1`) and then `ssh psa@192.168.0.2`.

Installation
------------

The `install_psakey.sh` script will take care of all the configuration you need to get you started with your PSA key installation.
You can configure the path of the root of the PSAKEY website that will be served to your car by editing the install script
and changing the `PSAKEYROOT` variable at the top.

After installation
------------------

When installation is done, you can reboot your RPi to make all changes active. Please note that the script configures the
sdcard partitions to be read-only because the RPi will be powered off by losing power when operated on the car, so to avoid filesystem
corruption, I force a read-only mounting, and I specify some directories to be mounted as tmpfs (on RAM).
If you need to remount read-write, you can run the `remount` script (installed in /usr/sbin/) and it will remount the
root filesystem as read-write for you. Don't forget to power off or reboot your Rpi properly to avoid problems.

If your SMEG+ displays the Hello World test correctly, then congratulations, you're good to go!
You can now write HTML code for your car to display, just edit the PSAKEYROOT/index.html file on your key.

Also, some SD Cards have problems with the read-only mounting in linux and the device does not boot after the psakey install.
[Ektilth](https://github.com/Ektilth) faced this issue with cheap noname cards and also with a Verbatim card. But a Sandisk Ultra worked fine every time for him.
So if you see this error when you connect the RPI with HDMI to a monitor, try another card.

Updating kernel
---------------

If you have to update to the latest kernel, run the `remount` command, then update/upgrade using apt,
and finally run `sudo build_eem.sh` again to build the newer version of the module.

Emulator
--------

You can emulate the SMEG+ browser using Electron, so you don't have to develop in your car :-)

- Go to https://github.com/electron/electron/releases and download the latest version for your platform
- Uncompress the archive and create the `resources/app` folder
- Copy the `resources/app` folder of this repository into the `resources/app` folder of Electron
- Open the main.js file, search for "SET.PSAKEY.IP.HERE", and change it for the IP of your PSAKey's Wifi IP
- Run Electron, and watch it :)
- The fonts are not reliably displayed yet compared to the real SMEG browser, this is on the TODO.
- The dev tools are opened right away, if you close it you can reopen it with F12, and you can push F5 to clear cache and reload the index.

SMEG+ API
---------

Of course that wouldn't be much fun if you couldn't use some JavaScript API functions of your SMEG+.
I plan to write a documentation and/or JavaScript function set to use the car's API, but here are some examples (you can find more in the `psakey/emulator.html` file):

- top.GetCar.VIN();
- top.GetCar.DrivingState();
- top.GetCar.FuelType(); (3 = gazoline)
- top.GetCar.Speed();
- top.GetCar.Mileage();
- top.GetCar.Autonomy();
- top.JavascriptBinding.LaunchGuidance(lat, lng);
- top.JavascriptBinding.LaunchPhoneCall(phonenumber);
- And others, to know what you're listening to, where your GPS is set to, open a notification, save a POI, etc.

A great reference to look at is the official dev portal https://developer.groupe-psa.io/webportal/v1/api-reference/list/ where you can get the full list with arguments and explanations. Some of those API calls may *not* be available to your car model though.

Useful projects:
-----------------------------

Here is another github project that uses psakey to display some useful informations:
https://github.com/Ektilth/smegpowerup

I've provided an application example showing realtime obd2 data from an OBD2 bluetooth adapter, you can find the source code under the `obd2` folder, and a video example here: https://youtu.be/RiFUbXsVagI 

Building the usb_f_eem module manually
-----------------------------

If the usb_f_eem module is missing for your kernel, you can automatically build the module from your device with **build_eem.sh**
or cross-compile it with this quick how-to:

Download toolchain:
```
git clone https://github.com/raspberrypi/tools ~/tools
```

Setup toolchain in path:
```
export PATH=$PATH:~/tools/arm-bcm2708/gcc-linaro-arm-linux-gnueabihf-raspbian-x64/bin
```

Download and extract same kernel version from https://www.kernel.org/

Get kernel config from Raspberry:
```
modprobe configs
```
(grab /proc/config.gz via scp for example)
```
gunzip config.gz
mv config .config
```

Enable USB Gadget EEM (under Device Drivers > USB support > USB Gadget Support > Ethernet Gadget):
```
make ARCH=arm CROSS_COMPILE=arm-linux-gnueabihf- menuconfig
```

Download kernel's Module.symvers:
```
wget https://raw.githubusercontent.com/Hexxeh/rpi-firmware/955fa1d6e8cd8c94ad8a6680a09269d9bd2945c5/Module.symvers
```

Make module:
```
make ARCH=arm CROSS_COMPILE=arm-linux-gnueabihf- SUBDIRS=drivers/usb/gadget/function/
```

Copy file `drivers/usb/gadget/function/usb_f_eem.ko` on the Raspberry `/lib/modules/4.14.29+/kernel/drivers/usb/gadget/function/` folder, then run:
```
depmod
```

