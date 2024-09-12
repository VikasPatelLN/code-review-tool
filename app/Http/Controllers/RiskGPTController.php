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
        "Coding Standard" => "Please review the following code and check if the code achieve its intended purpose without errors and do not suggest any follow up questions in response ?\n",
        "Resource Usage"=> "Please review the following code and check if the code optimized for memory and CPU usage and do not suggest any follow up questions in response ?\n",
        "Readability"=> "Please review the following code and check if the code easy to read and understand? Are there any parts that could be refactored for clarity and do not suggest any follow up questions in response ?\n",
        "Compliance"=> "Please review the following code and check if the the code comply with industry standards and best practices and do not suggest any follow up questions in response ?\n",
        "Security Considerations"=> "Please review the following code and check any potential security issues or areas for improvement and do not suggest any follow up questions in response ?\n"
    ];

    public string $verbiage = "\n
                Give specific feedback each aspect\n.
                NOTE:If the file contains only simple text and does not include any code or special formatting then do not do all steps mentioned above";

    public function aiCodeReview(Request $request)
    {

        $responseData  = [];
        $fileContentUrl = $request->get('file_url');

        $content = (new GitHubController())->getFileContent($fileContentUrl);

        foreach ($this->actions as $key => $action) {
            $this->defaultRiskGptParameters['content'] = $action . $content. $this->verbiage;

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
