<?php
// profile.php — user profile + favorites + TMDB modal
// Use the same TMDB key as movie-search.php
$apiKey = "6c1bc1f8d69124f9e53632fe87d1c149";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PlotTwist — Profile</title>
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
  --danger: #ef4444;
  --success: #22c55e;
  --border: rgba(255,255,255,.08);
}
* { box-sizing: border-box; margin:0; padding:0; }
body {
  font-family: system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial, sans-serif;
  color: var(--text);
  background:
    radial-gradient(1200px 800px at 80% -10%, rgba(124,58,237,.2), transparent),
    radial-gradient(800px 600px at -10% 80%, rgba(23,162,184,.18), transparent),
    linear-gradient(180deg, #0a0f14 0%, #0b0f14 40%, #06090d 100%);
  min-height: 100vh;
}

/* Header */
header {
  display:flex;
  align-items:center;
  gap:12px;
  background: rgba(17,25,39,.5);
  border-bottom:1px solid var(--border);
  backdrop-filter: blur(8px);
  padding:1rem 1.5rem;
}
header h1 {
  font-size:1.4rem;
  margin:0;
  cursor:pointer;
  color: var(--primary);
}

/* Layout */
.main {
  max-width: 1000px;
  margin: 2rem auto;
  padding: 0 1rem 3rem;
  display: grid;
  gap: 1.2rem;
}

/* Cards */
.card {
  background: linear-gradient(180deg, var(--card), var(--card-2));
  border: 1px solid var(--border);
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0,0,0,.25);
  padding: 1.25rem;
}
.card h2 {
  margin-bottom: .6rem;
  font-size: 1.3rem;
}
.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
@media (max-width: 800px) {
  .grid-2 { grid-template-columns: 1fr; }
}

/* Profile rows */
.field {
  display: grid;
  grid-template-columns: 160px 1fr;
  gap: .6rem;
  padding: .55rem 0;
  border-bottom: 1px solid rgba(255,255,255,.06);
}
.field:last-child { border-bottom: 0; }
.field .label { color: var(--muted); }
.field .value { color: var(--text); font-weight: 600; }

/* Favorites grid */
.fav-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 12px;
}
.fav-empty {
  color: var(--muted);
  padding: .8rem 1rem;
  border: 1px dashed rgba(255,255,255,.12);
  border-radius: 12px;
  text-align: center;
}
.fav-card {
  background: linear-gradient(180deg, var(--card), var(--card-2));
  border:1px solid var(--border);
  border-radius:14px;
  overflow:hidden;
  box-shadow:0 4px 12px rgba(0,0,0,.25);
  display:flex;
  flex-direction:column;
}
.fav-card img {
  width:100%;
  height:220px;
  object-fit:cover;
}
.fav-meta {
  padding:.8rem;
  display:flex;
  flex-direction:column;
  gap:.3rem;
}
.fav-title {
  font-weight:700;
  font-size:.95rem;
  color: var(--primary-2);
}
.fav-meta-text {
  font-size:.85rem;
  color: var(--muted);
}

/* Buttons */
.btn {
  display:inline-block;
  padding:.7rem 1.1rem;
  font-size:.9rem;
  border-radius:12px;
  border:1px solid var(--border);
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  color:#0b0f14;
  font-weight:700;
  cursor:pointer;
}
.btn:hover { filter: brightness(1.05); }
.btn-small {
  padding:.4rem .7rem;
  font-size:.8rem;
}
.btn-add {
  background: var(--success);
  color:#0b0f14;
}
.btn-remove {
  background: var(--danger);
  color:#fff;
}

/* Modal (matching movie-search.php, minus custom lists) */
.modal {
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.8);
  backdrop-filter:blur(8px);
  z-index:1000;
}
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
.modal-header {
  padding:14px 18px;
  border-bottom:1px solid rgba(255,255,255,.08);
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.modal-title {
  font-size:20px;
  margin:0;
  color:var(--primary-2);
}
.modal-close {
  background:transparent;
  border:0;
  font-size:28px;
  line-height:1;
  cursor:pointer;
  color:var(--muted);
}
.modal-body {
  display:grid;
  gap:16px;
  padding:16px;
  grid-template-columns: 120px 1fr;
}
.modal-poster {
  width:120px;
  border-radius:8px;
  object-fit:cover;
}
.modal-section h4 {
  margin:0 0 6px;
  font-size:16px;
  color:var(--primary-2);
}
.modal-section p {
  margin:0 0 10px;
  color:var(--text);
}
.chips {
  display:flex;
  flex-wrap:wrap;
  gap:6px;
}
.chip {
  background:rgba(255,255,255,0.1);
  border-radius:999px;
  padding:4px 10px;
  font-size:12px;
  color:var(--primary-2);
}
.video-link {
  display:inline-block;
  margin-top:6px;
  color:var(--primary-2);
  text-decoration:none;
  font-weight:600;
}
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

<main class="main">
  <!-- PROFILE INFO -->
  <section class="card">
    <h2>Profile</h2>
    <div id="profileInfo" class="grid-2">
      <div class="col">
        <div class="field"><div class="label">First Name</div><div class="value" id="pfFirstName">—</div></div>
        <div class="field"><div class="label">Last Name</div><div class="value" id="pfLastName">—</div></div>
      </div>
      <div class="col">
        <div class="field"><div class="label">Email</div><div class="value" id="pfEmail">—</div></div>
        <div class="field"><div class="label">Account Status</div><div class="value" id="pfStatus">—</div></div>
      </div>
    </div>
  </section>

  <!-- FAVORITES -->
  <section class="card">
    <h2>Favorites</h2>
    <div id="favorites" class="fav-grid">
      <div class="fav-empty">Your favorites library is empty. Add movies from the search page.</div>
    </div>
  </section>

  <!-- ACTIONS -->
  <section class="card">
    <h2>Actions</h2>
    <button class="btn" onclick="window.location='movie-search.php'">Go to Movie Search</button>
  </section>
</main>

<!-- Modal (same idea as movie-search.php) -->
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
          <h4>Favorites</h4>
          <button id="modalFavoriteBtn" class="btn">Add to Favorites</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JSONBin helpers -->
<script src="getJSONData.js"></script>
<script src="putJSONData.js"></script>

<script>
// ===== User + profile helpers =====
let currentUser = null;

function loadCurrentUserFromLocal() {
  try {
    currentUser = JSON.parse(localStorage.getItem('currentUser') || 'null');
  } catch (e) {
    currentUser = null;
  }
}
function saveCurrentUserToLocal() {
  if (!currentUser) return;
  localStorage.setItem('currentUser', JSON.stringify(currentUser));
}
function setText(id, value) {
  const el = document.getElementById(id);
  if (el) el.textContent = value ?? '—';
}

function renderProfile(){
  if (!currentUser) {
    setText('pfFirstName', '—');
    setText('pfLastName',  '—');
    setText('pfEmail',     '—');
    setText('pfStatus',    'Not logged in');
    return;
  }
  setText('pfFirstName', currentUser.firstName || '—');
  setText('pfLastName',  currentUser.lastName  || '—');
  setText('pfEmail',     currentUser.email     || '—');
  setText('pfStatus',    'Active');
}

function renderFavorites() {
  const favContainer = document.getElementById('favorites');
  if (!favContainer) return;

  const favs = (currentUser && Array.isArray(currentUser.favorites)) ? currentUser.favorites : [];
  favContainer.innerHTML = '';

  if (!favs.length) {
    const empty = document.createElement('div');
    empty.className = 'fav-empty';
    empty.textContent = 'Your favorites library is empty. Add movies from the search page.';
    favContainer.appendChild(empty);
    return;
  }

  favs.forEach(f => {
    const card = document.createElement('div');
    card.className = 'fav-card';
    card.setAttribute('data-movie-id', f.id ?? '');
    card.setAttribute('data-release', f.year ?? '');
    card.setAttribute('data-rating', f.rating ?? '');

    if (f.poster) {
      const img = document.createElement('img');
      img.src = f.poster;
      img.alt = (f.title || 'Poster') + ' poster';
      card.appendChild(img);
    }

    const meta = document.createElement('div');
    meta.className = 'fav-meta';

    const title = document.createElement('div');
    title.className = 'fav-title';
    title.textContent = f.title || 'Untitled';

    const metaText = document.createElement('div');
    metaText.className = 'fav-meta-text';
    const ratingPart = f.rating ? ` • Rating: ${f.rating}/10` : '';
    metaText.textContent = `Year: ${f.year || 'N/A'}${ratingPart}`;

    const actions = document.createElement('div');
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn btn-small js-details';
    btn.textContent = 'More Info';
    btn.setAttribute('data-movie-id', f.id ?? '');
    actions.appendChild(btn);

    meta.appendChild(title);
    meta.appendChild(metaText);
    meta.appendChild(actions);
    card.appendChild(meta);

    favContainer.appendChild(card);
  });
}

// ===== JSONBin sync for favorites =====
function ensureUserFavorites(user) {
  if (!Array.isArray(user.favorites)) user.favorites = [];
}

async function syncUserToJsonBin(mutator) {
  if (!currentUser) {
    alert('You must be logged in to modify your library.');
    return false;
  }
  try {
    let users = await getJSONData();
    if (!Array.isArray(users)) users = [];

    const idx = users.findIndex(u => u.email === currentUser.email);
    if (idx === -1) {
      alert('User not found in database.');
      return false;
    }

    ensureUserFavorites(users[idx]);
    mutator(users[idx]);

    const ok = await putJSONData(users);
    if (!ok) {
      alert('Failed to save changes.');
      return false;
    }

    currentUser = users[idx];
    saveCurrentUserToLocal();
    return true;
  } catch (err) {
    console.error(err);
    alert('Error communicating with server: ' + err.message);
    return false;
  }
}

// ===== Modal + TMDB logic (similar to movie-search) =====
const TMDB_API_KEY = <?= json_encode($apiKey) ?>;

const modal         = document.getElementById('movieModal');
const modalTitle    = document.getElementById('modalTitle');
const modalPoster   = document.getElementById('modalPoster');
const modalOverview = document.getElementById('modalOverview');
const modalFacts    = document.getElementById('modalFacts');
const modalGenres   = document.getElementById('modalGenres');
const modalCast     = document.getElementById('modalCast');
const modalTrailer  = document.getElementById('modalTrailer');
const modalCloseBtn = document.querySelector('.modal-close');
const modalFavoriteBtn = document.getElementById('modalFavoriteBtn');

let currentModalMovieId = null;
let currentModalMovie   = null;

function openModal() {
  modal.classList.add('open');
  modal.setAttribute('aria-hidden','false');
}
function closeModal() {
  modal.classList.remove('open');
  modal.setAttribute('aria-hidden','true');
}
modalCloseBtn.addEventListener('click', closeModal);
modal.addEventListener('click', (e)=>{ if(e.target===modal) closeModal(); });
document.addEventListener('keydown',(e)=>{ if(e.key==='Escape') closeModal(); });

async function fetchMovieDetails(id){
  const res = await fetch(`https://api.themoviedb.org/3/movie/${id}?api_key=${TMDB_API_KEY}&language=en-US&append_to_response=credits,videos`);
  if(!res.ok) throw new Error('Network error');
  return await res.json();
}
function formatMinutes(mins){
  if(!mins||isNaN(mins)) return 'N/A';
  const h=Math.floor(mins/60);
  const m=mins%60;
  return (h?`${h}h `:'') + (m?`${m}m`:'');
}
function setGenres(genres){
  modalGenres.innerHTML='';
  if(!genres||!genres.length) return;
  genres.slice(0,8).forEach(g=>{
    const span=document.createElement('span');
    span.className='chip';
    span.textContent=g.name;
    modalGenres.appendChild(span);
  });
}
function setCast(credits){
  if(!credits||!credits.cast){
    modalCast.textContent='N/A';
    return;
  }
  const names=credits.cast.slice(0,5).map(c=>c.name).filter(Boolean);
  modalCast.textContent=names.length?names.join(', '):'N/A';
}
function setTrailer(videos){
  if(!videos||!videos.results||!videos.results.length){
    modalTrailer.style.display='none';
    return;
  }
  const trailer = videos.results.find(v=>v.site==='YouTube'&&v.type==='Trailer')
               || videos.results.find(v=>v.site==='YouTube');
  if(trailer&&trailer.key){
    modalTrailer.href=`https://www.youtube.com/watch?v=${trailer.key}`;
    modalTrailer.style.display='inline-block';
  } else {
    modalTrailer.style.display='none';
  }
}

function isFavorite(movieId) {
  if (!currentUser || !Array.isArray(currentUser.favorites)) return false;
  return currentUser.favorites.some(f => String(f.id) === String(movieId));
}
function updateFavoriteButton(movieId) {
  if (!modalFavoriteBtn) return;
  const fav = isFavorite(movieId);
  if (fav) {
    modalFavoriteBtn.textContent = 'Remove from Favorites';
    modalFavoriteBtn.classList.remove('btn-add');
    modalFavoriteBtn.classList.add('btn-remove');
  } else {
    modalFavoriteBtn.textContent = 'Add to Favorites';
    modalFavoriteBtn.classList.remove('btn-remove');
    modalFavoriteBtn.classList.add('btn-add');
  }
}

async function syncFavorite(action, movieObj) {
  const ok = await syncUserToJsonBin(user => {
    ensureUserFavorites(user);
    let favs = user.favorites;
    if (action === 'add') {
      if (!favs.some(f => String(f.id) === String(movieObj.id))) {
        favs.push(movieObj);
      }
    } else if (action === 'remove') {
      favs = favs.filter(f => String(f.id) !== String(movieObj.id));
    }
    user.favorites = favs;
  });

  if (ok) {
    renderFavorites(); // refresh grid after change
  }
  return ok;
}

async function showDetails(id, fallbackTitle='Movie Details', fallbackPoster=null){
  modalTitle.textContent=fallbackTitle;
  modalOverview.textContent='Loading…';
  modalFacts.textContent='';
  modalGenres.innerHTML='';
  modalCast.textContent='Loading…';
  modalTrailer.style.display='none';
  modalPoster.src=fallbackPoster||'';
  modalPoster.alt=fallbackTitle+' poster';

  currentModalMovieId = id;
  currentModalMovie   = null;

  updateFavoriteButton(id);
  openModal();

  try{
    const data=await fetchMovieDetails(id);
    const title=data.title||data.original_title||fallbackTitle;
    const posterPath=data.poster_path?`https://image.tmdb.org/t/p/w300${data.poster_path}`:fallbackPoster;
    modalTitle.textContent=title;
    if(posterPath){modalPoster.src=posterPath; modalPoster.alt=title+' poster';}
    modalOverview.textContent=data.overview||'No description available.';
    const year=(data.release_date||'').slice(0,4)||'N/A';
    const runtime=formatMinutes(data.runtime);
    const numericRating=(typeof data.vote_average==='number')?data.vote_average.toFixed(1):null;
    const ratingLabel=numericRating?`${numericRating}/10`:'N/A';
    modalFacts.textContent=`Year: ${year} • Runtime: ${runtime} • Rating: ${ratingLabel}`;
    setGenres(data.genres);
    setCast(data.credits);
    setTrailer(data.videos);

    currentModalMovie = {
      id: Number(id),
      title: title,
      year: year,
      poster: posterPath || fallbackPoster,
      rating: numericRating ? Number(numericRating) : null
    };

    updateFavoriteButton(id);

    if (modalFavoriteBtn) {
      modalFavoriteBtn.onclick = async () => {
        const inLib = isFavorite(id);
        const action = inLib ? 'remove' : 'add';
        const ok = await syncFavorite(action, currentModalMovie);
        if (ok) {
          updateFavoriteButton(id);
        }
      };
    }
  }catch(e){
    console.error(e);
    modalOverview.textContent='Sorry, we could not load more details right now.';
    modalFacts.textContent='';
    modalGenres.innerHTML='';
    modalCast.textContent='N/A';
    modalTrailer.style.display='none';
  }
}

// When user clicks "More Info" on a favorite card
document.addEventListener('click',(e)=>{
  const btn=e.target.closest('.js-details');
  if(!btn) return;
  e.preventDefault();
  const id=btn.getAttribute('data-movie-id');
  if(!id) return;
  const card=btn.closest('.fav-card');
  const titleEl=card?.querySelector('.fav-title');
  const imgEl=card?.querySelector('img');
  const fallbackTitle=titleEl?titleEl.textContent:'Movie Details';
  const fallbackPoster=imgEl?imgEl.src:null;
  showDetails(id,fallbackTitle,fallbackPoster);
});

// ===== Init =====
loadCurrentUserFromLocal();
renderProfile();
renderFavorites();
</script>

</body>
</html>
