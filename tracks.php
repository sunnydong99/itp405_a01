<?php 
if ( !isset($_GET['playlist']) || empty($_GET['playlist']) ||
     ($_GET['playlist'] < 1) || ($_GET['playlist'] > 18) ){
    header("Location: playlists.php");
    exit();
} else {
    require(__DIR__ . '/vendor/autoload.php');

    if (file_exists(__DIR__ . '/.env')) {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        // echo "loaded!";
    }

    $pdo = new PDO($_ENV['PDO_CONNECTION_STRING']);

    $playlist_id = $_GET['playlist'];

    $sql = "
        SELECT tracks.name, albums.title AS album, artists.name AS artist, unit_price AS price, genres.name AS genre, playlists.name AS playlist
        FROM playlist_track
        INNER JOIN tracks
        ON playlist_track.track_id = tracks.id
        INNER JOIN genres 
        ON tracks.genre_id = genres.id
        INNER JOIN albums
        ON tracks.album_id = albums.id
        INNER JOIN artists
        ON albums.artist_id = artists.id
        INNER JOIN playlists
        ON playlist_track.playlist_id = playlists.id
        WHERE playlist_id = $playlist_id
    ;";

    $statement = $pdo->prepare($sql);
    $statement->execute();
    $tracks = $statement->fetchAll(PDO::FETCH_OBJ);



    if (empty($tracks)) { // doing another query in the DB for the playlist name - since it isn't't passed through the url or in an empty array
        $sql_empty = "SELECT playlists.name FROM playlists WHERE playlists.id = $playlist_id; ";
        $statement = $pdo->prepare($sql_empty);
        $statement->execute();
        $playlist_name = $statement->fetchAll(PDO::FETCH_OBJ);
        $error = "No tracks found for " . $playlist_name[0]->name;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Tracks</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-3">
    <a href="playlists.php" role="button" class="btn btn-outline-primary mb-3">Return to Playlists</a>
    <?php if (isset($error) && !empty($error)): ?>
        <div class="alert alert-primary" role="alert">
            <?php echo $error; ?>
        </div>
    <?php else: ?>
        <h3>Tracks Found For <?php echo $tracks[0]->playlist; ?></h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Track Name</th>
                    <th>Album</th>
                    <th>Artist</th>
                    <th>Price</th>
                    <th>Genre</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tracks as $track): ?>
                    <tr>
                        <td>
                            <?php echo $track->name ?>
                        </td>
                        <td>
                            <?php echo $track->album ?>
                        </td>
                        <td>
                            <?php echo $track->artist ?>
                        </td>
                        <td>
                            <?php echo $track->price ?>
                        </td>
                        <td>
                            <?php echo $track->genre ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="playlists.php" role="button" class="btn btn-outline-primary mb-3">Return to Playlists</a>
    <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>