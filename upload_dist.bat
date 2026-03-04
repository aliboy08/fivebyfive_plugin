set zip_file=dist.zip

powershell -Command "Compress-Archive -Path 'dist' -DestinationPath '%zip_file%' -Force"

call upload_zip.bat "%zip_file%"

call update_dist_version.bat