<?php
require_once('vdf.php');
$DROPBOX_LIBRARY_FILE = 'https://www.dropbox.com/s/4fobjxlruw17mac/sharedconfig.vdf?dl=1'; // Don't forget ?dl=1 when updating

function jsonp_decode($jsonp, $assoc = FALSE)
{
    if ($jsonp[0] !== '[' && $jsonp[0] !== '{') // We have JSONP
        $jsonp = substr($jsonp, strpos($jsonp, '('));

    return json_decode(trim($jsonp, '();'), $assoc);
}

setlocale(LC_ALL, 'en_US.UTF8');
function toAscii($str, $replace = array("'", '/', '\\'), $delimiter = '-')
{
    if (!empty($replace))
        $str = str_replace((array)$replace, ' ', $str);

    $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $str = preg_replace("#[^a-zA-Z0-9/_|+ -]#", '', $str);
    $str = strtolower(trim($str, '-'));
    $str = preg_replace("#[/_|+ -]+#", $delimiter, $str);

    return $str;
}


//	isset($CACHE_MAX_AGE) || $CACHE_MAX_AGE = strtotime('-10 minutes');
isset($CACHE_MAX_AGE) || $CACHE_MAX_AGE = strtotime('-60 minutes');
//	isset($CACHE_MAX_AGE) || $CACHE_MAX_AGE = strtotime('-9999 minutes');

if ($_GET['country'])
    $countryData = (object)['country_code' => $_GET['country']];
else {
    $countryData = jsonp_decode(file_get_contents('https://geoip-db.com/jsonp/' . $_SERVER['REMOTE_ADDR']));
    in_array($countryData->country_code, ['MY', 'CA', 'PH']) || $countryData->country_code = 'CA';
}
if (!$countryData) exit('Failed to get country data, likely the geoip-db.com api failed');

//	$library = vdf_decode( file_get_contents('sharedconfig.vdf') )['UserRoamingConfigStore']['Software']['Valve']['Steam']['apps'];
$library = file_get_contents($DROPBOX_LIBRARY_FILE);

if ($library) $library = vdf_decode($library);
else exit("Could not get Steam library file: $DROPBOX_LIBRARY_FILE");

$library = $library['UserRoamingConfigStore']['Software']['Valve']['Steam']['apps'];

// Obsolete apps (Left 4 dead 2 beta), Crappy apps (Vanguard princess, Chroma squad, Game tycoon)
$appsToSkip = [223530, 219540, 262150, 251130, 273770];
$onlineCoop = array();
$appTags = array();
$limit = $_GET['limit'] ? $_GET['limit'] : 99999;
$counter = 0;

// Loop in apps of the vdf file
foreach ($library as $appId => $appDetails) {
    if ($counter > $limit) break;
    $fetchFailed = false;
//		if ( !isset($appDetails['tags']) || (!in_array('Online Co-op', $appDetails['tags']) && !in_array('Online Co-op fix', $appDetails['tags'])) ) continue;
    if (in_array($appId, $appsToSkip)) continue; // Skip beta apps and obsolete apps (Left 4 Dead Beta)
    if (!isset($appDetails['tags'])) continue; // Skip apps with no tags set by me
    foreach ($appDetails['tags'] as $tag)
        $appTags[$appId] .= isset($localExceptions[$tag]) ? $localExceptions[$tag] : ' ' . toAscii($tag);

    $cachedFile = "cache/$appId$countryData->country_code.json";
    // If app cache file is obsolete, go get the new one
    if (($filemtime = filemtime($cachedFile)) <= $CACHE_MAX_AGE && false) {
        ++$counter;
        $steamData = json_decode(file_get_contents("http://store.steampowered.com/api/appdetails?appids=$appId&cc=$countryData->country_code"), TRUE);
        // Only write cache file if game was retrieved (If game doesn't exist anymore and we have a cached copy, the cache will be used instead)
        // or in case API limit was reached and we have an old cached version to use
        if ($steamData[$appId]['success']) {
            $onlineCoop[$appId] = $steamData[$appId]['data'];
            file_put_contents($cachedFile, json_encode($onlineCoop[$appId]));
//                continue;
        } else {
            // Failed to get data, mark it here
            $fetchFailed = true;
        }
    }


    if ($onlineCoop[$appId]) { // App data was already fetched from Steam request above
        $onlineCoop[$appId]['dataFrom'] = "Steam data from " . strftime("%Y-%m-%d %H:%M:%S");
    } elseif (is_file($cachedFile)) { // Else, check if app data was cached
        $onlineCoop[$appId] = json_decode(file_get_contents($cachedFile), TRUE);
        $onlineCoop[$appId]['dataFrom'] = "Cached data from " . strftime("%Y-%m-%d %H:%M:%S", filemtime($cachedFile));
    } elseif (is_file("custom_data/$appId.json")) { // Else, check if there is custom data, entered by me
        $onlineCoop[$appId] = json_decode(file_get_contents("custom_data/$appId.json"), TRUE);
        $onlineCoop[$appId]['dataFrom'] = "Custom data from " . strftime("%Y-%m-%d %H:%M:%S", filemtime("custom_data/$appId.json"));
    } else { // Else, No data, have no info about this app
        $onlineCoop[$appId] = json_decode(file_get_contents('custom_data/default.json'), TRUE);
        $onlineCoop[$appId]['dataFrom'] = 'No data';
//			echo "$appId<br>";
    }

    $onlineCoop[$appId]['fetchFailed'] = $fetchFailed;
}

