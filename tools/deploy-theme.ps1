param(
    [string]$ConfigPath = 'F:\seo\conn.php',
    [string]$RemoteRoot = '/www.formulapaddock.it/wp-content/themes/f1-speed-seo',
    [switch]$WhatIf
)

$ErrorActionPreference = 'Stop'
$themeRoot = Split-Path $PSScriptRoot -Parent

if (-not (Test-Path -LiteralPath $ConfigPath -PathType Leaf)) {
    throw "File di configurazione non trovato: $ConfigPath"
}

$settings = @{}
Get-Content -LiteralPath $ConfigPath | ForEach-Object {
    $line = $_.Trim()
    if ($line.StartsWith('$') -and $line.Contains('=')) {
        $parts = $line.Split('=', 2)
        $name = $parts[0].Trim().TrimStart('$')
        $value = $parts[1].Trim().TrimEnd(';').Trim().Trim("'").Trim('"')
        $settings[$name] = $value
    }
}

$ftpHost = $settings.ftp_host -replace '^ftp://', '' -replace '/+$', ''
if (-not $ftpHost -or -not $settings.ftp_user -or -not $settings.ftp_passw) {
    throw 'I parametri FTP in conn.php sono incompleti.'
}

$credential = [System.Net.NetworkCredential]::new($settings.ftp_user, $settings.ftp_passw)

function New-FtpRequest([string]$RemotePath, [string]$Method) {
    $escapedPath = (($RemotePath -split '/') | ForEach-Object { [Uri]::EscapeDataString($_) }) -join '/'
    $request = [System.Net.FtpWebRequest]::Create([Uri]("ftp://$ftpHost$escapedPath"))
    $request.Method = $Method
    $request.Credentials = $credential
    $request.UsePassive = $true
    $request.UseBinary = $true
    $request.KeepAlive = $false
    $request.Timeout = 30000
    return $request
}

function Ensure-FtpDirectory([string]$RemotePath) {
    try {
        $request = New-FtpRequest $RemotePath ([System.Net.WebRequestMethods+Ftp]::MakeDirectory)
        $response = $request.GetResponse()
        $response.Close()
    }
    catch [System.Net.WebException] {
        # La cartella esiste già nella maggior parte dei casi.
    }
}

$excludedDirectoryNames = @('.git', '.github', '.vs', 'node_modules', 'tools')
$excludedFileNames = @('.gitignore', '.DS_Store', 'Thumbs.db', 'README.md')
$files = Get-ChildItem -LiteralPath $themeRoot -File -Recurse | Where-Object {
    $relative = [IO.Path]::GetRelativePath($themeRoot, $_.FullName)
    $segments = $relative -split '[\\/]'
    -not ($segments | Where-Object { $_ -in $excludedDirectoryNames }) -and
    $_.Name -notin $excludedFileNames
}

Write-Host "Tema locale: $themeRoot"
Write-Host "Destinazione: ftp://$ftpHost$RemoteRoot/"
Write-Host "File da pubblicare: $($files.Count)"

if ($WhatIf) {
    $files | ForEach-Object { Write-Host ("SIMULAZIONE  " + [IO.Path]::GetRelativePath($themeRoot, $_.FullName)) }
    return
}

$createdDirectories = [System.Collections.Generic.HashSet[string]]::new()
foreach ($file in $files) {
    $relative = [IO.Path]::GetRelativePath($themeRoot, $file.FullName).Replace('\', '/')
    $relativeDirectory = [IO.Path]::GetDirectoryName($relative).Replace('\', '/')
    if ($relativeDirectory -and $createdDirectories.Add($relativeDirectory)) {
        $current = $RemoteRoot
        foreach ($segment in ($relativeDirectory -split '/')) {
            $current = "$current/$segment"
            Ensure-FtpDirectory $current
        }
    }

    $remoteFile = "$RemoteRoot/$relative"
    $request = New-FtpRequest $remoteFile ([System.Net.WebRequestMethods+Ftp]::UploadFile)
    $request.ContentLength = $file.Length
    $requestStream = $request.GetRequestStream()
    $inputStream = [IO.File]::OpenRead($file.FullName)
    try { $inputStream.CopyTo($requestStream) }
    finally {
        $inputStream.Close()
        $requestStream.Close()
    }
    $response = $request.GetResponse()
    $response.Close()
    Write-Host "PUBBLICATO   $relative"
}

Write-Host 'Pubblicazione completata.'
