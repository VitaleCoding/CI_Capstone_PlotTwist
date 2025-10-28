<?php
session_start();

$binId = "68db3f4bae596e708f009e5c";
$masterKey = "$2a$10$R1S9oi04F7AYLcy30BU37eHzClLDoyFBvYWdA3OZBYBm7IixjYbn.";

function http_get_json($url, $key) {
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "X-Master-Key: $key\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $resp = @file_get_contents($url, false, $context);
    if ($resp === false) return null;
    return json_decode($resp, true);
}

function http_put_json($url, $data, $key) {
    $opts = [
        "http" => [
            "method" => "PUT",
            "header" => "Content-Type: application/json\r\n" .
                        "X-Master-Key: $key\r\n",
            "content" => json_encode($data)
        ]
    ];
    $context = stream_context_create($opts);
    $resp = @file_get_contents($url, false, $context);
    return $resp !== false;
}

// Handle login/register requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $post = $_POST;

    $jsonData = http_get_json("https://api.jsonbin.io/v3/b/$binId/latest", $masterKey);
    if ($jsonData === null) {
        echo json_encode(['success'=>false, 'message'=>'Failed to fetch data from JSONBin']);
        exit;
    }

    $users = $jsonData['record'] ?? [];

    if ($post['action'] === 'login') {
        $input = trim($post['username']);
        $pass  = trim($post['password']);

        $user = null;
        foreach ($users as $u) {
            if (strcasecmp($u['username'], $input) === 0 || strcasecmp($u['email'], $input) === 0) {
                $user = $u;
                break;
            }
        }

        if ($user && $user['password'] === $pass) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['firstName'] = $user['firstName'];
            echo json_encode(['success'=>true, 'firstName'=>$user['firstName']]);
        } else {
            echo json_encode(['success'=>false, 'message'=>'Invalid username or password']);
        }
        exit;
    }

    if ($post['action'] === 'register') {
        $newUser = [
            'lastName' => trim($post['lastName']),
            'firstName' => trim($post['firstName']),
            'username' => trim($post['username']),
            'email' => trim($post['email']),
            'password' => trim($post['password']),
            'favorites' => [],
            'movies' => []
        ];

        foreach ($users as $u) {
            if (strcasecmp($u['username'], $newUser['username']) === 0 || strcasecmp($u['email'], $newUser['email']) === 0) {
                echo json_encode(['success'=>false, 'message'=>'Username or email already exists']);
                exit;
            }
        }

        $users[] = $newUser;

        if (http_put_json("https://api.jsonbin.io/v3/b/$binId", ['record'=>$users], $masterKey)) {
            $_SESSION['username'] = $newUser['username'];
            $_SESSION['firstName'] = $newUser['firstName'];
            echo json_encode(['success'=>true, 'firstName'=>$newUser['firstName']]);
        } else {
            echo json_encode(['success'=>false, 'message'=>'Failed to register user']);
        }
        exit;
    }

    if ($post['action'] === 'logout') {
        session_destroy();
        echo json_encode(['success'=>true]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>PlotTwist — Home</title>
<meta name="color-scheme" content="light dark" />
<style>
:root {
--bg: #0b0f14; --card: #0f1720; --card-2: #111927; --text: #e6edf3; --muted: #a7b0ba;
--primary: #ff5a1f; --primary-2: #ffd166; --danger: #ef4444; --success: #22c55e; --ring: rgba(124, 58, 237, 0.45);
}
* { box-sizing: border-box; }
html, body { height:100%; margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial, sans-serif; color: var(--text);
background: radial-gradient(1200px 800px at 80% -10%, rgba(124,58,237,.2), transparent),
radial-gradient(800px 600px at -10% 80%, rgba(23, 162, 184, .18), transparent),
linear-gradient(180deg, #0a0f14 0%, #0b0f14 40%, #06090d 100%); }
header { display:flex; justify-content:space-between; align-items:center; padding:1.2rem 2rem; background: rgba(17,25,39,.5); border-bottom:1px solid rgba(255,255,255,.08); backdrop-filter: blur(8px); }
header h1 { font-size:1.4rem; margin:0; }
header h1 a { color:inherit; text-decoration:none; }
header button { background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:#0b0f14; border:none; padding:.7rem 1.4rem; font-weight:700; border-radius:12px; cursor:pointer; transition: transform .05s ease, filter .15s ease; }
header button:active { transform: translateY(1px); }

main { text-align:center; padding:4rem 1rem; }
main h2 { font-size:2rem; margin-bottom:.75rem; }
main p { color: var(--muted); font-size:1.1rem; max-width:600px; margin:0 auto; }

.btn { padding:.85rem 1rem; border-radius:14px; border:1px solid rgba(255,255,255,.08); background: linear-gradient(135deg, var(--primary), var(--primary-2)); color:#0b0f14; font-weight:700; cursor:pointer; margin-top:2rem; }

.modal { display:none; position:fixed; inset:0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); justify-content:center; align-items:center; z-index:999; padding:1rem; }
.modal.is-open { display:flex; }
.modal-content { background: linear-gradient(180deg, var(--card), var(--card-2)); border:1px solid rgba(255,255,255,.06); border-radius:20px; width:95%; max-width:400px; padding:1.25rem; position:relative; }
.close { position:absolute; top:10px; right:16px; font-size:1.6rem; color:var(--muted); cursor:pointer; }

.row { display:grid; gap:.4rem; margin-bottom:.8rem; }
label { font-weight:600; font-size:.95rem; }
.input input { width:100%; padding:.9rem 1rem; border-radius:12px; border:1px solid rgba(255,255,255,.08); background: rgba(17,25,39,.6); color: var(--text); }

#msgLogin, #msgRegister { text-align:center; margin-top:.5rem; font-weight:700; }
#msgLogin { color: var(--success); }
#msgRegister { color: var(--success); }

</style>
</head>
<body>
<header>
<h1><a href="homepage.php">🎬 PlotTwist</a></h1>
<?php if(isset($_SESSION['username'])): ?>
    <span style="font-weight:600;">Signed in as <?= htmlspecialchars($_SESSION['firstName']) ?></span>
    <button id="logoutBtn">Logout</button>
<?php else: ?>
    <button id="openLogin">Sign In</button>
    <button id="openRegister">Register</button>
<?php endif; ?>
</header>

<main>
<h2>Welcome to PlotTwist</h2>
<p>Discover, track, and review your favorite films and shows.</p>

<button class="btn" onclick="window.location='movie-search.php'">Go to Movie Search</button>
</main>

<!-- Login Modal -->
<div class="modal" id="loginModal" aria-hidden="true">
<div class="modal-content">
<span class="close" id="closeLogin">&times;</span>
<h2 style="text-align:center;">Log in</h2>
<form id="loginForm">
<div class="row"><label>Username or Email</label><div class="input"><input type="text" name="username" required></div></div>
<div class="row"><label>Password</label><div class="input"><input type="password" name="password" required minlength="8"></div></div>
<button type="submit" class="btn">Login</button>
<div id="msgLogin" role="alert" aria-live="polite"></div>
</form>
</div>
</div>

<!-- Register Modal -->
<div class="modal" id="registerModal" aria-hidden="true">
<div class="modal-content">
<span class="close" id="closeRegister">&times;</span>
<h2 style="text-align:center;">Register</h2>
<form id="registerForm">
<div class="row"><label>First Name</label><div class="input"><input type="text" name="firstName" required></div></div>
<div class="row"><label>Last Name</label><div class="input"><input type="text" name="lastName" required></div></div>
<div class="row"><label>Username</label><div class="input"><input type="text" name="username" required></div></div>
<div class="row"><label>Email</label><div class="input"><input type="email" name="email" required></div></div>
<div class="row"><label>Password</label><div class="input"><input type="password" name="password" required minlength="8"></div></div>
<button type="submit" class="btn">Register</button>
<div id="msgRegister" role="alert" aria-live="polite"></div>
</form>
</div>
</div>

<script>
(function(){
const loginModal = document.getElementById('loginModal');
const registerModal = document.getElementById('registerModal');
const openLogin = document.getElementById('openLogin');
const openRegister = document.getElementById('openRegister');
const closeLogin = document.getElementById('closeLogin');
const closeRegister = document.getElementById('closeRegister');

openLogin?.addEventListener('click', ()=>{loginModal.classList.add('is-open'); loginModal.setAttribute('aria-hidden','false');});
openRegister?.addEventListener('click', ()=>{registerModal.classList.add('is-open'); registerModal.setAttribute('aria-hidden','false');});
closeLogin.addEventListener('click', ()=>{loginModal.classList.remove('is-open'); loginModal.setAttribute('aria-hidden','true');});
closeRegister.addEventListener('click', ()=>{registerModal.classList.remove('is-open'); registerModal.setAttribute('aria-hidden','true');});

document.addEventListener('keydown', e=>{
if(e.key==='Escape'){loginModal.classList.remove('is-open'); registerModal.classList.remove('is-open');}
});

// Login form
document.getElementById('loginForm').addEventListener('submit', async e=>{
e.preventDefault();
const fd = new FormData(e.target); fd.append('action','login');
const res = await fetch('homepage.php',{method:'POST',body:fd});
const data = await res.json();
const msg = document.getElementById('msgLogin');
if(data.success){ msg.style.color='var(--success)'; msg.textContent='Welcome '+data.firstName; setTimeout(()=>location.reload(),800); }
else { msg.style.color='var(--danger)'; msg.textContent=data.message||'Failed'; }
});

// Register form
document.getElementById('registerForm').addEventListener('submit', async e=>{
e.preventDefault();
const fd = new FormData(e.target); fd.append('action','register');
const res = await fetch('homepage.php',{method:'POST',body:fd});
const data = await res.json();
const msg = document.getElementById('msgRegister');
if(data.success){ msg.style.color='var(--success)'; msg.textContent='Registered! Welcome '+data.firstName; setTimeout(()=>location.reload(),800); }
else { msg.style.color='var(--danger)'; msg.textContent=data.message||'Failed'; }
});

// Logout
const logoutBtn = document.getElementById('logoutBtn');
if(logoutBtn) logoutBtn.addEventListener('click', async ()=>{
const fd = new FormData(); fd.append('action','logout');
const r = await fetch('homepage.php',{method:'POST',body:fd});
const j = await r.json(); if(j.success) location.reload();
});
})();
</script>
</body>
</html>
