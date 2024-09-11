<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use JetBrains\PhpStorm\NoReturn;

class GitHubController extends Controller
{

    protected Client $httpClient;
    protected array $requestOptions = [];
    protected string $url;
    protected string $repo;
    protected mixed $repoOwner;

    public function __construct()
    {
        $this->httpClient = new Client();
        $this->requestOptions = [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
                'Authorization' => env('GIT_BEARER_TOKEN'),
            ]
        ];
        $this->url = env('GIT_REPOSITORY_URL');
        $this->repo = env('GIT_REPO_NAME');
        $this->repoOwner = env('GIT_REPO_OWNER');
    }


    /**
     * Display a listing of the resource.
     */
    public function fetchPullRequest()
    {
        $responseData = [];
        $pullRequestsURI = $this->url.$this->repoOwner.'/'.$this->repo.'/pulls';
        $response = $this->httpClient->get($pullRequestsURI, $this->requestOptions);
        $pullRequestList = collect(json_decode($response->getBody(), true));

        $pullRequestList->each(function ($item, $key) use (&$responseData) {
           $responseData[$key]['pr_name'] = $item['title'];
           $responseData[$key]['pr_url'] = $item['html_url'];
           $responseData[$key]['pr_number'] = $item['number'];
        });

        return $responseData;
    }


    public function getFilesAndContent(Request $request)
    {

        $this->requestOptions['headers']['Accept'] = 'application/vnd.github+json';

        $pr_number = (int)$request->get('pull_request_number');
        $pullRequestFilesURI = $this->url.$this->repoOwner.'/'.$this->repo.'/pulls/'.$pr_number.'/files';
        $response = $this->httpClient->get($pullRequestFilesURI, $this->requestOptions);
        $pullRequestFileList = collect(json_decode($response->getBody(), true));

        $responseData = [];
        $pullRequestFileList->each(function ($item, $key) use (&$responseData) {
            $responseData[$key]['file_name'] = $item['filename'];
            $responseData[$key]['content_url'] = $item['contents_url'];

            $this->requestOptions['headers']['Accept'] = 'application/vnd.github.html+json';

            $fileContentResponse = $this->httpClient->get($item['contents_url'], $this->requestOptions);

            $responseData[$key]['file_content'] = $fileContentResponse->getBody()->getContents();
        });


        return $responseData;
    }


    public function getFileContent(string $fileContentUrl): false|string
    {
        $this->requestOptions['headers']['Accept'] = 'application/vnd.github.json';

        $fileContentResponse = $this->httpClient->get($fileContentUrl, $this->requestOptions);

        return base64_decode(collect(json_decode($fileContentResponse->getBody(), true))['content']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
