@echo off
setlocal

set slug=%1
set version=%2

rem Generate timestamp
for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd_HHmmss"') do set "timestamp=%%i"

set "api_key=N8nFybEdxaeCKDxJTtkY3RSnuiSR3s4a1as"
set "update_url=https://devlibrary2021.wpengine.com/fivebyfive/modules/update.php?slug=%slug%^&version=%version%^&api_key=%api_key%^&t=%timestamp%"

rem Capture curl response
for /f "usebackq delims=" %%r in (`curl -s "%update_url%"`) do set "response=%%r"

echo Update URL: %update_url%
echo Curl response: %response%

:end
pause
endlocal