<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Exception;
use Google_Http_MediaFileUpload;
use Google_Service_Exception;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class YoutubeController extends Controller
{
    function youtube(Request $request)
    {
        //session()->forget('token');
        //dd(session('token'));

        $OAUTH2_CLIENT_ID = env('OAUTH2_CLIENT_ID');
        $OAUTH2_CLIENT_SECRET = env('OAUTH2_CLIENT_SECRET');

        $client = new Google_Client();
        $client->setClientId($OAUTH2_CLIENT_ID);
        $client->setClientSecret($OAUTH2_CLIENT_SECRET);
        $client->setScopes('https://www.googleapis.com/auth/youtube');
        //$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],  FILTER_SANITIZE_URL);
        $redirect = route('youtube');
        $client->setRedirectUri($redirect);

        // Define an object that will be used to make all API requests.
        $youtube = new Google_Service_YouTube($client);

        $code = $request->get('code');
        if (isset($code)) {
            /*dd([
               'state'  => strval(session('state')),
                'code' => strval($request->get('code'))
            ]);*/

            if (strval(session('state')) !== strval($request->get('state'))) {
                die('The session state did not match.');
            }

            $client->authenticate($request->get('code'));
            session(['token' => $client->getAccessToken()]);
            //dd(session('token'));
            //header('Location: ' . $redirect);
            return redirect()->route('youtube');
        }


        //dd(session('token'));
        Log::info('**token session2 ' . session('token')['access_token']);
        if (session()->has('token')) {
            $client->setAccessToken(session('token'));
        }

        // Check to ensure that the access token was successfully acquired.
        if ($client->getAccessToken()) {
            try{
                // Specify the size of each chunk of data, in bytes. Set a higher value for
                // reliable connection as fewer chunks lead to faster uploads. Set a lower
                // value for better recovery on less reliable connections.
                $chunkSizeBytes = 15 * 1024 * 1024;

                // REPLACE this value with the path to the file you are uploading.
                //$videoPath = "videos/video.mp4";
                //$videoPath = storage_path('app/videos') . '/video.mp4';
                $videoPath = storage_path('app/videos') . '/' . session('video_file');
                //$videoPath = 'video_edited.mp4';
                //$videoPath = session('video_file');


                // Create a snippet with title, description, tags and category ID
                // Create an asset resource and set its snippet metadata and type.
                // This example sets the video's title, description, keyword tags, and
                // video category.
                $snippet = new Google_Service_YouTube_VideoSnippet();
                $snippet->setTitle("Test title");
                $snippet->setDescription("Test description");
                $snippet->setTags(array("tag1", "tag2"));

                // Numeric video category. See
                // https://developers.google.com/youtube/v3/docs/videoCategories/list
                $snippet->setCategoryId("29"); //Non-profits & Activism

                // Set the video's status to "public". Valid statuses are "public",
                // "private" and "unlisted".
                $status = new Google_Service_YouTube_VideoStatus();
                $status->privacyStatus = "private";

                // Associate the snippet and status objects with a new video resource.
                $video = new Google_Service_YouTube_Video();
                $video->setSnippet($snippet);
                $video->setStatus($status);

                // Setting the defer flag to true tells the client to return a request which can be called
                // with ->execute(); instead of making the API call immediately.
                $client->setDefer(true);

                // Create a request for the API's videos.insert method to create and upload the video.
                $insertRequest = $youtube->videos->insert("status,snippet", $video);

                // Create a MediaFileUpload object for resumable uploads.
                $media = new Google_Http_MediaFileUpload(
                    $client,
                    $insertRequest,
                    'video/*',
                    null,
                    true,
                    $chunkSizeBytes
                );
                $media->setFileSize(filesize($videoPath));

                Log::info('antes de cargar');

                // Read the media file and upload it chunk by chunk.
                $status = false;
                $handle = fopen($videoPath, "rb");
                while (!$status && !feof($handle)) {
                    Log::info('cargando...');
                    $chunk = fread($handle, $chunkSizeBytes);
                    $status = $media->nextChunk($chunk);
                }

                fclose($handle);

                // If you want to make other calls after the file upload, set setDefer back to false
                $client->setDefer(false);

                dd([
                    'status' => 'video uploaded',
                    'title' => $status['snippet']['title'],
                    'id' => $status['id'],
                ]);

                //dd('termino');

                Log::info('despues de dd');
            } catch (Google_Service_Exception $e) {
                dd('1 A service error occurred: ' . $e->getMessage());
            } catch (Google_Exception $e) {
                dd('2 A service error occurred: ' . $e->getMessage());
            }

            session(['token' => $client->getAccessToken()]);
        } else {
            // If the user hasn't authorized the app, initiate the OAuth flow
            $state = mt_rand();
            $client->setState($state);
            session(['state' => $state]);

            $authUrl = $client->createAuthUrl();

            return view('auth')->with('url', $authUrl);
        }

    }
}
