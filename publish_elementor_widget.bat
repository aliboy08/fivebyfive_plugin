@echo off
setlocal

set slug=%1

if "%1%" == "" (
    echo specify widget
    pause
    exit
)

set "widgets_path=%CD%\dev\elementor_widgets"

set "source_path=%widgets_path%\%slug%"

if not exist "%source_path%\" (
    echo directory not found:
    echo %source_path%
    pause
    exit
)

set "zip_file=%slug%.zip"
set "zip_file_path=%widgets_path%\%zip_file%"

set "install_name=devlibrary2021"
set "remote_path=/sites/%install_name%/fivebyfive/elementor_widgets/"
set "ssh_host=%install_name%@%install_name%.ssh.wpengine.net"

set "exclude=node_modules;dev;.git;v4wp;zip.ps1;publish.bat;.gitignore;jsconfig.json;package.json;package-lock.json;wp-manifest.cjs;vite.config.js;%zip_file%"

echo Creating zip file...
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0zip.ps1" -source_path "%source_path%" -file_name "%slug%" -exclude "%exclude%"

echo.
echo zip_file_path: %zip_file_path%
echo.
echo Uploading to remote (%remote_path%)...

scp -O "%zip_file_path%" %ssh_host%:%remote_path%

:end
@REM pause
endlocal