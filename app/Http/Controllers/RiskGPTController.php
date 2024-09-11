<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class RiskGPTController extends Controller
{

    protected array $defaultRiskGptParameters = [
        "mode"=>"General",
        "selected_user_documents"=>[],
        "selected_internal_document_sources"=>[],
        "staging_thread_preferences"=> [
            "model"=>"gpt-35-turbo",
            "stop"=>"",
            "modelPersona"=>"",
            "userBackground"=>"",
            "topP"=>1,
            "temperature"=>1,
            "frequencyPenalty"=>0,
            "presencePenalty"=>0,
            "maxTokens"=>4096,
            "embeddingModel"=>"text-embedding-ada-002",
            "chunkerType"=>"textChunker",
            "threadPreferenceFlag"=>0,
            "selectedDocuments"=>""
        ]];

    protected array $actions = [
        'Review Points' => "Please do the code review and check if the code follows the coding standards ",
        'Security Concerns' => "Please check the following code for security vulnerabilities ",
        'Optimized Code' => "Please check if the following code can be optimization ",
    ];

    public function aiCodeReview(Request $request)
    {

        $responseData  = [];
        $fileContentUrl = $request->get('file_url');

        $content = (new GitHubController())->getFileContent($fileContentUrl);

        foreach ($this->actions as $key => $action) {
            $this->defaultRiskGptParameters['content'] = $action . $content;
            $riskGptResponse = (new Client())->post(env('RISK_GPT_AZURE_URL'), [
                'body' => json_encode($this->defaultRiskGptParameters),
                'headers' => [
                    'Authorization' => env('RISK_GPT_AZURE_TOKEN'),
                    'Content-Type' => 'application/json',
                ]
            ]);

            $encodedData = json_decode($riskGptResponse->getBody());

            $responseData[$key] = htmlentities($encodedData[1]->content);

        }

        return response()->json($responseData);
    }

}
