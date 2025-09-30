<?php

$apiKey = "6c1bc1f8d69124f9e53632fe87d1c149";
$results = [];

if (isset($_GET['query'])) {
    $query = urlencode($_GET['query']);
    $url = "https://api.themoviedb.org/3/search/movie?api_key=$apiKey&query=$query";

    // Fetch data from TMDB
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
    <meta charset="UTF-8">
    <title>Movie Search</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px; }
        form { margin-bottom: 20px; }
        .movie { display: flex; align-items: center; margin-bottom: 15px; background: white; padding: 10px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        img { width: 80px; height: auto; margin-right: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Welcome to PlotTwist!</h1>
    <form method="get">
        <input type="text" name="query" placeholder="Enter movie name..." value="<?= htmlspecialchars($_GET['query'] ?? '') ?>" required>
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($results)): ?>
        <h2>Results:</h2>
        <?php foreach ($results as $movie): ?>
            <div class="movie">
                <?php if ($movie['poster_path']): ?>
                    <img src="https://image.tmdb.org/t/p/w200<?= $movie['poster_path'] ?>" alt="Poster">
                <?php endif; ?>
                <div>
                    <h3><?= htmlspecialchars($movie['title']) ?> (<?= substr($movie['release_date'] ?? 'N/A', 0, 4) ?>)</h3>
                    <p><?= htmlspecialchars($movie['overview'] ?: "No description available.") ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php elseif (isset($_GET['query'])): ?>
        <p>No results found.</p>
    <?php endif; ?>
</body>
</html>
