# Fetches all rows from your Google Sheet and saves to data/jobs.json
# Run: powershell -ExecutionPolicy Bypass -File scripts/sync-jobs-from-sheet.ps1

$SheetId = '1ndJnRvRoTkJcbXCn0gzOOQNPH_XzpcVd6JuIceXqaMo'
$Gid = '274791102'
$OutFile = Join-Path $PSScriptRoot '..\data\jobs.json'
# Optional: paste your Apps Script Web App URL (from google-apps-script/Code.gs deploy)
$AppsScriptUrl = 'https://script.google.com/macros/s/AKfycbyI_DYlw2ki-OkCjImjGqaeaontFMo8FWj6S0gxDNhhVZghPR4vIb7kEut_os9xF1BljQ/exec'

function Find-ColIndex($headers, $names) {
  for ($i = 0; $i -lt $headers.Count; $i++) {
    $h = $headers[$i].ToString().ToLower()
    foreach ($n in $names) {
      if ($h -eq $n.ToLower() -or $h.Contains($n.ToLower())) { return $i }
    }
  }
  return -1
}

function Parse-Gviz($text) {
  if ($text -match 'google\.visualization\.Query\.setResponse\(([\s\S]+)\)\s*;?\s*$') {
    return $Matches[1] | ConvertFrom-Json
  }
  throw 'Invalid Google Sheet JSON response'
}

function Rows-FromGviz($data) {
  if ($data.status -eq 'error') { throw ($data.errors[0].detailed_message) }
  $headers = @($data.table.cols | ForEach-Object { [string]$_.label })
  $col = @{
    timestamp = Find-ColIndex $headers @('timestamp')
    title       = Find-ColIndex $headers @('title')
    description = Find-ColIndex $headers @('description')
    company     = Find-ColIndex $headers @('company')
    category  = Find-ColIndex $headers @('category')
    location  = Find-ColIndex $headers @('location (select city)', 'location')
    area      = Find-ColIndex $headers @('area / locality', 'area')
    salary    = Find-ColIndex $headers @('salary')
    experience = Find-ColIndex $headers @('experience')
  }
  $jobs = @()
  $idx = 0
  foreach ($row in $data.table.rows) {
    $cells = $row.c
    $get = {
      param($i)
      if ($i -lt 0 -or $null -eq $cells[$i]) { return '' }
      $c = $cells[$i]
      if ($null -ne $c.f) { return [string]$c.f }
      if ($null -ne $c.v) { return [string]$c.v }
      return ''
    }
    $title = & $get $col.title
    $company = & $get $col.company
    if (-not $title -and -not $company) { continue }
    $idx++
    $jobs += [ordered]@{
      id         = [string]$idx
      timestamp  = & $get $col.timestamp
      title       = $title
      description = & $get $col.description
      company     = $company
      category   = if (& $get $col.category) { & $get $col.category } else { 'General' }
      location   = & $get $col.location
      area       = & $get $col.area
      salary     = & $get $col.salary
      experience = & $get $col.experience
    }
  }
  return ,$jobs
}

function Normalize-ApiRow($row, $index) {
  $pick = {
    param($names)
    foreach ($n in $names) {
      if ($null -ne $row.$n -and "$($row.$n)" -ne '') { return "$($row.$n)".Trim() }
    }
    return ''
  }
  [ordered]@{
    id         = if ($row.id) { "$($row.id)" } else { [string]($index + 1) }
    timestamp  = & $pick @('timestamp', 'Timestamp')
    title       = & $pick @('title', 'Title')
    description = & $pick @('description', 'Description')
    company     = & $pick @('company', 'Company')
    category   = if (& $pick @('category', 'Category')) { & $pick @('category', 'Category') } else { 'General' }
    location   = & $pick @('location', 'Location (Select city)', 'Location')
    area       = & $pick @('area', 'Area / Locality', 'Area')
    salary     = & $pick @('salary', 'Salary')
    experience = & $pick @('experience', 'Experience')
  }
}

$jobs = @()
try {
  if ($AppsScriptUrl) {
    Write-Host 'Fetching from Apps Script...'
    $raw = Invoke-RestMethod -Uri ($AppsScriptUrl + '?t=' + [DateTimeOffset]::UtcNow.ToUnixTimeSeconds()) -Method Get
    $i = 0
    foreach ($row in $raw) {
      $n = Normalize-ApiRow $row $i
      if ($n.title -or $n.company) {
        $jobs += $n
        $i++
      }
    }
  } else {
    Write-Host 'Fetching from Google Sheet...'
    $url = "https://docs.google.com/spreadsheets/d/$SheetId/gviz/tq?tqx=out:json&gid=$Gid"
    $text = (Invoke-WebRequest -Uri $url -UseBasicParsing).Content
    $data = Parse-Gviz $text
    $jobs = Rows-FromGviz $data
  }
} catch {
  Write-Host "ERROR: $_" -ForegroundColor Red
  Write-Host 'Share sheet as Anyone with link (Viewer) OR set `$AppsScriptUrl` in this script.' -ForegroundColor Yellow
  exit 1
}

$json = $jobs | ConvertTo-Json -Depth 5
$dir = Split-Path $OutFile -Parent
if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir | Out-Null }
[System.IO.File]::WriteAllText($OutFile, $json)
Write-Host "Saved $($jobs.Count) job(s) to data/jobs.json" -ForegroundColor Green
