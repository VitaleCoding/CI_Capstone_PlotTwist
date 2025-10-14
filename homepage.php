<?php
// --- PHP section (at the top of the file) ---
$apiKey = "6c1bc1f8d69124f9e53632fe87d1c149";
$results = [];

if (isset($_GET['query'])) {
    $query = urlencode($_GET['query']);
    $url = "https://api.themoviedb.org/3/search/movie?api_key=$apiKey&query=$query";

    $response = file_get_contents($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        $results = $data['results'] ?? [];
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
      --bg: #0b0f14;
      --card: #0f1720;
      --card-2: #111927;
      --text: #e6edf3;
      --muted: #a7b0ba;
      --primary: #ff5a1f;
      --primary-2: #ffd166;
      --danger: #ef4444;
      --success: #22c55e;
      --ring: rgba(124, 58, 237, 0.45);
    }

    * { box-sizing: border-box; }
    html, body {
      height: 100%; margin: 0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial, sans-serif;
      color: var(--text);
      background: radial-gradient(1200px 800px at 80% -10%, rgba(124,58,237,.2), transparent),
                  radial-gradient(800px 600px at -10% 80%, rgba(23, 162, 184, .18), transparent),
                  linear-gradient(180deg, #0a0f14 0%, #0b0f14 40%, #06090d 100%);
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.2rem 2rem;
      background: rgba(17,25,39,.5);
      border-bottom: 1px solid rgba(255,255,255,.08);
      backdrop-filter: blur(8px);
    }
    header h1 { font-size: 1.4rem; margin: 0; }
    header button {
      background: linear-gradient(135deg, var(--primary), var(--primary-2));
      color: #0b0f14;
      border: none;
      padding: .7rem 1.4rem;
      font-weight: 700;
      border-radius: 12px;
      cursor: pointer;
      transition: transform .05s ease, filter .15s ease;
    }
    header button:active { transform: translateY(1px); }

    main { text-align: center; padding: 4rem 1rem; }
    main h2 { font-size: 2rem; margin-bottom: .75rem; }
    main p { color: var(--muted); font-size: 1.1rem; max-width: 600px; margin: 0 auto; }

    form { margin-top: 2rem; }
    form input[type="text"] {
      padding: .7rem 1rem;
      border-radius: 10px;
      border: 1px solid rgba(255,255,255,.15);
      background: rgba(17,25,39,.6);
      color: var(--text);
      width: 250px;
    }
    form button {
      background: linear-gradient(135deg, var(--primary), var(--primary-2));
      color: #0b0f14;
      border: none;
      padding: .7rem 1rem;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
    }

    .movie {
      display: flex;
      align-items: flex-start;
      margin: 1rem auto;
      background: var(--card);
      padding: 1rem;
      border-radius: 14px;
      max-width: 600px;
      gap: 1rem;
    }
    .movie img {
      width: 80px;
      border-radius: 8px;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.7);
      backdrop-filter: blur(8px);
      justify-content: center;
      align-items: center;
      z-index: 999;
    }
    .modal-content {
      background: linear-gradient(180deg, var(--card), var(--card-2));
      border: 1px solid rgba(255,255,255,.06);
      border-radius: 20px;
      width: 90%;
      max-width: 440px;
      padding: 1.5rem;
      animation: fadeIn .3s ease;
      position: relative;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .close {
      position: absolute; top: 10px; right: 20px;
      font-size: 1.6rem; color: var(--muted); cursor: pointer;
    }
    .row { display: grid; gap: .4rem; margin-bottom: .8rem; }
    label { font-weight: 600; font-size: .95rem; }

    .input input {
      width: 100%; padding: .9rem 1rem; border-radius: 12px;
      border: 1px solid rgba(255,255,255,.08);
      background: rgba(17,25,39,.6); color: var(--text);
    }
    .btn {
      width: 100%; padding: .85rem 1rem; border-radius: 14px;
      border: 1px solid rgba(255,255,255,.08);
      background: linear-gradient(135deg, var(--primary), var(--primary-2));
      color: #0b0f14; font-weight: 700;
      cursor: pointer;
    }
    #msg { text-align: center; margin-top: .5rem; }
  </style>
</head>
<body>
  <header>
    <h1>🎬 PlotTwist</h1>
    <button id="openLogin">Sign In</button>
  </header>

  <main>
    <h2>Welcome to PlotTwist</h2>
    <p>Discover, track, and review your favorite films and shows.</p>

    <!-- Movie Search Form -->
    <form method="get">
      <input type="text" name="query" placeholder="Search for a movie..." value="<?= htmlspecialchars($_GET['query'] ?? '') ?>" required>
      <button type="submit">Search</button>
    </form>

    <?php if (!empty($results)): ?>
      <h3>Results:</h3>
      <?php foreach ($results as $movie): ?>
        <div class="movie">
          <?php if ($movie['poster_path']): ?>
            <img src="https://image.tmdb.org/t/p/w200<?= $movie['poster_path'] ?>" alt="Poster">
          <?php endif; ?>
          <div>
            <h4><?= htmlspecialchars($movie['title']) ?> (<?= substr($movie['release_date'] ?? 'N/A', 0, 4) ?>)</h4>
            <p><?= htmlspecialchars($movie['overview'] ?: "No description available.") ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php elseif (isset($_GET['query'])): ?>
      <p>No results found.</p>
    <?php endif; ?>
  </main>

  <!-- Modal Login -->
  <div class="modal" id="loginModal">
    <div class="modal-content">
      <span class="close" id="closeLogin">&times;</span>
      <h2 style="text-align:center;">Log in to PlotTwist</h2>

<section class="card" role="region" aria-labelledby="form-title">
      <form id="loginForm" novalidate>
        <h2 id="form-title" class="sr-only" style="position:absolute;left:-9999px;">Sign in form</h2>

        <div class="row">
          <label for="username">Username or email</label>
          <div class="input">
            <input id="username" name="username" type="text" required autocomplete="username" placeholder="e.g. samvitale or sam@example.com" />
          </div>
        </div>

        <div class="row">
          <label for="password">Password</label>
          <div class="input">
            <input id="password" name="password" type="password" required minlength="8" autocomplete="current-password" placeholder="••••••••" />
            <button type="button" class="toggle-pw" id="togglePw" aria-pressed="false" aria-label="Show password">Show</button>
          </div>
        </div>

        <div class="options">
          <label style="display:flex; gap:.55rem; align-items:center; font-weight:500;">
            <input type="checkbox" id="remember" name="remember" />
            Keep me signed in on this device
          </label>
          <a href="#" id="forgot">Forgot password?</a>
        </div>

        <button id="submitBtn" class="btn" type="submit">
          <span class="btn-label">Sign in</span>
          <span class="spinner" aria-hidden="true" style="display:none; width:16px; height:16px; border-radius:50%; border:2px solid rgba(0,0,0,.25); border-top-color:#0b0f14; animation: spin .7s linear infinite"></span>
        </button>

        <div id="msg" role="alert" aria-live="polite"></div>

        

        <p class="helper">New here? <a href="register.html">Create an account</a></p>

  <script>
    const modal = document.getElementById('loginModal');
    const openBtn = document.getElementById('openLogin');
    const closeBtn = document.getElementById('closeLogin');
    const form = document.getElementById('loginForm');
    const msg = document.getElementById('msg');

    openBtn.onclick = () => modal.style.display = 'flex';
    closeBtn.onclick = () => modal.style.display = 'none';
    window.onclick = e => { if (e.target === modal) modal.style.display = 'none'; };

    form.addEventListener('submit', e => {
      e.preventDefault();
      msg.textContent = 'Signing in...';
      msg.style.color = '#22c55e';
      setTimeout(() => {
        msg.textContent = 'Welcome back!';
        modal.style.display = 'none';
      }, 1000);
    });
  </script>
</body>
</html>
