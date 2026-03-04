set zip_file=dist.zip

@REM powershell -Command "Compress-Archive -Path 'dist' -DestinationPath '%zip_file%' -Force"

"C:\Program Files\7-Zip\7z.exe" a -tzip "%zip_file%" "dist"

call upload_zip.bat "%zip_file%"

call update_dist_version.bat