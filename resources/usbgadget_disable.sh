#!/bin/bash

if [ "$(whoami)" != "root" ]; then
	echo "This script needs to be run as root. Please use sudo."
	exit 1
fi

echo "Stopping USB Gadget..."

echo "" > /sys/kernel/config/usb_gadget/g/UDC
# sh -c "echo '' > /sys/kernel/config/usb_gadget/g/UDC"
