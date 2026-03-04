@echo off
setlocal

set slug=%1

if "%1%" == "" (
    echo specify module slug
    pause
    exit
)

set "base_path=%CD%\dev\modules"

set "source_path=%base_path%\%slug%"

if not exist "%source_path%\" (
    echo directory not found:
    echo %source_path%
    pause
    exit
)

set "json_file=%source_path%\config.json"

for /f "usebackq delims=" %%A in (`powershell -NoProfile -Command ^
    "(Get-Content '%json_file%' | ConvertFrom-Json).version"`) do set "version=%%A"

for /f "usebackq delims=" %%A in (`powershell -NoProfile -Command ^
    "(Get-Content '%json_file%' | ConvertFrom-Json).with_asset"`) do set "with_asset=%%A"

echo %slug% %version%

if "%with_asset%" == "True" (
    call npm run build
    call upload_dist.bat
)

set "zip_file=%base_path%\%slug%.zip"

"C:\Program Files\7-Zip\7z.exe" a -tzip "%zip_file%" "%source_path%"

call upload_zip.bat "%zip_file%" modules

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd_HHmmss"') do set "timestamp=%%i"

set "api_key=N8nFybEdxaeCKDxJTtkY3RSnuiSR3s4a1as"
set "update_url=https://devlibrary2021.wpengine.com/fivebyfive/modules/update.php?slug=%slug%^&version=%version%^&api_key=%api_key%^&t=%timestamp%"
echo updating version...
curl "%update_url%"

endlocal