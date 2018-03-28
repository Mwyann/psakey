This guide will let you make an Raspberry Pi Zero W act like a connected key from PSA
(commercial names are Peugeot Connect Apps - Citroën Multicity Connect). The service will be/is discontinued
since March 2018, although the system on the car still works perfectly.

The car's system consists of a simple Internet Explorer 6 Mobile, so it is able to display HTML pages with
images, CSS support, Javascript (jQuery 1.x works). This project could lead to a new kind of connected
service, open-source and community-driven.

Of course, this project isn't aimed at enabling Peugeot/Citroën commercial services for free,
its purpose is to enable people to make their own services by developing (or installing) them on their own hardware.


Quick install from scratch
==========================

- Download and install latest raspbian stretch lite on a sdcard: https://www.raspberrypi.org/downloads/raspbian/
- Start your RPi with HDMI and a keyboard plugged in via USB OTG
- Connect with user "pi" and password "raspberry" (beware non-QWERTY keyboards)
- Execute `sudo raspi-config`, and follow these steps:
  - Update raspi-config
  - Localisation Options > Change Keyboard Layout > Choose your keyboard layout
  - Network > Wifi > Enter SSID/passphrase
  - Change password (recommended)
  - Boot Options > Wait for Network > No
  - Interfacing > SSH > Yes
  - Advanced > Expand
  - Advanced > Memory Split > 16
  - Finish and reboot the RPi
- Grab the RPi's Wifi IP and make sure you can connect to it with SSH, like `ssh pi@192.168.0.20`
- Once connected to SSH:
  - `sudo apt-get update`
  - `sudo apt-get upgrade`
  - `sudo apt-get install git`
  - `git clone https://github.com/Mwyann/psakey.git`
  - `cd psakey`
  - `sudo bash install_psakey.sh`
  - `poweroff`
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
  (for example `ifconfig usb0 192.168.0.1`) and then `ssh pi@192.168.0.2`.

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

Updating kernel
---------------

If you want to update to the latest supported kernel, just run the update_kernel.sh script.

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

Building the usb_f_eem module
-----------------------------

If the usb_f_eem module is missing for your kernel, here is a quick cross-compilation how-to:

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

