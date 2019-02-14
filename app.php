<?php
    isset($_GET['app_id']) || exit('$_GET[\'app_id\'] not set');
    $app_id = $_GET['app_id'];

    /*
     * JQUERY DOWNLOADER
     * -php
     *  Display list of all games with <div class="app" data-app_id="xxxx">
     * -jquery
     *  Loop in each
     *
     * Should call one php script per app, this way, the same script can be called by cronjob
     *
     * PHP DOWNLOAD SCRIPT
     * 1-Request to steam for app_id
     * 2-cache data as usual
     * 3-For each media picture ("header_image" + thumbnail + full size + "background"), check if exists locally
     *   If not, call another sub-script to download file
     *     if successful or already up to date, replace image url with local in json to cache
     *
     */
?>
<div id="game<?= $app_id ?>" class="game-info" rel="<?= $game['background'] ?>" style="display:none">
    <div class="row">
        <div class="main-info">
            <h2><a target="<?= $app_id ?>"
                   href="https://store.steampowered.com/app/<?= $app_id ?>"><?= $game['name'] ?></a></h2>
            <h7><?= $game['release_date']['date'] ?> <span>(<?= $game['dataFrom'] ?>)</span></h7>
            <div class="price">
                <?php include('price.php') ?>
            </div>
            <div class="tags">
                <?php foreach ($game['genres'] as $genre): ?>
                    <span class="tag badge badge-light"><?= $genre['description'] ?></span>
                <?php endforeach ?>
                <?php foreach ($library[$app_id]['tags'] as $tag): if (in_array($tag, $ignoredCustomTags)) continue; ?>
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