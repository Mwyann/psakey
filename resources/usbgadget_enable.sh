#!/bin/bash

if [ "$(whoami)" != "root" ]; then
	echo "This script needs to be run as root. Please use sudo."
	exit 1
fi

echo "Starting USB Gadget..."

if [ ! -d "/sys/kernel/config/usb_gadget/g/configs/c.1" ]; then
	# usb gadget not yet created
	/usr/local/bin/myusbgadget
fi

ls /sys/class/udc/ > /sys/kernel/config/usb_gadget/g/UDC

