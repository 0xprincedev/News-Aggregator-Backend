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

class GetArticleFromNewsDataApi implements ShouldQueue
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
        $apiKey = env('NEWS_DATA_API');
        $url = "https://newsdata.io/api/1/news?apikey={$apiKey}&language=en&timeframe=24";

        $nextPage = true;

        while ($nextPage) {
            $response = file_get_contents($url);
            $newsData = json_decode($response, true);

            foreach ($newsData["results"] as $article) {
                $model = new Article();

                $model->title = $article["title"];
                $model->content = $article["content"];
                $model->url = $article["link"];
                $model->urlToImage = $article["image_url"];
                $model->author = isset($article["creator"]) ? $article["creator"][0] : "";
                $model->source = $article["source_id"];
                $model->category = $article["category"][0];
                $model->publishedAt = Carbon::parse($article["pubDate"])->toDateTimeString();

                $model->save();
            }

            $nextPage = $newsData["nextPage"];
        }
    }
}
