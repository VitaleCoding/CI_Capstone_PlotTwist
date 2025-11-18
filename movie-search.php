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
* { box-sizing: border-box; margin:0; padding:0; }
body {
  font-family: system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial, sans-serif;
  color: var(--text);
  background:
    radial-gradient(1200px 800px at 80% -10%, rgba(124,58,237,.2), transparent),
    radial-gradient(800px 600px at -10% 80%, rgba(23,162,184,.18), transparent),
    linear-gradient(180deg, #0a0f14 0%, #0b0f14 40%, #06090d 100%);
  min-height:100vh;
}

/* Header */
header {
  display:flex; align-items:center; gap:12px;
  background: rgba(17,25,39,.5);
  border-bottom:1px solid rgba(255,255,255,.08);
  backdrop-filter: blur(8px);
  padding:1rem 1.5rem;
}
header h1 {
  font-size:1.4rem;
  margin:0;
  cursor:pointer;
  color: var(--primary);
}

/* Search Bar */
form {
  display:flex; gap:10px; flex-wrap:wrap; justify-content:center;
  margin:2rem auto;
  max-width:700px;
}
input[type="text"] {
  flex:1 1 400px;
  padding:.9rem 1rem;
  border-radius:12px;
  border:1px solid var(--border);
  background: rgba(17,25,39,.6);
  color: var(--text);
  font-size:1rem;
}
button[type="submit"] {
  padding:.9rem 1.5rem;
  border-radius:12px;
  border:none;
  font-weight:700;
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  color:#0b0f14;
  cursor:pointer;
  transition: transform .05s ease, filter .15s ease;
}
button[type="submit"]:active { transform: translateY(1px); }

/* Sort Dropdown */
#sortSelect {
  padding:.5rem 1rem;
  border-radius:12px;
  border:1px solid rgba(255,255,255,.08);
  background: rgba(17,25,39,.6);
  color: var(--text);
  font-size:1rem;
  margin-bottom:1rem;
}

/* Grid Layout */
.movie-grid {
  display:grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap:20px;
  padding:1rem 2rem 3rem;
}
.movie {
  background: linear-gradient(180deg, var(--card), var(--card-2));
  border:1px solid var(--border);
  border-radius:14px;
  overflow:hidden;
  box-shadow:0 4px 12px rgba(0,0,0,.25);
  display:flex;
  flex-direction:column;
  transition: transform .25s ease, box-shadow .25s ease;
}
.movie:hover {
  transform: scale(1.04);
  box-shadow:0 10px 25px rgba(0,0,0,.4);
}
.movie img {
  width:100%;
  height:270px;
  object-fit:cover;
}
.meta {
  padding:.9rem;
  flex-grow:1;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
}
.meta h3 {
  margin:0 0 .5rem;
  font-size:1rem;
  color: var(--primary-2);
}
.meta p {
  margin:0;
  color: var(--muted);
  font-size:.85rem;
}

/* Buttons */
.btn {
  display:inline-block;
  padding:7px 14px;
  font-size:.85rem;
  border-radius:10px;
  border:1px solid var(--border);
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  color:#0b0f14;
  cursor:pointer;
  transition: background .2s ease;
}
.btn-add {
  background: #22c55e !important; /* green */
  color: #0b0f14 !important;
}

.btn-remove {
  background: #ef4444 !important; /* red */
  color: white !important;
}
.btn:hover {
  background: linear-gradient(135deg, var(--primary-2), var(--primary));
  color:#0b0f14;
}

/* Modal */
.modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.8); backdrop-filter:blur(8px); z-index:1000; }
.modal.open { display:block; }
.modal-dialog {
  background: linear-gradient(180deg, var(--card), var(--card-2));
  border:1px solid var(--border);
  color:var(--text);
  width:95%; max-width:800px;
  margin:4% auto;
  border-radius:16px;
  overflow:hidden;
  box-shadow:0 15px 40px rgba(0,0,0,.45);
  position:relative;
}
.modal-header { padding:14px 18px; border-bottom:1px solid rgba(255,255,255,.08); display:flex; justify-content:space-between; align-items:center; }
.modal-title { font-size:20px; margin:0; color:var(--primary-2); }
.modal-close { background:transparent; border:0; font-size:28px; line-height:1; cursor:pointer; color:var(--muted); }
.modal-body { display:grid; gap:16px; padding:16px; grid-template-columns: 120px 1fr; }
.modal-poster { width:120px; border-radius:8px; object-fit:cover; }
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
  <h1 onclick="window.location='homepage.html'">🎬 PlotTwist</h1>
</header>

<form method="get" action="movie-search.php">
  <input type="text" name="query" placeholder="Search for a movie..." value="<?= h($_GET['query'] ?? '') ?>" required>
  <button type="submit">Search</button>
</form>

<?php if (!empty($results)): ?>
  <div style="text-align:center;">
    <label for="sortSelect" style="margin-right:0.5rem; font-weight:600;">Sort by:</label>
    <select id="sortSelect">
      <option value="newest">Newest to Oldest</option>
      <option value="oldest">Oldest to Newest</option>
      <option value="rating_high">Rating: High to Low</option>
      <option value="rating_low">Rating: Low to High</option>
    </select>
  </div>

  <div class="movie-grid" id="movieGrid">
    <?php foreach ($results as $movie): 
      $id = $movie['id'] ?? null;
      $title = $movie['title'] ?? 'Untitled';
      $releaseYear = isset($movie['release_date']) && $movie['release_date'] !== '' ? substr($movie['release_date'], 0, 4) : 'N/A';
      $rating = isset($movie['vote_average']) ? number_format($movie['vote_average'],1) : 0;
      $poster = isset($movie['poster_path']) && $movie['poster_path'] ? "https://image.tmdb.org/t/p/w300" . $movie['poster_path'] : null;
    ?>
      <div class="movie" data-release="<?= h($releaseYear) ?>" data-rating="<?= h($rating) ?>" <?= $id ? 'data-movie-id="'.h($id).'"' : '' ?> >
        <?php if ($poster): ?>
          <img src="<?= h($poster) ?>" alt="<?= h($title) ?> poster">
        <?php else: ?>
          <img src="data:image/svg+xml;charset=UTF-8,<?= rawurlencode('<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;200&quot; height=&quot;270&quot;><rect width=&quot;100%&quot; height=&quot;100%&quot; fill=&quot;#1b2735&quot;/><text x=&quot;50%&quot; y=&quot;50%&quot; dominant-baseline=&quot;middle&quot; text-anchor=&quot;middle&quot; fill=&quot;#777&quot; font-size=&quot;14&quot; font-family=&quot;Arial&quot;>No Poster</text></svg>') ?>" alt="No poster available" />
        <?php endif; ?>
        <div class="meta">
          <h3><?= h($title) ?> (<?= h($releaseYear) ?>)</h3>
          <p>Rating: <?= h($rating) ?>/10</p>
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
		<div class="modal-section">
		  <h4>Library</h4>
          <button id="modalFavoriteBtn" class="btn">Add to Library</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="getJSONData.js"></script>
<script src="putJSONData.js"></script>

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
const modalFavoriteBtn = document.getElementById('modalFavoriteBtn');

// --- current user + favorites helpers ---
function getCurrentUser() {
  try {
    return JSON.parse(localStorage.getItem('currentUser') || 'null');
  } catch (e) {
    return null;
  }
}

function setCurrentUser(user) {
  localStorage.setItem('currentUser', JSON.stringify(user));
}

function isFavorite(movieId) {
  const user = getCurrentUser();
  if (!user || !Array.isArray(user.favorites)) return false;
  return user.favorites.some(f => String(f.id) === String(movieId));
}

function updateFavoriteButton(movieId) {
  if (!modalFavoriteBtn) return;

  if (isFavorite(movieId)) {
    modalFavoriteBtn.textContent = 'Remove from Library';
    modalFavoriteBtn.classList.remove('btn-add');
    modalFavoriteBtn.classList.add('btn-remove');
  } else {
    modalFavoriteBtn.textContent = 'Add to Library';
    modalFavoriteBtn.classList.remove('btn-remove');
    modalFavoriteBtn.classList.add('btn-add');
  }
}

async function syncFavorite(action, movieObj) {
  const user = getCurrentUser();
  if (!user) {
    alert('Please log in on the homepage to use your library.');
    return false;
  }

  // get all users from JSONBin
  let users = await getJSONData();
  if (!Array.isArray(users)) users = [];

  const idx = users.findIndex(u => u.email === user.email);
  if (idx === -1) {
    alert('User not found in database.');
    return false;
  }

  let favs = Array.isArray(users[idx].favorites) ? users[idx].favorites : [];

  if (action === 'add') {
    if (!favs.some(f => String(f.id) === String(movieObj.id))) {
      favs.push(movieObj);
    }
  } else if (action === 'remove') {
    favs = favs.filter(f => String(f.id) !== String(movieObj.id));
  }

  users[idx].favorites = favs;

  const ok = await putJSONData(users);
  if (ok) {
    // keep localStorage in sync
    user.favorites = favs;
    setCurrentUser(user);
  } else {
    alert('Failed to update library.');
  }

  return ok;
}


function openModal() { modal.classList.add('open'); modal.setAttribute('aria-hidden','false'); }
function closeModal() { modal.classList.remove('open'); modal.setAttribute('aria-hidden','true'); }

modalCloseBtn.addEventListener('click', closeModal);
modal.addEventListener('click', (e)=>{ if(e.target===modal) closeModal(); });
document.addEventListener('keydown',(e)=>{ if(e.key==='Escape') closeModal(); });

async function fetchMovieDetails(id){
  const res = await fetch(`https://api.themoviedb.org/3/movie/${id}?api_key=${TMDB_API_KEY}&language=en-US&append_to_response=credits,videos`);
  if(!res.ok) throw new Error('Network error');
  return await res.json();
}
function formatMinutes(mins){ if(!mins||isNaN(mins)) return 'N/A'; const h=Math.floor(mins/60); const m=mins%60; return (h?`${h}h `:'') + (m?`${m}m`:''); }
function setGenres(genres){ modalGenres.innerHTML=''; if(!genres||!genres.length)return; genres.slice(0,8).forEach(g=>{const span=document.createElement('span');span.className='chip';span.textContent=g.name;modalGenres.appendChild(span);}); }
function setCast(credits){ if(!credits||!credits.cast){modalCast.textContent='N/A'; return;} const names=credits.cast.slice(0,5).map(c=>c.name).filter(Boolean); modalCast.textContent=names.length?names.join(', '):'N/A'; }
function setTrailer(videos){ if(!videos||!videos.results||!videos.results.length){modalTrailer.style.display='none'; return;} const trailer=videos.results.find(v=>v.site==='YouTube'&&v.type==='Trailer')||videos.results.find(v=>v.site==='YouTube'); if(trailer&&trailer.key){ modalTrailer.href=`https://www.youtube.com/watch?v=${trailer.key}`; modalTrailer.style.display='inline-block'; } else { modalTrailer.style.display='none'; } }

async function showDetails(id, fallbackTitle='Movie Details', fallbackPoster=null){
  modalTitle.textContent = fallbackTitle;
  modalOverview.textContent = 'Loading…';
  modalFacts.textContent = '';
  modalGenres.innerHTML = '';
  modalCast.textContent = 'Loading…';
  modalTrailer.style.display = 'none';
  modalPoster.src = fallbackPoster || '';
  modalPoster.alt = fallbackTitle + ' poster';

  // Set initial button state based on current favorites
  updateFavoriteButton(id);

  openModal();
  try {
    const data = await fetchMovieDetails(id);
    const title = data.title || data.original_title || fallbackTitle;
    const posterPath = data.poster_path ? `https://image.tmdb.org/t/p/w300${data.poster_path}` : fallbackPoster;
    modalTitle.textContent = title;
    if (posterPath) {
      modalPoster.src = posterPath;
      modalPoster.alt = title + ' poster';
    }
    modalOverview.textContent = data.overview || 'No description available.';
    const year = (data.release_date || '').slice(0,4) || 'N/A';
    const runtime = formatMinutes(data.runtime);
    const ratingVal = (typeof data.vote_average === 'number') ? data.vote_average.toFixed(1) : null;
    const ratingLabel = ratingVal ? `${ratingVal}/10` : 'N/A';
    modalFacts.textContent = `Year: ${year} • Runtime: ${runtime} • Rating: ${ratingLabel}`;
    setGenres(data.genres);
    setCast(data.credits);
    setTrailer(data.videos);

    // Prepare movie object to store in favorites
    const movieObj = {
      id: id,
      title: title,
      year: year,
      poster: posterPath || null,
      rating: ratingVal ? Number(ratingVal) : null
    };

    if (modalFavoriteBtn) {
      // reset handler each time the modal opens
      modalFavoriteBtn.onclick = async () => {
        const inLib = isFavorite(id);
        const action = inLib ? 'remove' : 'add';
        const ok = await syncFavorite(action, movieObj);
        if (ok) {
          updateFavoriteButton(id);
        }
      };
    }
  } catch (e) {
    modalOverview.textContent = 'Sorry, we could not load more details right now.';
    modalFacts.textContent = '';
    modalGenres.innerHTML = '';
    modalCast.textContent = 'N/A';
    modalTrailer.style.display = 'none';
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

// Sorting
const sortSelect = document.getElementById('sortSelect');
const movieGrid = document.getElementById('movieGrid');

function sortMovies(criteria){
  const movies=Array.from(movieGrid.children);
  movies.sort((a,b)=>{
    const yearA=parseInt(a.getAttribute('data-release'))||0;
    const yearB=parseInt(b.getAttribute('data-release'))||0;
    const ratingA=parseFloat(a.getAttribute('data-rating'))||0;
    const ratingB=parseFloat(b.getAttribute('data-rating'))||0;
    switch(criteria){
      case 'newest': return yearB-yearA;
      case 'oldest': return yearA-yearB;
      case 'rating_high': return ratingB-ratingA;
      case 'rating_low': return ratingA-ratingB;
      default: return 0;
    }
  });
  movies.forEach(m=>movieGrid.appendChild(m));
}

sortSelect.addEventListener('change',(e)=>sortMovies(e.target.value));
sortMovies('newest');
</script>

</body>
</html>
