#!/bin/bash -e

# http://irq5.io/2016/12/22/raspberry-pi-zero-as-multiple-usb-gadgets/
# https://gist.github.com/geekman/5bdb5abdc9ec6ac91d5646de0c0c60c4
# https://www.kernel.org/doc/Documentation/usb/gadget_configfs.txt
# https://www.kernel.org/doc/Documentation/ABI/testing/configfs-usb-gadget

echo "Configuring USB Gadget..."

modprobe libcomposite

cd /sys/kernel/config/usb_gadget/
mkdir g1 && cd g1

USBMODE="EEM"

if [ "$USBMODE" == "EEM" ]; then
  echo 0x243a > idVendor  # Peugeot
  echo 0x0001 > idProduct # Connect Apps
  echo 0x0226 > bcdDevice # v2.26
  echo 0x0200 > bcdUSB    # USB 2.0

  # Composite support for Windows
  #echo 0xEF > bDeviceClass
  #echo 0x02 > bDeviceSubClass
  #echo 0x01 > bDeviceProtocol

  mkdir -p strings/0x409
  echo "CAFEDECA"                  > strings/0x409/serialnumber
  echo "Aperture Science"          > strings/0x409/manufacturer
  echo "PSAKey"                    > strings/0x409/product
else
  echo 0x8564 > idVendor  # Transcend
  echo 0x4000 > idProduct #
  echo 0x0034 > bcdDevice # 0.34
  echo 0x0200 > bcdUSB    # USB 2.0

  mkdir -p strings/0x409
  echo "000000000020"              > strings/0x409/serialnumber
  echo "TS-RDF5"                   > strings/0x409/manufacturer
  echo "Transcend"                 > strings/0x409/product
fi

echo "Enabling MassStorage function..."
mkdir -p functions/mass_storage.usb0  # mass storage
if [ "$USBMODE" == "EEM" ]; then
  echo "Enabling EEM function..."
  mkdir -p functions/eem.usb0  # network
fi

mkdir -p configs/c.1
echo 500 > configs/c.1/MaxPower
ln -s functions/mass_storage.usb0 configs/c.1/
if [ "$USBMODE" == "EEM" ]; then
  ln -s functions/eem.usb0 configs/c.1/
fi

#echo /dev/mmcblk0p4 > /sys/kernel/config/usb_gadget/g/functions/mass_storage.usb0/lun.0/file
if [ "$USBMODE" == "MS" ]; then
  echo /mnt/data/smegupdate.up > /sys/kernel/config/usb_gadget/g/functions/mass_storage.usb0/lun.0/file
fi
echo 1 > functions/mass_storage.usb0/lun.0/removable

udevadm settle -t 5 || :
ls /sys/class/udc/ > UDC
