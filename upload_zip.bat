@echo off

set file=%1
set sub_dir=%2

set "install_name=devlibrary2021"
set "remote_path=/sites/%install_name%/fivebyfive/"
set "ssh_host=%install_name%@%install_name%.ssh.wpengine.net"

if NOT "%sub_dir%"=="" (
    set "remote_path=%remote_path%%sub_dir%"
)

echo Uploading [%file%] to [%remote_path%] ...

scp -O %1 %ssh_host%:%remote_path%