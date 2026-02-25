@echo off
setlocal

set slug=%1
set version=%2

if "%1%" == "" (
    echo specify module name
    pause
    exit
)

if "%2%" == "" (
    echo specify version
    pause
    exit
)

set "modules_path=%CD%\dev\modules"

set "source_path=%modules_path%\%slug%"

if not exist "%source_path%\" (
    echo directory not found:
    echo %source_path%
    pause
    exit
)

set "zip_file=%slug%.zip"
set "zip_file_path=%modules_path%\%zip_file%"

set "install_name=devlibrary2021"
set "remote_path=/sites/%install_name%/fivebyfive/modules/"
set "ssh_host=%install_name%@%install_name%.ssh.wpengine.net"

set "exclude=node_modules;dev;.git;v4wp;zip.ps1;publish.bat;.gitignore;jsconfig.json;package.json;package-lock.json;wp-manifest.cjs;vite.config.js;%zip_file%"

echo %slug% (%version%)

echo Creating zip file...
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0zip.ps1" -source_path "%source_path%" -file_name "%slug%" -exclude "%exclude%"

echo zip_file_path: %zip_file_path%

echo Uploading to remote (%remote_path%)...

scp -O "%zip_file_path%" %ssh_host%:%remote_path%

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd_HHmmss"') do set "timestamp=%%i"

set "api_key=N8nFybEdxaeCKDxJTtkY3RSnuiSR3s4a1as"
set "update_url=https://devlibrary2021.wpengine.com/fivebyfive/modules/update.php?slug=%slug%^&version=%version%^&api_key=%api_key%^&t=%timestamp%"
echo update module version...
curl "%update_url%"

:end
@REM pause
endlocal