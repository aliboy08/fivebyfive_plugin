@echo off
setlocal

set slug=%1

if "%1%" == "" (
    echo specify widget slug
    pause
    exit
)

set sub_dir=elementor_widgets

set "base_path=%CD%\dev\%sub_dir%"

set "source_path=%base_path%\%slug%"

if not exist "%source_path%\" (
    echo directory not found:
    echo %source_path%
    pause
    exit
)

call npm run build

set "zip_file=%base_path%\%slug%.zip"

powershell -Command "Compress-Archive -Path '%source_path%' -DestinationPath '%zip_file%' -Force"

call upload_zip.bat "%zip_file%" "%sub_dir%"

call upload_dist.bat

:end
endlocal