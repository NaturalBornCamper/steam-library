<!doctype html>
<?php

 /*
  * // Hein?
  * TODO data.packages
  *
  * // Priority
  * TODO Load app tabs via AJAX (call app.php?id=550 and output result), page is taking forever to load right now
  * TODO Cronjob download small/large pictures in case store page disappears from Steam. Find a way to only download new ones.. videos??
  * TODO Don't fetch on page load, have only local data fetched, then fix cronjob to make sure it fetches new data all the time
  *
  * // Less priority
  * TODO Smaller filters to add more, or make them or compact somehow (smaller title, put in in boxes to remove margin, etc)
  * TODO Add other filters (Try Soon, Try Someday)
  * TODO Filter text box at the top
  * TODO Have filters That can be "none", "yes", "no" to eliminate apps that are co-op for example
  * TODO preload all large images when clicking on app card?
  * TODO Put Bootstrap in there?
  * TODO screenshots diaporama auto (si pas d'interaction)
  * TODO previous / next app arrows (Also keyboard arrows)
  * TODO previous / next screenshot arrows
  * TODO have ".selected" class for came card that is currently selected (I think already done, add more visual guide like an arrow
  * TODO autoplay vid
  *
  * // Not so important
  * TODO half-life 2 video et tv pas alignés
  */
?>
<html>
<head>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"
            integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"
            integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ"
            crossorigin="anonymous"></script>

    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="favicon/apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="favicon/apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="favicon/apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="favicon/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon-precomposed" sizes="60x60" href="favicon/apple-touch-icon-60x60.png" />
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="favicon/apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="favicon/apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="favicon/apple-touch-icon-152x152.png" />
    <link rel="icon" type="image/png" href="favicon/favicon-196x196.png" sizes="196x196" />
    <link rel="icon" type="image/png" href="favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/png" href="favicon/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="favicon/favicon-16x16.png" sizes="16x16" />
    <link rel="icon" type="image/png" href="favicon/favicon-128.png" sizes="128x128" />
    <meta name="application-name" content="&nbsp;"/>
    <meta name="msapplication-TileColor" content="#FFFFFF" />
    <meta name="msapplication-TileImage" content="favicon/mstile-144x144.png" />
    <meta name="msapplication-square70x70logo" content="favicon/mstile-70x70.png" />
    <meta name="msapplication-square150x150logo" content="favicon/mstile-150x150.png" />
    <meta name="msapplication-wide310x150logo" content="favicon/mstile-310x150.png" />
    <meta name="msapplication-square310x310logo" content="favicon/mstile-310x310.png" />

    <title>Steam library</title>
</head>

<body>
<?php
$CACHE_MAX_AGE = strtotime('-60 minutes');
$CACHE_MAX_AGE = strtotime('-9999 minutes');

$localExceptions = [
    'Local 3 players' => ' local-2-players local-3-players',
    'Local 4 players' => ' local-2-players local-3-players local-4-players',
    'Local 6 players' => ' local-2-players local-3-players local-4-players local-6-players',
    'Local 8 players' => ' local-2-players local-3-players local-4-players local-6-players local-8-players',
    'Local 16 players' => ' local-2-players local-3-players local-4-players local-6-players local-8-players local-16-players',
];
$ignoredCustomTags = ['favorite', 'Online bordel'];

function trace($var)
{
    echo '<pre>' . print_r($var, true) . '</pre>';
}

function arrayToClass($genres)
{
    $class = '';
    foreach ($genres as $genre)
//			$class .= ' '.($genre['description']);
        $class .= ' ' . toAscii($genre['description']);
    return $class;
}

include_once('fetch.php');
?>
<div id="menu" class="">
    <div class="row">
        <div class="col col-md-5 discount">ORDER <span id="gameCount">XX</span> GAMES BY:</div>
        <div class="col col-md-4 pull-left">
            <select id="sorter"
                    onChange="sortGameCards( $(this).find(':selected').data('attribute'), $(this).find(':selected').data('direction') )">
                <option data-attribute="" data-direction="">------SELECT------</option>
                <option data-attribute="price" data-direction="asc">Price $ -> $$$</option>
                <option data-attribute="price" data-direction="desc">Price $$$ -> $</option>
                <option data-attribute="discount" data-direction="asc">Discount % -> %%%</option>
                <option data-attribute="discount" data-direction="desc">Discount %%% -> %</option>
                <option data-attribute="title" data-direction="asc">Name A -> Z</option>
                <option data-attribute="title" data-direction="desc">Name Z -> A</option>
                <option data-attribute="date" data-direction="asc">Date ASC</option>
                <option data-attribute="date" data-direction="desc">Date DESC</option>
                <option data-attribute="recommendations" data-direction="asc">Recommendations ASC</option>
                <option data-attribute="recommendations" data-direction="desc">Recommendations DESC</option>
                <option data-attribute="rating" data-direction="asc">Metacritic rating 1 -> 100</option>
                <option data-attribute="rating" data-direction="desc">Metacritic rating 100 -> 1</option>
                <option data-attribute="random">Randomly</option>
            </select>
        </div>
    </div>
    <?php include_once('filters.php') ?>
    <div id="game-cards">
        <?php
        $categories = array();
        $genres = array();
        ?>
        <?php foreach ($onlineCoop as $appId => $game): //if (!$game['name']) continue ?>
            <?php
            foreach ($game['categories'] as $category)
                $categories[$category['id']] = $category['description'];
            foreach ($game['genres'] as $genre)
                $genres[$genre['id']] = $genre['description'];
            ?>
            <div class="game-card card <?= $appTags[$appId] ?><?= arrayToClass($game['genres']) ?>"
                 style="background-image:url(<?= $game['header_image'] ?>);"
                 data-app_id="<?= $appId ?>"
                 data-title="<?= $game['name'] ?>"
                 data-price="<?= $game['is_free'] ? 0 : (isset($game['price_overview']['final']) ? ($game['price_overview']['final'] ? $game['price_overview']['final'] : 0) : 999999) ?>"
                 data-date="<?= strtotime(str_replace(',', ' ', $game['release_date']['date'])) ?>"
                 data-rating="<?= $game['metacritic']['score'] ?>"
                 data-discount="<?= $game['price_overview']['discount_percent'] ?>"
                 data-recommendations="<?= $game['recommendations']['total'] ?>">
                <div class="card-body">
                    <h5 class="card-title">
                        <?php include('price.php'); ?>
                    </h5>
                    <?php if ($game['fetchFailed']): ?>
                        <div class="warning" class="warning">&#9888;&#9888;&#9888;</div>
                    <?php endif ?>
                </div>
            </div>
        <?php endforeach ?>
        <?php
        //	trace($categories);
        //	trace($genres);
        ?>
    </div>
</div>
<div id="game-tabs">
    <?php foreach ($onlineCoop as $appId => $game): //if (!$game['name']) continue ?>
    <?php /*
        <div id="game<?= $appId ?>" class="game-info" rel="<?= $game['background'] ?>" style="display:none">
            <div class="row">
                <div class="main-info">
                    <h2><a target="<?= $appId ?>"
                           href="https://store.steampowered.com/app/<?= $appId ?>"><?= $game['name'] ?></a></h2>
                    <h7><?= $game['release_date']['date'] ?> <span>(<?= $game['dataFrom'] ?>)</span></h7>
                    <div class="price">
                        <?php include('price.php') ?>
                    </div>
                    <div class="tags">
                        <?php foreach ($game['genres'] as $genre): ?>
                            <span class="tag badge badge-light"><?= $genre['description'] ?></span>
                        <?php endforeach ?>
                        <?php foreach ($library[$appId]['tags'] as $tag): if (in_array($tag, $ignoredCustomTags)) continue; ?>
                            <span class="tag badge badge-dark"><?= $tag ?></span>
                        <?php endforeach ?>
                    </div>
                    <h3 style="margin-top:10px">
                        <?php if ($game['metacritic']): ?>
                            <span class="metascore"
                                  style="background-color: hsl(<?= floor(($game['metacritic']['score']) * 120 / 100); ?>, 80%, 50%);"><a
                                        href="<?= $game['metacritic']['url'] ?>" target="metacritic"
                                        style="text-decoration:none"><?= $game['metacritic']['score'] ?></a></span>
                        <?php endif ?>
                        <?php if ($game['controller_support']): ?>
                            <img src="ico_controller.png"/>
                        <?php endif ?>
                        <span style="font-size:50%"><?= number_format($game['recommendations']['total']) ?> recommendations</span>
                    </h3>
                    <?php if ($game['short_description']): ?>
                        <?php $game['short_description'] = strip_tags($game['short_description']) ?>
                        <div title="<?= str_replace('"', '', $game['short_description']) ?>">
                            <?= strlen($game['short_description']) > 200 ? substr($game['short_description'], 0, 200) . '...' : $game['short_description'] ?>
                        </div>
                    <?php endif ?>
                </div>
                <div class="description">
                    <?= $game['about_the_game'] ?>
                </div>
            </div>
            <div class="row">
                <div class="thumbnail-container">
                    <ul class="thumbnails">
                        <?php foreach ($game['screenshots'] as $screenshot): ?>
                            <li><img data-path_thumbnail="<?= $screenshot['path_thumbnail'] ?>"
                                     data-path_full="<?= $screenshot['path_full'] ?>"/></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
            <div class="videos">
                <?php foreach ($game['movies'] as $video): ?>
                    <span data-name="<?= $video['name'] ?>" data-thumbnail="<?= $video['thumbnail'] ?>"
                          data-video_480="<?= $video['webm']['480'] ?>"
                          data-video_max="<?= $video['webm']['max'] ?>"></span>
                <?php endforeach ?>
            </div>
        </div>
    */ ?>
    <?php endforeach ?>
    <div id="tv">
        <img data-toggle="modal" data-target="#screenshot-modal"/>
        <div id="carouselExampleIndicators" class="carousel slide" data-interval="false">
            <ol class="carousel-indicators"></ol>
            <div class="carousel-inner"></div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
</div>

<div class="modal fade" id="screenshot-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
            </div>
            <div class="modal-body">
                <img/>
            </div>
        </div>
    </div>
</div>

<script src="function.js"></script>
<script>
    var activeFilters = new Object();

    $(function () {
        $('#tv img').on('click', function (e) {
            $('#screenshot-modal img').attr('src', $(e.target).attr('rel'));
            $('#screenshot-modal').modal('handleUpdate')
//			$('#screenshot-modal').modal('show');
        });

        $('.filter').each(function (index, element) {
            activeFilters[$(element).val()] = $(element).prop('checked');
        });
        filterGames();

        let firstAppId = $('.game-card:first').data('app_id');
        if (typeof firstAppId == "undefined") alert("First App id is undefined")
        else showGame(firstAppId);

        $('.game-card').click(function () {
            let appId = $(this).data('app_id');
            if (typeof appId == "undefined") alert("App id is undefined")
            else showGame(appId);
        });

        $('#carouselExampleIndicators').on('slid.bs.carousel', function () {
            $('video').each(function () {
                $(this)[0].pause();
            });
        })

        $('.filter').on('click', function () {
            activeFilters[$(this).val()] = $(this).prop('checked');
            filterGames();
        });

//		$('body').on('click', '.carousel-item', function()
        $('body').on('click', 'video', function () {
            this.paused ? this.play() : this.pause();
        });

        $('.thumbnails img').click(function () {
            setScreenshot($(this).data('path_thumbnail'), $(this).data('path_full'));
        });

        $('#screenshot-modal').click(function () {
            $('#screenshot-modal').modal('hide')
        });
    });
</script>
</body>
</html>