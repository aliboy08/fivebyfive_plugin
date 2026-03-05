@echo off

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd_HHmmss"') do set "timestamp=%%i"
set "update_url=https://devlibrary2021.wpengine.com/fivebyfive/update.php?api_key=N8nFybEdxaeCKDxJTtkY3RSnuiSR3s4a1as^&dist_version=%timestamp%"

echo Updating dist version...

echo timestamp: %timestamp%

node update_dist_version.cjs "%timestamp%"

curl "%update_url%"