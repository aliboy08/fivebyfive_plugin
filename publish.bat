@echo off

call npm run build

set zip_file=fivebyfive.zip

powershell -Command "Compress-Archive -Path 'fivebyfive.php','src','dist' -DestinationPath '%zip_file%' -Force"

call upload_zip.bat "%zip_file%"

call update_dist_version.bat

@REM pause