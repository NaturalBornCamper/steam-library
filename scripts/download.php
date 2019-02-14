<?php

function downloadFile($app_id, $file_url)
{
    // Missing params?
    if (!$app_id || !$file_url) {
        return [
            'success' => false,
            'message' => $$app_id ? 'Missing app_id' : 'Missing file_url'
        ];
    }

    /* GOOD EXAMPLE OF BULLETPROOF CURL BELOW, BUT CURL DOESN'T SUPPORT CDN DOWNLOAD, SO USING SIMPLE "COPY()" INSTEAD
    // cURL available?
    $ch = curl_init($file_url);
    if ($ch === false) {
        return [
            'success' => false,
            'message' => 'Failed to init cURL'
        ];
    }

    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // Note: Update user agent from time to time to get latest chrome by searching "get my user agent" in Google
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36');

    $remoteFileContent = curl_exec($ch);
    $curlError = ($remoteFileContent === false) ? curl_error($ch) : false;
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check if cURL error
    if ($curlError) {
        return [
            'success' => false,
            'message' => 'Failed to get file, returned cURL error: ' . $curlError
        ];
    }

    // Check if file was received
    if ($status >= 400) {
        return [
            'success' => false,
            'message' => 'Failed to get file, returned status ' . $status
        ];
    }
    */

    // Make sure download folder exists
    $downloadFolderPath = "{$_SERVER['DOCUMENT_ROOT']}/downloaded_screenshots/$app_id/";
    if (!is_dir($downloadFolderPath) && !@mkdir($downloadFolderPath, 0755, true)) {
        return [
            'success' => false,
            'message' => "Failed to create folder $downloadFolderPath, check permissions?"
        ];
    }

    $basename = strtok(basename($file_url), '?'); // Strip query string as well
    $localFilePath = $downloadFolderPath . $basename;
    $success = @copy($file_url, $localFilePath);

    if ($success) {
        return [
            'success' => true,
            'message' => 'Image downloaded to ' . $localFilePath
        ];
    } else {
        $errors = error_get_last();
        return [
            'success' => false,
            'message' => "Failed to write $localFilePath, error {$errors['type']}: {$errors['message']}"
        ];
    }
}


if (isset($_GET['app_id']) && isset($_GET['url'])) {

    $json = downloadFile($_GET['app_id'], $_GET['url']);

    $json = array_merge($json, [
        'app_id' => $_GET['app_id'],
        'url' => $_GET['url']
    ]);

    header('Content-type:application/json;charset=utf-8');
    echo json_encode($json);
}