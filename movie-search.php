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
<title>PlotTwist — Movie Search</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root {
  --bg: #0b0f14; 
  --card: #0f1720; 
  --card-2: #111927; 
  --text: #e6edf3; 
  --muted: #a7b0ba; 
  --primary: #ff5a1f; 
  --primary-2: #ffd166; 
  --border: rgba(255,255,255,.06);
}
* { box-sizing: border-box; }
body {
  font-family: system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial, sans-serif;
  color: var(--text);
  background:
    radial-gradient(1200px 800px at 80% -10%, rgba(124,58,237,.2), transparent),
    radial-gradient(800px 600px at -10% 80%, rgba(23,162,184,.18), transparent),
    linear-gradient(180deg, #0a0f14 0%, #0b0f14 40%, #06090d 100%);
  margin:0; padding:0;
}

/* Header like homepage */
header {
  display:flex; justify-content:space-between; align-items:center;
  padding:1.2rem 2rem;
  background: rgba(17,25,39,.5);
  border-bottom:1px solid rgba(255,255,255,.08);
  backdrop-filter: blur(8px);
}
header h1 { margin:0; }
header h1 a { color:inherit; text-decoration:none; font-size:1.4rem; font-weight:700; }

/* Search Bar */
form {
  display:flex; gap:10px; flex-wrap:wrap; justify-content:center;
  margin:2rem auto;
  max-width:700px;
}
input[type="text"] {
  flex:1 1 400px;
  padding:0.9rem 1rem;
  border-radius:12px;
  border:1px solid rgba(255,255,255,.08);
  background: rgba(17,25,39,.6);
  color: var(--text);
  font-size:1rem;
}
button[type="submit"] {
  padding:0.9rem 1.5rem;
  border-radius:12px;
  border:none;
  font-weight:700;
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  color:#0b0f14;
  cursor:pointer;
  transition: transform .05s ease, filter .15s ease;
}
button[type="submit"]:active { transform: translateY(1px); }

/* Movie Grid */
.movie-grid {
  display:grid;
  grid-template-columns: repeat(auto-fill, minmax(180px,1fr));
  gap:20px;
  padding:1rem 2rem 3rem;
}
.movie {
  background: linear-gradient(180deg, var(--card), var(--card-2));
  border:1px solid var(--border);
  border-radius:20px;
  overflow:hidden;
  display:flex;
  flex-direction:column;
  transition: transform .25s ease, box-shadow .25s ease;
}
.movie:hover { transform: scale(1.04); box-shadow:0 10px 25px rgba(0,0,0,.4); }
.movie img { width:100%; height:270px; object-fit:cover; border-bottom:1px solid var(--border); }
.meta { padding:0.9rem; flex-grow:1; display:flex; flex-direction:column; justify-content:space-between; }
.meta h3 { margin:0 0 0.5rem; font-size:1rem; color:var(--primary-2); }
.meta p { margin:0; color:var(--muted); font-size:0.85rem; line-height:1.4; height:50px; overflow:hidden; text-overflow:ellipsis; }
.actions { margin-top:10px; display:flex; justify-content:center; }
.btn {
  display:inline-block; padding:7px 14px; font-size:0.85rem;
  border-radius:12px; border:1px solid rgba(255,255,255,.08);
  background: rgba(255,255,255,.08); color: var(--text); cursor:pointer;
  transition: background .2s ease;
}
.btn:hover {
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  color:#0b0f14;
}

/* Modal styling kept same as before, slightly rounded */
.modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.8); backdrop-filter:blur(8px); z-index:1000; }
.modal.open { display:block; }
.modal-dialog {
  background: linear-gradient(180deg, var(--card), var(--card-2));
  border:1px solid var(--border);
  color:var(--text);
  width:95%; max-width:800px;
  margin:4% auto;
  border-radius:20px;
  overflow:hidden;
  box-shadow:0 15px 40px rgba(0,0,0,.45);
  position:relative;
}
.modal-header { padding:14px 18px; border-bottom:1px solid rgba(255,255,255,.08); display:flex; justify-content:space-between; align-items:center; }
.modal-title { font-size:20px; margin:0; color:var(--primary-2); }
.modal-close { background:transparent; border:0; font-size:28px; line-height:1; cursor:pointer; color:var(--muted); }
.modal-body { display:grid; gap:16px; padding:16px; grid-template-columns:120px 1fr; }
.modal-poster { width:120px; border-radius:12px; object-fit:cover; }
.modal-section h4 { margin:0 0 6px; font-size:16px; color:var(--primary-2); }
.modal-section p { margin:0 0 10px; color:var(--text); }
.chips { display:flex; flex-wrap:wrap; gap:6px; }
.chip { background:rgba(255,255,255,.1); border-radius:999px; padding:4px 10px; font-size:12px; color:var(--primary-2); }
.video-link { display:inline-block; margin-top:6px; color:var(--primary-2); text-decoration:none; font-weight:600; }
@media (max-width:640px) {
  .modal-body { grid-template-columns:1fr; }
  .modal-poster { width:160px; margin:auto; }
}
</style>
</head>
<body>

<header>
  <h1><a href="homepage.html">🎬 PlotTwist</a></h1>
</header>

<form method="get" action="movie-search.php">
  <input type="text" name="query" placeholder="Search for a movie..." value="<?= h($_GET['query'] ?? '') ?>" required>
  <button type="submit">Search</button>
</form>

<?php if (!empty($results)): ?>
  <div class="movie-grid">
    <?php foreach ($results as $movie): 
      $id = $movie['id'] ?? null;
      $title = $movie['title'] ?? 'Untitled';
      $releaseYear = isset($movie['release_date']) && $movie['release_date'] !== '' ? substr($movie['release_date'], 0, 4) : 'N/A';
      $overview = $movie['overview'] ?? '';
      $poster = isset($movie['poster_path']) && $movie['poster_path'] ? "https://image.tmdb.org/t/p/w300" . $movie['poster_path'] : null;
    ?>
      <div class="movie" <?= $id ? 'data-movie-id="'.h($id).'"' : '' ?> >
        <?php if ($poster): ?>
          <img src="<?= h($poster) ?>" alt="<?= h($title) ?> poster">
        <?php else: ?>
          <img src="data:image/svg+xml;charset=UTF-8,<?= rawurlencode('<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;200&quot; height=&quot;270&quot;><rect width=&quot;100%&quot; height=&quot;100%&quot; fill=&quot;#1b2735&quot;/><text x=&quot;50%&quot; y=&quot;50%&quot; dominant-baseline=&quot;middle&quot; text-anchor=&quot;middle&quot; fill=&quot;#777&quot; font-size=&quot;14&quot; font-family=&quot;Arial&quot;>No Poster</text></svg>') ?>" alt="No poster available" />
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
  </div>
<?php elseif (isset($_GET['query'])): ?>
  <p style="text-align:center; color:var(--muted);">No results found.</p>
<?php endif; ?>

<!-- Modal remains the same -->
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
        <div>
          <h4>Where to Watch</h4>
          <div id="modalServices"></div>
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
const modalServices = document.getElementById('modalServices');
const modalCloseBtn = document.querySelector('.modal-close');

function openModal() { modal.classList.add('open'); modal.setAttribute('aria-hidden','false'); }
function closeModal() { modal.classList.remove('open'); modal.setAttribute('aria-hidden','true'); }
modalCloseBtn.addEventListener('click', closeModal);
modal.addEventListener('click', (e) => { if(e.target === modal) closeModal(); });
document.addEventListener('keydown', (e) => { if(e.key === 'Escape') closeModal(); });

async function fetchMovieDetails(id) {
  const base='https://api.themoviedb.org/3';
  const url=`${base}/movie/${id}?api_key=${TMDB_API_KEY}&language=en-US&append_to_response=credits,videos,watch/providers`;
  const res = await fetch(url);
  if(!res.ok) throw new Error('Network error');
  return await res.json();
}
function formatMinutes(mins){if(!mins||isNaN(mins))return'N/A';const h=Math.floor(mins/60);const m=mins%60;return (h?`${h}h `:'')+(m?`${m}m`:'');}
function setGenres(genres){modalGenres.innerHTML='';if(!genres||!genres.length)return;genres.slice(0,8).forEach(g=>{const span=document.createElement('span');span.className='chip';span.textContent=g.name;modalGenres.appendChild(span);});}
function setCast(credits){if(!credits||!credits.cast){modalCast.textContent='N/A';return;}const names=credits.cast.slice(0,5).map(c=>c.name).filter(Boolean);modalCast.textContent=names.length?names.join(', '):'N/A';}
function setTrailer(videos){if(!videos||!videos.results||!videos.results.length){modalTrailer.style.display='none';return;}const trailer=videos.results.find(v=>v.site==='YouTube'&&v.type==='Trailer')||videos.results.find(v=>v.site==='YouTube');if(trailer&&trailer.key){modalTrailer.href=`https://www.youtube.com/watch?v=${trailer.key}`;modalTrailer.style.display='inline-block';}else{modalTrailer.style.display='none';}}
function setWatchProviders(data){modalServices.innerHTML = "";if (!data || !data.results || !data.results.US) {
        modalServices.textContent = "Not available on streaming.";
        return;
    }

    const us = data.results.US;

    const available = us.flatrate || us.rent || us.buy || [];

    if (available.length === 0) {
        modalServices.textContent = "Not available to stream.";
        return;
    }

    const watchLink = us.link;

    const a = document.createElement('a');
    a.className = "video-link";
    a.textContent = "TMDB Page";
    a.href = watchLink;
    a.target = "_blank";
    a.rel = "noopener";

    modalServices.appendChild(a);
}


async function showDetails(id,fallbackTitle='Movie Details',fallbackPoster=null){
  modalTitle.textContent=fallbackTitle||'Movie Details';
  modalOverview.textContent='Loading…';
  modalFacts.textContent='';
  modalGenres.innerHTML='';
  modalCast.textContent='Loading…';
  modalTrailer.style.display='none';
  modalPoster.src=fallbackPoster||'';
  modalPoster.alt=fallbackTitle?`${fallbackTitle} poster`:'Poster';
  openModal();
  try{
    const data = await fetchMovieDetails(id);
    const title = data.title || fallbackTitle;
    const posterPath = data.poster_path ? `https://image.tmdb.org/t/p/w300${data.poster_path}` : fallbackPoster;
    modalTitle.textContent = title;
    if (posterPath) modalPoster.src = posterPath;
    modalOverview.textContent = data.overview || 'No description available.';
    const year = (data.release_date || '').slice(0, 4) || 'N/A';
    const runtime = formatMinutes(data.runtime);
    const rating = data.vote_average ? `${data.vote_average.toFixed(1)}/10` : 'N/A';
    modalFacts.textContent = `Year: ${year} • Runtime: ${runtime} • Rating: ${rating}`;
    setGenres(data.genres);
    setCast(data.credits);
    setTrailer(data.videos);
    setWatchProviders(data["watch/providers"]); // <-- NEW
  }catch(e){
    modalOverview.textContent='Sorry, we could not load more details right now.';
    modalFacts.textContent='';
    modalGenres.innerHTML='';
    modalCast.textContent='N/A';
    modalTrailer.style.display='none';
  }
}
document.addEventListener('click',(e)=>{
  const btn=e.target.closest('.js-details');
  if(!btn) return;
  e.preventDefault();
  const id=btn.getAttribute('data-movie-id');
  if(!id) return;
  const card=btn.closest('.movie');
  const titleEl=card?.querySelector('.meta h3');
  const imgEl=card?.querySelector('img');
  const fallbackTitle=titleEl?titleEl.textContent:'Movie Details';
  const fallbackPoster=imgEl?imgEl.src:null;
  showDetails(id,fallbackTitle,fallbackPoster);
});
</script>

</body>
</html>
