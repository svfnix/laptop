<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;
use Telegram\Bot\Api;
use Telegram\Bot\HttpClients\GuzzleHttpClient;

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

    function icon($text){

        if(strpos($text, 'مشخصات فیزیکی') !== false){
            return '💻';
        }

        if(strpos($text, 'پردازنده مرکزی') !== false){
            return '🎛';
        }

        if(strpos($text, 'حافظه RAM') !== false){
            return '®';
        }

        if(strpos($text, 'حافظه داخلی') !== false){
            return '💾';
        }

        if(strpos($text, 'پردازنده گرافیکی') !== false){
            return '👁‍🗨';
        }

        if(strpos($text, 'صفحه نمایش') !== false){
            return '🖥';
        }

        if(strpos($text, 'امکانات') !== false){
            return '🕹';
        }

        if(strpos($text, 'سایر مشخصات') !== true){
            return '⚙';
        }

    }

    function extract_message($item){

        $crawler = new Crawler($item['_source']['DetailSource']);

        $data = [];
        $list = $crawler->filter('span');
        foreach($list as $span){
            $data[]= trim($span->textContent);
        };

        $list = [];
        while($key = array_shift($data)){
            $list[$key] = array_shift($data);
        }

        $return = ['<strong>' . html_entity_decode($item['_source']['FaTitle']) . '</strong>', ''];
        foreach ($list as $key => $val){
            $return[] = $this->icon($key) . " {$key} :\n {$val}\n";
        }

        $return[] = '<strong>📈 قیمت ' . number_format($item['_source']['MinPriceList'] / 10) . ' تومان</strong>';
        $return[] = '--------------';
        $return[] = 'دریافت قیمت بروز لپتاپ 👈';
        $return[] = '';
        $return[] = '✅ https://t.me/joinchat/AAAAAEHDdz0C6TZ3Zcqegg';

        return implode("\n", $return);
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
            'dell' => 2
        ];

        $telegram = new Api();

        foreach ($brands as $brand => $code) {

            $list = file_get_contents("https://search.digikala.com/api/search/?category=c18&brand={$code}&pageSize=48&sortBy=10&status=2");
            $list = mb_convert_encoding($list, 'HTML-ENTITIES', "UTF-8");
            $list = json_decode($list, 1);

            $logo_sent = false;
            foreach ($list['hits']['hits'] as $item) {

                if(!$logo_sent){
                    $telegram->sendPhoto([
                        'chat_id' => "-1001103329085",
                        'photo' => "http://136.243.158.61/brands/{$brand}.png",
                        'caption' => "💻لیست قیمت لپتاپ های برند {$brand}"
                    ]);
                    $logo_sent = true;
                }

                $telegram->sendMessage([
                    'chat_id' => "-1001103329085",
                    'text' => $this->extract_message($item),
                    'parse_mode' => 'HTML'
                ]);

                die();
            }

        }





       /*
       ['_source']['DetailSource']
       $counter = 0;
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
