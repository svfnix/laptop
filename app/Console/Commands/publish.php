<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;

class publish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'publish post to channel';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $brands = [
            'apple' => 10,
            'asus' => 4,
            'lenovo' => 94,
            'hp' => 6,
            'acer' => 3,
            'msi' => 95,
            'vio' => 188,
            'microsoft' => 51,
            'dell' => 2,
            'sony' => 1,
        ];

        $telegram = new Api();

        foreach ($brands as $brand => $code) {

            $list = file_get_contents("https://search.digikala.com/api/search/?category=c18&brand={$code}&pageSize=48&sortBy=10&status=2");
            $list = json_decode($list, 1);

            foreach ($list['hits']['hits'] as $item) {

                $telegram->sendMessage([
                    'chat_id' => "-1001103329085",
                    'text' =>
                ]);

                $telegram->sendPhoto([
                    'chat_id' => "-1001103329085",
                    'photo' => str_replace(' ', '%20', "http://file.digikala.com/digikala/{$item['_source']['ImagePath']}"),
                    'caption' => strip_tags($item['_source']['DetailSource'])
                ]);

                die();
            }

        }





       /* $counter = 0;
        $lastvideos = cache('lastvideos', []);
        $new_videos = [];


        $crawler->filter('.video-item__thumb')->each(function ($node) use ($client, $telegram, &$counter, $lastvideos, &$new_videos) {
            if($counter < 5) {
                $href = $node->attr('href');
                $href_arr = explode('/', $href);
                if(count($href_arr) > 4) {
                    if ($href_arr[3] == 'v') {
                        if (!in_array($href_arr[4], $lastvideos)) {
                            try {
                                $this->info('=====================');

                                $crawler2 = $client->request('GET', $href);
                                $link = $crawler2->filter('.download-link > a')->first();

                                $video = explode('?', $link->attr('href'));
                                $video = $video[0];

                                $this->info('title: ' . $node->attr('title'));
                                $this->info('link: ' . $video);


                                $this->info('publishing message ...');
                                $telegram->sendVideo([
                                    'chat_id' => env('CHANNEL'),
                                    'video' => $video,
                                    'caption' => $node->attr('title') . "\n\n📽 " . env('CHANNEL')
                                ]);

                                $this->info('done');

                                $new_videos[] = $href_arr[4];
                                cache(['lastvideos' => $new_videos], Carbon::now()->addYears(1));
                                $counter++;
                            } catch (\Exception $e) {
                                $this->warn($e->getMessage());
                            }
                        }
                    }
                }
            }
        });*/



    }
}
