@echo off

call npm run build

set folder=fivebyfive
set zip_file=%folder%.zip

if exist %folder% rmdir /s /q %folder%
mkdir %folder%

xcopy fivebyfive.php %folder%\
xcopy data.json %folder%\
xcopy src %folder%\src /E /I
xcopy dist %folder%\dist /E /I

"C:\Program Files\7-Zip\7z.exe" a -tzip "%CD%\%folder%.zip" "%CD%\%folder%"

rmdir /s /q %folder%

call upload_zip.bat "%folder%.zip"
call update_dist_version.bat