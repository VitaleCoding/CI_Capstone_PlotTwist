<?php
$apiKey = "6c1bc1f8d69124f9e53632fe87d1c149";
$results = [];

function http_get_json($url) {
  $resp = @file_get_contents($url);
  if ($resp === false) { return null; }
  return json_decode($resp, true);
}

if (isset($_GET['action']) && $_GET['action'] === 'details') {
  header('Content-Type: application/json');
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid id']);
    exit;
  }

  $append = 'credits,videos,release_dates';
  $url = "https://api.themoviedb.org/3/movie/$id?api_key={$GLOBALS['apiKey']}&append_to_response=$append";
  $data = http_get_json($url);

  if (!$data) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to fetch data from TMDB']);
    exit;
  }

  echo json_encode($data);
  exit;
}

if (isset($_GET['query'])) {
  $query = urlencode($_GET['query']);
  $url = "https://api.themoviedb.org/3/search/movie?api_key=$apiKey&query=$query";
  $data = http_get_json($url);
  $results = $data['results'] ?? [];
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
      background:
        radial-gradient(1200px 800px at 80% -10%, rgba(124,58,237,.2), transparent),
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
    header h1 a { color: inherit; text-decoration: none; }
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
      margin-left: .4rem;
    }

    .movie {
      display: flex;
      align-items: flex-start;
      margin: 1rem auto;
      background: var(--card);
      padding: 1rem;
      border-radius: 14px;
      max-width: 680px;
      gap: 1rem;
      border: 1px solid rgba(255,255,255,.06);
      transition: transform .05s ease, box-shadow .15s ease;
      cursor: pointer;
    }
    .movie:hover {
      box-shadow: 0 10px 30px rgba(0,0,0,.35);
      transform: translateY(-1px);
    }
    .movie img {
      width: 80px;
      border-radius: 8px;
      flex: 0 0 auto;
    }
    .movie h4 { margin: 0 0 .35rem 0; font-size: 1.05rem; }
    .movie p { margin: 0; color: var(--muted); line-height: 1.35; }

    /* Modal (shared styles) */
    .modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.7);
      backdrop-filter: blur(8px);
      justify-content: center;
      align-items: center;
      z-index: 999;
      padding: 1rem;
    }
    .modal.is-open { display: flex; }
    .modal-content {
      background: linear-gradient(180deg, var(--card), var(--card-2));
      border: 1px solid rgba(255,255,255,.06);
      border-radius: 20px;
      width: 95%;
      max-width: 800px;
      padding: 1.25rem;
      position: relative;
      animation: fadeIn .25s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .close {
      position: absolute; top: 10px; right: 16px;
      font-size: 1.6rem; color: var(--muted); cursor: pointer;
    }

    /* Login form specifics (kept minimal, matches your previous styles) */
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
    .options { display: flex; justify-content: space-between; align-items:center; margin: .4rem 0 .8rem; }
    #msg { text-align: center; margin-top: .5rem; }

    /* Details modal content */
    .details-grid {
      display: grid;
      grid-template-columns: 140px 1fr;
      gap: 1rem;
    }
    .details-grid img { width: 140px; border-radius: 10px; }
    .meta { color: var(--muted); margin: .25rem 0; }
    .pill {
      display: inline-block;
      padding: .25rem .5rem;
      border-radius: 999px;
      border: 1px solid rgba(255,255,255,.12);
      margin-right: .35rem;
      font-size: .85rem;
      color: var(--muted);
    }
    @media (max-width: 640px) {
      .details-grid { grid-template-columns: 1fr; }
      .details-grid img { width: 100%; max-width: 240px; }
    }
  </style>
</head>
<body>
  <header>
    <h1><a href="homepage.php">🎬 PlotTwist</a></h1>
    <button id="openLogin">Sign In</button>
  </header>

  <main>
    <h2>Welcome to PlotTwist</h2>
    <p>Discover, track, and review your favorite films and shows.</p>

    <form method="get" role="search" aria-label="Search movies">
      <input type="text" name="query" placeholder="Search for a movie." value="<?= htmlspecialchars($_GET['query'] ?? '') ?>" required>
      <button type="submit">Search</button>
    </form>

    <?php if (!empty($results)): ?>
      <h3 style="margin-top:2rem;">Results:</h3>

      <?php foreach ($results as $movie): ?>
        <div class="movie" data-id="<?= (int)$movie['id'] ?>">
          <?php if (!empty($movie['poster_path'])): ?>
            <img src="https://image.tmdb.org/t/p/w200<?= htmlspecialchars($movie['poster_path']) ?>" alt="Poster">
          <?php endif; ?>
          <div>
            <h4>
              <?= htmlspecialchars($movie['title']) ?>
              <span class="meta">(<?= htmlspecialchars(substr($movie['release_date'] ?? 'N/A', 0, 4)) ?>)</span>
            </h4>
            <p><?= htmlspecialchars($movie['overview'] ?: "No description available.") ?></p>
          </div>
        </div>
      <?php endforeach; ?>

    <?php elseif (isset($_GET['query'])): ?>
      <p style="margin-top:2rem;">No results found.</p>
    <?php endif; ?>
  </main>

  <div class="modal" id="loginModal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="loginTitle">
      <span class="close" id="closeLogin" aria-label="Close">&times;</span>
      <h2 id="loginTitle" style="text-align:center;">Log in to PlotTwist</h2>

      <form id="loginForm" novalidate>
        <div class="row">
          <label for="username">Username or email</label>
          <div class="input">
            <input id="username" name="username" type="text" required autocomplete="username" placeholder="e.g. sam or sam@example.com" />
          </div>
        </div>

        <div class="row">
          <label for="password">Password</label>
          <div class="input">
            <input id="password" name="password" type="password" required minlength="8" autocomplete="current-password" placeholder="••••••••" />
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
        </button>

        <div id="msg" role="alert" aria-live="polite"></div>
      </form>
    </div>
  </div>

  <div class="modal" id="movieModal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="movieTitle">
      <span class="close" id="closeMovie" aria-label="Close">&times;</span>
      <div id="movieBody">
        <p style="margin:1rem 0; color:var(--muted);">Select a title to view details…</p>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const loginModal = document.getElementById('loginModal');
      const openLogin = document.getElementById('openLogin');
      const closeLogin = document.getElementById('closeLogin');

      function openLoginModal() { loginModal.classList.add('is-open'); loginModal.setAttribute('aria-hidden', 'false'); }
      function closeLoginModal() { loginModal.classList.remove('is-open'); loginModal.setAttribute('aria-hidden', 'true'); }

      openLogin?.addEventListener('click', openLoginModal);
      closeLogin?.addEventListener('click', closeLoginModal);
      loginModal?.addEventListener('click', (e) => { if (e.target === loginModal) closeLoginModal(); });

      const movieModal = document.getElementById('movieModal');
      const closeMovie = document.getElementById('closeMovie');
      const movieBody  = document.getElementById('movieBody');

      function openMovieModal() { movieModal.classList.add('is-open'); movieModal.setAttribute('aria-hidden', 'false'); }
      function closeMovieModal() { movieModal.classList.remove('is-open'); movieModal.setAttribute('aria-hidden', 'true'); }

      closeMovie.addEventListener('click', closeMovieModal);
      movieModal.addEventListener('click', (e) => { if (e.target === movieModal) closeMovieModal(); });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          closeLoginModal();
          closeMovieModal();
        }
      });

      document.querySelectorAll('.movie[data-id]').forEach(card => {
        card.addEventListener('click', async () => {
          const id = card.getAttribute('data-id');
          movieBody.innerHTML = '<p style="margin:1rem 0; color:var(--muted);">Loading…</p>';
          openMovieModal();

          try {
            const res = await fetch(`homepage.php?action=details&id=${encodeURIComponent(id)}`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Network error');
            const m = await res.json();

            const title   = (m.title || m.name || 'Untitled');
            const year    = (m.release_date || m.first_air_date || '').slice(0,4) || 'N/A';
            const poster  = m.poster_path ? ('https://image.tmdb.org/t/p/w342' + m.poster_path) : '';
            const runtime = (m.runtime ? (m.runtime + ' min') :
                            (Array.isArray(m.episode_run_time) && m.episode_run_time[0] ? (m.episode_run_time[0] + ' min') : ''));
            const rating  = (typeof m.vote_average === 'number' ? m.vote_average.toFixed(1) : '—');
            const genres  = (m.genres && m.genres.length ? m.genres.map(g => g.name) : []);
            const overview = m.overview || 'No description available.';
            const cast = (m.credits && m.credits.cast ? m.credits.cast.slice(0,6).map(c => c.name) : []);
            const countries = (m.production_countries || []).map(c => c.iso_3166_1);

            let trailer = '';
            if (m.videos && m.videos.results) {
              const yt = m.videos.results.find(v => v.site === 'YouTube' && (v.type === 'Trailer' || v.type === 'Teaser'));
              if (yt && yt.key) trailer = `https://www.youtube.com/watch?v=${yt.key}`;
            }

            let cert = '';
            if (m.release_dates && Array.isArray(m.release_dates.results)) {
              const us = m.release_dates.results.find(r => r.iso_3166_1 === 'US');
              if (us && us.release_dates && us.release_dates[0] && us.release_dates[0].certification) {
                cert = us.release_dates[0].certification;
              }
            }

            movieBody.innerHTML = `
              <div class="details-grid">
                ${poster ? `<img src="${poster}" alt="Poster for ${title}">` : ''}
                <div>
                  <h2 id="movieTitle" style="margin:.2rem 0 0 0;">${title} <span class="meta">(${year})</span></h2>
                  <p class="meta">
                    ${genres.map(g => `<span class="pill">${g}</span>`).join(' ')}
                    ${runtime ? `<span class="pill">${runtime}</span>` : ''}
                    ${cert ? `<span class="pill">${cert}</span>` : ''}
                    ${countries.length ? `<span class="pill">${countries.join(', ')}</span>` : ''}
                  </p>
                  <p style="margin: .75rem 0 1rem 0;">${overview}</p>
                  <p><strong>Top cast:</strong> ${cast.length ? cast.join(', ') : '—'}</p>
                  ${trailer ? `<a href="${trailer}" target="_blank" rel="noopener" style="display:inline-block; margin-top:.8rem; background:linear-gradient(135deg, var(--primary), var(--primary-2)); color:#0b0f14; padding:.6rem 1rem; border-radius:10px; font-weight:700; text-decoration:none;">Watch trailer</a>` : ''}
                  <div style="margin-top: .8rem; color: var(--muted);">⭐ Average rating: <strong style="color:var(--text)">${rating}</strong></div>
                </div>
              </div>
            `;
          } catch (err) {
            movieBody.innerHTML = `<p style="color: var(--danger);">Sorry, we couldn’t load details for this title.</p>`;
          }
        });
      });
    })();
  </script>
</body>
</html>
