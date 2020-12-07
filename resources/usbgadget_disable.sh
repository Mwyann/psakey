#!/bin/bash

if [ ! -d /sys/kernel/config/usb_gadget ]; then
        modprobe libcomposite
fi

if [ ! -d /sys/kernel/config/usb_gadget/g1 ]; then
        exit 0
fi

# Yank it back

cd /sys/kernel/config/usb_gadget/g1
echo '' > UDC

sleep 1

rm configs/c.1/*.*
rm os_desc/c.1
rmdir configs/c.1/strings/*
rmdir configs/c.1
rmdir functions/*
rmdir strings/*
cd ..
rmdir g1

