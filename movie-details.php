<?php
header('Content-Type: application/json');

$apiKey = "6c1bc1f8d69124f9e53632fe87d1c149";
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing or invalid id']);
  exit;
}

$append = 'credits,videos,release_dates';
$url = "https://api.themoviedb.org/3/movie/$id?api_key=$apiKey&append_to_response=$append";

$response = @file_get_contents($url);
if ($response === false) {
  http_response_code(502);
  echo json_encode(['error' => 'Failed to fetch data from TMDB']);
  exit;
}

echo $response;
