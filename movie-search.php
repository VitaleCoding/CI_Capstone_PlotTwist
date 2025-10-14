<?php
$apiKey = "6c1bc1f8d69124f9e53632fe87d1c149";
$results = [];

if (isset($_GET['query'])) {
    $query = urlencode($_GET['query']);
    $url = "https://api.themoviedb.org/3/search/movie?api_key=$apiKey&query=$query&include_adult=false&language=en-US";

    $response = @file_get_contents($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        $results = $data['results'] ?? [];
    }
}

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movie Search • PlotTwist</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        :root {
            --bg: #f7f7f7;
            --card: #fff;
            --text: #222;
            --muted: #666;
            --border: #e6e6e6;
            --accent: #111;
        }
        body { font-family: Arial, sans-serif; background: var(--bg); padding: 20px; color: var(--text); }
        header { display:flex; align-items:center; gap:12px; margin-bottom: 16px; }
        header a { text-decoration:none; color:#1e90ff; }
        h1 { margin:.2em 0 .4em; }

        form { margin-bottom: 18px; display:flex; gap:8px; flex-wrap:wrap; }
        input[type="text"] { flex:1 1 280px; padding:10px; font-size:16px; border-radius:8px; border:1px solid #ccc; }
        button[type="submit"] { padding:10px 16px; font-size:16px; border-radius:8px; border:0; background:var(--accent); color:#fff; cursor:pointer; }

        .movie { display:flex; align-items:flex-start; gap:15px; margin-bottom:15px; background:var(--card); padding:12px; border-radius:10px; box-shadow:0 2px 5px rgba(0,0,0,.07); border:1px solid var(--border); }
        .movie img { width:80px; height:auto; border-radius:6px; object-fit:cover; }
        .meta h3 { margin:0 0 6px; }
        .meta p { margin:0; color:var(--muted); line-height:1.4; }
        .actions { margin-top:8px; }
        .btn { display:inline-block; padding:8px 12px; font-size:14px; border-radius:8px; border:1px solid #ccc; background:#fff; cursor:pointer; }
        .btn:hover { background:#f0f0f0; }

        /* Modal */
        .modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:1000; }
        .modal.open { display:block; }
        .modal-dialog {
            background:#fff; color:#111; width:95%; max-width:800px;
            margin:4% auto; border-radius:12px; overflow:hidden; box-shadow:0 15px 40px rgba(0,0,0,.25); position:relative;
        }
        .modal-header { padding:14px 18px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center; }
        .modal-title { font-size:20px; margin:0; }
        .modal-close { background:transparent; border:0; font-size:28px; line-height:1; cursor:pointer; }
        .modal-body { display:grid; gap:16px; padding:16px; grid-template-columns: 120px 1fr; }
        .modal-poster { width:120px; border-radius:8px; object-fit:cover; }
        .modal-section h4 { margin:0 0 6px; font-size:16px; }
        .modal-section p { margin:0 0 10px; color:#333; }
        .chips { display:flex; flex-wrap:wrap; gap:6px; }
        .chip { background:#f1f1f1; border-radius:999px; padding:4px 10px; font-size:12px; }
        .video-link { display:inline-block; margin-top:6px; color:#1e90ff; text-decoration:none; }
        @media (max-width: 640px) {
            .modal-body { grid-template-columns: 1fr; }
            .modal-poster { width:160px; }
        }
    </style>
</head>
<body>
    <header>
        <a href="Homepage.html">&larr; Back to Home</a>
        <h1>Search for your movie here!</h1>
    </header>

    <form method="get" action="movie-search.php">
        <input type="text" name="query" placeholder="Enter movie name..." value="<?= h($_GET['query'] ?? '') ?>" required>
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($results)): ?>
        <h2>Results:</h2>
        <?php foreach ($results as $movie): 
            $id = $movie['id'] ?? null;
            $title = $movie['title'] ?? 'Untitled';
            $releaseYear = isset($movie['release_date']) && $movie['release_date'] !== '' ? substr($movie['release_date'], 0, 4) : 'N/A';
            $overview = $movie['overview'] ?? '';
            $poster = isset($movie['poster_path']) && $movie['poster_path'] ? "https://image.tmdb.org/t/p/w200" . $movie['poster_path'] : null;
        ?>
            <div class="movie" <?= $id ? 'data-movie-id="'.h($id).'"' : '' ?>>
                <?php if ($poster): ?>
                    <img src="<?= h($poster) ?>" alt="<?= h($title) ?> poster">
                <?php else: ?>
                    <img src="data:image/svg+xml;charset=UTF-8,<?= rawurlencode('<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;80&quot; height=&quot;120&quot;><rect width=&quot;100%&quot; height=&quot;100%&quot; fill=&quot;#eaeaea&quot;/><text x=&quot;50%&quot; y=&quot;50%&quot; dominant-baseline=&quot;middle&quot; text-anchor=&quot;middle&quot; fill=&quot;#777&quot; font-size=&quot;12&quot; font-family=&quot;Arial&quot;>No Poster</text></svg>') ?>" alt="No poster available" />
                <?php endif; ?>
                <div class="meta">
                    <h3><?= h($title) ?> (<?= h($releaseYear) ?>)</h3>
                    <p><?= $overview !== '' ? h($overview) : 'No description available.' ?></p>
                    <?php if ($id): ?>
                        <div class="actions">
                            <button class="btn js-details" data-movie-id="<?= h($id) ?>">More Info</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php elseif (isset($_GET['query'])): ?>
        <p>No results found.</p>
    <?php endif; ?>

    <!-- Modal -->
    <div id="movieModal" class="modal" aria-hidden="true">
        <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <div class="modal-header">
                <h3 id="modalTitle" class="modal-title">Movie Details</h3>
                <button class="modal-close" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <img id="modalPoster" class="modal-poster" src="" alt="Poster" />
                <div class="modal-content-area">
                    <div class="modal-section">
                        <h4>Overview</h4>
                        <p id="modalOverview">Loading…</p>
                    </div>
                    <div class="modal-section">
                        <h4>Info</h4>
                        <p id="modalFacts"></p>
                        <div id="modalGenres" class="chips"></div>
                    </div>
                    <div class="modal-section">
                        <h4>Top Cast</h4>
                        <p id="modalCast">Loading…</p>
                    </div>
                    <div class="modal-section">
                        <h4>Trailer</h4>
                        <a id="modalTrailer" target="_blank" rel="noopener" class="video-link">Open YouTube trailer</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
      const TMDB_API_KEY = <?= json_encode($apiKey) ?>;
      const modal = document.getElementById('movieModal');
      const modalTitle = document.getElementById('modalTitle');
      const modalPoster = document.getElementById('modalPoster');
      const modalOverview = document.getElementById('modalOverview');
      const modalFacts = document.getElementById('modalFacts');
      const modalGenres = document.getElementById('modalGenres');
      const modalCast = document.getElementById('modalCast');
      const modalTrailer = document.getElementById('modalTrailer');
      const modalCloseBtn = document.querySelector('.modal-close');

      function openModal() {
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
      }
      function closeModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
      }
      modalCloseBtn.addEventListener('click', closeModal);
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
      });

      async function fetchMovieDetails(id) {
        const base = 'https://api.themoviedb.org/3';
        const url = `${base}/movie/${id}?api_key=${TMDB_API_KEY}&language=en-US&append_to_response=credits,videos`;
        const res = await fetch(url);
        if (!res.ok) throw new Error('Network error');
        return await res.json();
      }

      function formatMinutes(mins) {
        if (!mins || isNaN(mins)) return 'N/A';
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        return (h ? `${h}h ` : '') + (m ? `${m}m` : '');
      }

      function setGenres(genres) {
        modalGenres.innerHTML = '';
        if (!genres || !genres.length) {
          return;
        }
        genres.slice(0, 8).forEach(g => {
          const span = document.createElement('span');
          span.className = 'chip';
          span.textContent = g.name;
          modalGenres.appendChild(span);
        });
      }

      function setCast(credits) {
        if (!credits || !credits.cast) {
          modalCast.textContent = 'N/A';
          return;
        }
        const names = credits.cast.slice(0, 5).map(c => c.name).filter(Boolean);
        modalCast.textContent = names.length ? names.join(', ') : 'N/A';
      }

      function setTrailer(videos) {
        if (!videos || !videos.results || !videos.results.length) {
          modalTrailer.style.display = 'none';
          return;
        }
        const trailer = videos.results.find(v => v.site === 'YouTube' && v.type === 'Trailer') 
                     || videos.results.find(v => v.site === 'YouTube');
        if (trailer && trailer.key) {
          modalTrailer.href = `https://www.youtube.com/watch?v=${trailer.key}`;
          modalTrailer.style.display = 'inline-block';
        } else {
          modalTrailer.style.display = 'none';
        }
      }

      async function showDetails(id, fallbackTitle = 'Movie Details', fallbackPoster = null) {
        modalTitle.textContent = fallbackTitle || 'Movie Details';
        modalOverview.textContent = 'Loading…';
        modalFacts.textContent = '';
        modalGenres.innerHTML = '';
        modalCast.textContent = 'Loading…';
        modalTrailer.style.display = 'none';
        modalPoster.src = fallbackPoster || '';
        modalPoster.alt = fallbackTitle ? `${fallbackTitle} poster` : 'Poster';

        openModal();

        try {
          const data = await fetchMovieDetails(id);

          const title = data.title || data.original_title || fallbackTitle || 'Movie Details';
          const posterPath = data.poster_path ? `https://image.tmdb.org/t/p/w300${data.poster_path}` : fallbackPoster;
          modalTitle.textContent = title;
          if (posterPath) { modalPoster.src = posterPath; modalPoster.alt = `${title} poster`; }

          modalOverview.textContent = data.overview || 'No description available.';

          const year = (data.release_date || '').slice(0,4) || 'N/A';
          const runtime = formatMinutes(data.runtime);
          const rating = (typeof data.vote_average === 'number') ? `${data.vote_average.toFixed(1)}/10` : 'N/A';
          modalFacts.textContent = `Year: ${year} • Runtime: ${runtime} • Rating: ${rating}`;

          setGenres(data.genres);

          setCast(data.credits);

          setTrailer(data.videos);

        } catch (e) {
          modalOverview.textContent = 'Sorry, we could not load more details right now.';
          modalFacts.textContent = '';
          modalGenres.innerHTML = '';
          modalCast.textContent = 'N/A';
          modalTrailer.style.display = 'none';
        }
      }

      document.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-details');
        if (!btn) return;
        e.preventDefault();
        const id = btn.getAttribute('data-movie-id');
        if (!id) return;

        const card = btn.closest('.movie');
        const titleEl = card?.querySelector('.meta h3');
        const imgEl = card?.querySelector('img');
        const fallbackTitle = titleEl ? titleEl.textContent : 'Movie Details';
        const fallbackPoster = imgEl ? imgEl.src : null;

        showDetails(id, fallbackTitle, fallbackPoster);
      });
    </script>
</body>
</html>
