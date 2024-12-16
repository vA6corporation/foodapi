#!/bin/bash
file=foodapi
server=34.173.177.156
tar -cf ../$file.tar.xz ../$file
scp ../$file.tar.xz vampi@$server:
ssh vampi@$server tar -xf $file.tar.xz
ssh vampi@$server rm $file.tar.xz
ssh vampi@$server sudo rm -rf /var/www/$file
ssh vampi@$server sudo mv $file /var/www/
