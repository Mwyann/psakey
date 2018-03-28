#!/bin/bash

PSAKEYROOT=/var/www/psakey

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

if [ "`grep ^dtoverlay=dwc2 /boot/config.txt`" = "" ]; then
echo dtoverlay=dwc2 >> /boot/config.txt
fi

echo "Preparing installation..."
apt-get update

echo "Installing Apache2..."
apt-get install apache2

echo "Enabling needed Apache2 modules..."
# Headers are needed to set special headers like Access-Control-Allow-Origin.
a2enmod headers
# Proxy is needed to allow Internet access from the car
a2enmod proxy_http
# Rewrite is needed to redirect some special files
a2enmod rewrite

echo "Installing Apache2 configuration..."
sed "s#PSAKEYROOT#$PSAKEYROOT#" < resources/psakey.conf > /etc/apache2/sites-available/psakey.conf
a2ensite psakey
mkdir /usr/lib/systemd/system
cp resources/myapache2 /usr/bin/myapache2
chmod +x /usr/bin/myapache2
cp resources/myapache2.service /usr/lib/systemd/system/
systemctl enable myapache2

echo "Installing base PSAKey folder..."
cp psakey $PSAKEYROOT -R
systemctl restart apache2

echo "Installing USB gadget script..."
# Installing as a system service to enable it on boot
cp resources/myusbgadget /usr/bin/myusbgadget
chmod +x /usr/bin/myusbgadget
cp resources/myusbgadget.service /usr/lib/systemd/system/
systemctl enable myusbgadget

echo "Installing network related files..."
# USB gadget configuration file
cp resources/usb0 /etc/network/interfaces.d/

echo "Setting up the read-only filesystem..."
# We need to setup filesystems as readonly because the RPi will be shut down without notice
cp /etc/fstab /etc/fstab.bak
sed 's/^\(PARTUUID.*defaults\)/\1,ro/' < /etc/fstab.bak > /etc/fstab
# Add some folders as tmpfs (logs, tmp...)
cat resources/fstab.tmp >> /etc/fstab
# this remount script can be called to mount root filesystem as read-write again
cp resources/remount /usr/sbin/remount
chmod +x /usr/sbin/remount
mv /etc/resolv.conf /tmp/resolv.conf
ln -s /tmp/resolv.conf /etc/resolv.conf

echo "Installation is done! You can reboot to apply the configuration,"
echo "then plug the key into your car's USB port and navigate to the Internet application."
