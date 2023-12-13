<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

use App\Models\Article;

class GetArticleFromNewsAPi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $today = Carbon::now()->format('Y-m-d');
        $apiKey = env('NEWS_API_KEY');
        $url = "https://newsapi.org/v2/everything?q=_&from=2023-12-1&to={$today}&sortBy=popularity&apiKey={$apiKey}";
        $response = file_get_contents($url);
        $newsData = json_decode($response, true);

        foreach ($newsData["articles"] as $article) {
            $model = new Article();

            $model->title = $article["title"];
            $model->content = $article["content"];
            $model->url = $article["url"];
            $model->urlToImage = $article["urlToImage"];
            $model->author = $article["author"];
            $model->source = $article["source"]["name"];
            $model->publishedAt = $article["publishedAt"];

            $model->save();
        }
    }
}
