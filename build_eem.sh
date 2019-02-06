#!/bin/bash

# check rpi-source
if [ -z "$(which rpi-source)" ]; then
	sudo apt install bc
	wget https://raw.githubusercontent.com/notro/rpi-source/master/rpi-source -O /usr/bin/rpi-source && sudo chmod +x /usr/bin/rpi-source && /usr/bin/rpi-source -q --tag-update
fi

# around 150 Mo
rpi-source --nomake
cd /lib/modules/$(uname -r)/source

# enabling USB EEM module in config
sed -i "s/# CONFIG_USB_ETH_EEM is not set/CONFIG_USB_ETH_EEM=y/" .config

# building module
make  drivers/usb/gadget/function/usb_f_eem.ko
# installing module
sudo mv drivers/usb/gadget/function/usb_f_eem.ko /lib/modules/$(uname -r)/kernel/drivers/usb/gadget/function/
sudo depmod
cd -

