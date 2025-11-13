<?php
// profile.php — renders a profile using data stored in localStorage by the frontend
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PlotTwist — Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root {
  --bg: #0b0f14; --card: #0f1720; --card-2: #111927; --text: #e6edf3; --muted: #a7b0ba;
  --primary: #ff5a1f; --primary-2: #ffd166; --danger: #ef4444; --success: #22c55e; --ring: rgba(124, 58, 237, 0.45);
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

/* Header (logo only, clickable back to homepage) */
header {
  display:flex; align-items:center; gap:12px;
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

/* Favorites placeholder grid */
.fav-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 12px;
}
.fav-empty {
  color: var(--muted);
  padding: .8rem 1rem;
  border: 1px dashed rgba(255,255,255,.12);
  border-radius: 12px;
  text-align: center;
}

/* Buttons */
.btn {
  display:inline-block;
  padding:.7rem 1.1rem;
  font-size:.95rem;
  border-radius:12px;
  border:1px solid var(--border);
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  color:#0b0f14;
  font-weight:700;
  cursor:pointer;
}
</style>
</head>
<body>

<header>
  <h1 onclick="window.location='homepage.html'">🎬 PlotTwist</h1>
</header>

<main class="main">
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

  <section class="card">
    <h2>Favorites</h2>
    <div id="favorites" class="fav-grid">
      <div class="fav-empty">Your favorites library is empty. Add movies from the search page.</div>
    </div>
  </section>

  <section class="card">
    <h2>Actions</h2>
    <button class="btn" onclick="window.location='movie-search.php'">Go to Movie Search</button>
  </section>
</main>

<script>
// Load current user from localStorage (set by homepage.js after login)
const currentUser = JSON.parse(localStorage.getItem('currentUser') || 'null');

function setText(id, value) {
  const el = document.getElementById(id);
  if (el) el.textContent = value ?? '—';
}

(function renderProfile(){
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

  // Render favorites if you later store them as currentUser.favorites = [ {id, title, poster, ...}, ... ]
})();
</script>

</body>
</html>
