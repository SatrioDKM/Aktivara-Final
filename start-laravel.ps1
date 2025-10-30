# ==========================================
# 🚀 Laravel Local Network Server Launcher
# ==========================================
$port = 8000
Write-Host "=========================================="
Write-Host "🚀 Starting Laravel Local Network Server"
Write-Host "=========================================="
Write-Host ""

# Dapatkan IP lokal
$ip = (Get-NetIPAddress -AddressFamily IPv4 |
       Where-Object { $_.IPAddress -like "192.*" -or $_.IPAddress -like "10.*" -or $_.IPAddress -like "172.*" } |
       Select-Object -First 1 -ExpandProperty IPAddress)

if (-not $ip) {
    Write-Host "❌ Tidak bisa menemukan IP lokal. Pastikan kamu terhubung ke Wi-Fi atau LAN." -ForegroundColor Red
    exit
}

# Buka firewall untuk port 8000
Write-Host "🧱 Membuka firewall untuk port $port ..."
netsh advfirewall firewall add rule name="Laravel Dev Server" dir=in action=allow protocol=TCP localport=$port | Out-Null

# Jalankan server Laravel
Write-Host "⚙️ Menjalankan Laravel server..."
Start-Process -NoNewWindow -FilePath "php" -ArgumentList "artisan serve --host=0.0.0.0 --port=$port"

# Tunggu sedikit supaya server sempat start
Start-Sleep -Seconds 2

# URL untuk akses dari device lain
$url = "http://$ip`:$port"

Write-Host ""
Write-Host "✅ Laravel server berjalan di: " -NoNewline
Write-Host "$url" -ForegroundColor Green

# Copy ke clipboard dan buka di browser
$url | Set-Clipboard
Start-Process $url

Write-Host ""
Write-Host "📋 URL sudah disalin ke clipboard!"
Write-Host "🌐 Temanmu bisa akses lewat: $url"
Write-Host ""
Write-Host "Tekan Ctrl + C untuk menghentikan server Laravel."
Write-Host ""

# Menjaga PowerShell tetap aktif biar bisa lihat log
php artisan serve --host=0.0.0.0 --port=$port
