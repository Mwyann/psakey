#!/bin/bash

if [ "`whoami`" != "root" ];
then
echo "This script needs to be run as root. Please use sudo."
exit 1
fi

echo "Installing 4.14.29+ kernel..."

rpi-update 955fa1d6e8cd8c94ad8a6680a09269d9bd2945c5

echo "Installing USB gadget kernel module..."

KVER=`uname -r`
if [ ! -d "resources/kernel/$KVER" ]; then
echo "Module usb_f_eem.ko for your kernel version $KVER is unavailable. Please compile it yourself (see README)."
exit 1
fi
cp -R resources/kernel/$KVER/* /lib/modules/$KVER/
depmod

echo "Done! You can reboot."
