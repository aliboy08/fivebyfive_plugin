param(
    [string]$source_path,
    [string]$file_name,
    [string]$exclude
)

# Resolve full paths
$sourcePath = (Resolve-Path $source_path).Path
$destinationPath = Join-Path (Split-Path $sourcePath -Parent) ($file_name + '.zip')

# Convert exclude string to array
$excludePatterns = $exclude -split ';' | Where-Object { $_ -ne '' }

# Function to check if a file/folder should be excluded
function Should-Exclude {
    param (
        [string]$fullPath,
        [string]$sourcePath,
        [string[]]$patterns
    )

    $relativePath = $fullPath.Substring($sourcePath.Length).TrimStart('\','/')
    foreach ($pattern in $patterns) {
        if ($relativePath -like "$pattern*" -or $relativePath -like "*\$pattern\*") {
            return $true
        }
    }
    return $false
}

# Check if there are files after exclusion
$allFiles = Get-ChildItem -Path $sourcePath -Recurse -Force | Where-Object {
    -not (Should-Exclude -fullPath $_.FullName -sourcePath $sourcePath -patterns $excludePatterns)
}

if ($allFiles.Count -eq 0) {
    Write-Warning "No files found to zip. Check your exclude patterns."
    exit
}

# Create a temporary folder
$tempDir = [System.IO.Path]::Combine([System.IO.Path]::GetTempPath(), [System.IO.Path]::GetRandomFileName())
New-Item -ItemType Directory -Path $tempDir -Force | Out-Null

# Copy the **entire module folder** into the temp container
$containerPath = Join-Path $tempDir $file_name
Copy-Item -Path $sourcePath -Destination $containerPath -Recurse -Force

# Remove excluded files/folders from temp container
foreach ($pattern in $excludePatterns) {
    Get-ChildItem -Path $containerPath -Recurse -Force -Include $pattern | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue
}

# Use 7-Zip to zip the container folder including the folder itself
$sevenZipPath = "C:\Program Files\7-Zip\7z.exe"  # Adjust if necessary
$arguments = @(
    "a",
    "`"$destinationPath`"",
    "`"$containerPath`"",
    "-r"
)

try {
    & $sevenZipPath @arguments
    Write-Output "Zip file created successfully at $destinationPath"
} catch {
    Write-Error "Failed to create zip file: $_"
}

# Clean up temp folder
Remove-Item -Path $tempDir -Recurse -Force