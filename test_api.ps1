# API Testing Script for Local License Management Backend
# Run this script using PowerShell: .\test_api.ps1

$BaseUrl = "http://127.0.0.1:8000/api"
$DeviceId = "TEST-HWID-UUID-999"
$CompName = "TEST-DESKTOP-PC"

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host " STARTING BACKEND API ENDPOINT TESTS" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# 1. GET Settings
try {
    Write-Host "1. Testing GET /settings..." -NoNewline
    $res = Invoke-RestMethod -Uri "$BaseUrl/settings" -Method GET
    if ($res.success -eq $true) {
        Write-Host " [SUCCESS]" -ForegroundColor Green
        Write-Host "   Notice: $($res.notice)" -ForegroundColor Gray
        Write-Host "   Trial Days: $($res.trial_days)" -ForegroundColor Gray
        Write-Host "   Heartbeat: $($res.heartbeat_interval)s" -ForegroundColor Gray
    } else {
        Write-Host " [FAILED]" -ForegroundColor Red
    }
} catch {
    Write-Host " [ERROR: $_]" -ForegroundColor Red
}

# 2. POST Auth Device (First Login -> Auto Trial Generation)
try {
    Write-Host "2. Testing POST /auth/device (First time connection)..." -NoNewline
    $body = @{
        device_id = $DeviceId
        computer_name = $CompName
        cpu = "Intel Core i9-14900K"
        gpu = "NVIDIA RTX 4090"
        os = "Windows 11 Pro"
        app_version = "1.0.0"
    } | ConvertTo-Json
    $res = Invoke-RestMethod -Uri "$BaseUrl/auth/device" -Method POST -Body $body -ContentType "application/json"
    if ($res.success -eq $true) {
        Write-Host " [SUCCESS]" -ForegroundColor Green
        Write-Host "   Key Generated: $($res.license.license_key)" -ForegroundColor Gray
        Write-Host "   Status: $($res.license.status)" -ForegroundColor Gray
        Write-Host "   Expiry: $($res.license.expire_at)" -ForegroundColor Gray
        $Global:LicenseKey = $res.license.license_key
    } else {
        Write-Host " [FAILED]" -ForegroundColor Red
    }
} catch {
    Write-Host " [ERROR: $_]" -ForegroundColor Red
}

# 3. POST License Check
try {
    Write-Host "3. Testing POST /license/check..." -NoNewline
    $body = @{
        device_id = $DeviceId
        license_key = $Global:LicenseKey
    } | ConvertTo-Json
    $res = Invoke-RestMethod -Uri "$BaseUrl/license/check" -Method POST -Body $body -ContentType "application/json"
    if ($res.success -eq $true) {
        Write-Host " [SUCCESS]" -ForegroundColor Green
        Write-Host "   Valid: $($res.license.is_valid)" -ForegroundColor Gray
    } else {
        Write-Host " [FAILED]" -ForegroundColor Red
    }
} catch {
    Write-Host " [ERROR: $_]" -ForegroundColor Red
}

# 4. POST Heartbeat
try {
    Write-Host "4. Testing POST /heartbeat..." -NoNewline
    $body = @{
        device_id = $DeviceId
    } | ConvertTo-Json
    $res = Invoke-RestMethod -Uri "$BaseUrl/heartbeat" -Method POST -Body $body -ContentType "application/json"
    if ($res.success -eq $true) {
        Write-Host " [SUCCESS]" -ForegroundColor Green
        Write-Host "   Interval: $($res.heartbeat_interval)s" -ForegroundColor Gray
    } else {
        Write-Host " [FAILED]" -ForegroundColor Red
    }
} catch {
    Write-Host " [ERROR: $_]" -ForegroundColor Red
}

# 5. POST Version Check
try {
    Write-Host "5. Testing POST /version..." -NoNewline
    $body = @{
        version = "1.0.0"
    } | ConvertTo-Json
    $res = Invoke-RestMethod -Uri "$BaseUrl/version" -Method POST -Body $body -ContentType "application/json"
    if ($res.success -eq $true) {
        Write-Host " [SUCCESS]" -ForegroundColor Green
        Write-Host "   Update Available: $($res.update_available)" -ForegroundColor Gray
        Write-Host "   Latest: $($res.latest_version)" -ForegroundColor Gray
    } else {
        Write-Host " [FAILED]" -ForegroundColor Red
    }
} catch {
    Write-Host " [ERROR: $_]" -ForegroundColor Red
}

# 6. POST Payment Create
try {
    Write-Host "6. Testing POST /payment/create..." -NoNewline
    $body = @{
        license_key = $Global:LicenseKey
        amount = 150000
    } | ConvertTo-Json
    $res = Invoke-RestMethod -Uri "$BaseUrl/payment/create" -Method POST -Body $body -ContentType "application/json"
    if ($res.success -eq $true) {
        Write-Host " [SUCCESS]" -ForegroundColor Green
        Write-Host "   Order Code: $($res.order_code)" -ForegroundColor Gray
        Write-Host "   Simulate checkout URL: $($res.checkout_url)" -ForegroundColor Gray
        $Global:OrderCode = $res.order_code
    } else {
        Write-Host " [FAILED]" -ForegroundColor Red
    }
} catch {
    Write-Host " [ERROR: $_]" -ForegroundColor Red
}

# 7. POST Payment Webhook (Simulation)
try {
    Write-Host "7. Testing POST /payment/webhook (Simulated Callback)..." -NoNewline
    $body = @{
        data = @{
            orderCode = $Global:OrderCode
            amount = 150000
            status = "success"
            reference = "TX-999-TEST"
        }
    } | ConvertTo-Json
    $res = Invoke-RestMethod -Uri "$BaseUrl/payment/webhook" -Method POST -Body $body -ContentType "application/json"
    if ($res.success -eq $true) {
        Write-Host " [SUCCESS]" -ForegroundColor Green
        Write-Host "   Message: $($res.message)" -ForegroundColor Gray
    } else {
        Write-Host " [FAILED]" -ForegroundColor Red
    }
} catch {
    Write-Host " [ERROR: $_]" -ForegroundColor Red
}

# 8. POST License Check again to confirm active & extended state
try {
    Write-Host "8. Checking license state after simulated payment..." -NoNewline
    $body = @{
        device_id = $DeviceId
        license_key = $Global:LicenseKey
    } | ConvertTo-Json
    $res = Invoke-RestMethod -Uri "$BaseUrl/license/check" -Method POST -Body $body -ContentType "application/json"
    if ($res.success -eq $true) {
        Write-Host " [SUCCESS]" -ForegroundColor Green
        Write-Host "   Status after payment: $($res.license.status)" -ForegroundColor Gray
        Write-Host "   New Expiry: $($res.license.expire_at)" -ForegroundColor Gray
    } else {
        Write-Host " [FAILED]" -ForegroundColor Red
    }
} catch {
    Write-Host " [ERROR: $_]" -ForegroundColor Red
}

# 9. POST Log
try {
    Write-Host "9. Testing POST /log (Crash event upload)..." -NoNewline
    $body = @{
        device_id = $DeviceId
        type = "crash"
        message = "Application crashed at thread 0x05f: Access Violation"
        details = @{
            module = "ffmpeg_encoder.dll"
            address = "0x7ffa12b8"
        }
    } | ConvertTo-Json
    $res = Invoke-RestMethod -Uri "$BaseUrl/log" -Method POST -Body $body -ContentType "application/json"
    if ($res.success -eq $true) {
        Write-Host " [SUCCESS]" -ForegroundColor Green
    } else {
        Write-Host " [FAILED]" -ForegroundColor Red
    }
} catch {
    Write-Host " [ERROR: $_]" -ForegroundColor Red
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host " ALL TESTS COMPLETE!" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
