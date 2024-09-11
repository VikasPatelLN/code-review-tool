
## Steps to Setup Locally CRT

1) git clone https://github.com/VikasPatelLN/code-review-tool.git 
2) Rename .env.example file to .env
3) Install composer globally if you dont have  (Get this from Topdesk)
4) Run command  : composer install or composer dump-autoload
5) php artisan key:generate
6) changes below key in .env file

### 
    
    GIT_REPOSITORY_URL="https://api.github.com/repos/"
    GIT_BEARER_TOKEN=
    GIT_REPO_OWNER=""
    GIT_REPO_NAME="code-review"

    RISK_GPT_AZURE_URL="https://ln-chatgpt-bk.azurewebsites.net/api/message"
    RISK_GPT_AZURE_TOKEN=


7)Run command to start the server : php artisan serve