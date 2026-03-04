@echo off

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd_HHmmss"') do set "timestamp=%%i"
set "api_key=N8nFybEdxaeCKDxJTtkY3RSnuiSR3s4a1as"
set "update_url=https://devlibrary2021.wpengine.com/fivebyfive/update.php?api_key=%api_key%^&dist_version=%timestamp%"

echo Updating dist version...

echo %update_url%
curl "%update_url%"