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

class GetArticleFromTheGuardianApi implements ShouldQueue
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
        $apiKey = env('THE_GUARDIAN_KEY');
        $url = "https://content.guardianapis.com/search?format=json&from-date={$today}&order-by=relevance&api-key={$apiKey}&page-size=200&to-date={$today}&show-fields=thumbnail";
        $response = file_get_contents($url);
        $newsData = json_decode($response, true);

        foreach ($newsData["response"]["results"] as $article) {
            $model = new Article();

            $model->title = $article["webTitle"];
            $model->content = $article['lead_paragraph'] ?? NULL;
            $model->url = $article["webUrl"];
            $model->urlToImage = isset($article['fields']) ? $article['fields']['thumbnail'] : NULL;
            $model->author = $article['author'] ?? NULL;
            $model->source = "The Guardian";
            $model->category = $article["pillarName"];
            $model->publishedAt = Carbon::parse($article["webPublicationDate"])->toDateTimeString();

            $model->save();
        }
    }
}
