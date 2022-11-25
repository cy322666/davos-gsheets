<?php

namespace App\Console\Commands;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;
use Ufee\Amo\Oauthapi;

class GetEvents extends Command
{
    protected $signature = 'events:get';

    protected $description = 'Command description';

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $client_id     = '355993cc-40a0-4134-80b5-7e9a25c2cc15';
        $client_secret = '5f2N9Vp0WbsqIn9PgtHL25khqBVGAwlQ4tv9PWqCqaobHfCmsN8OPH7Tk2OsrVe1';
        $access_code   = 'def50200df75a0320e80dcb3d338d611a3794e56e039c65d61cc3aa6d783fd79e867de0ef11f6b53215600327db50b34d065328777f4d87ddc16fc5944f9f79744ca0b34055ceb8d73768e027cdf5b7a09aa58041fae23d9bd647601091f5e4e4ede17873dc44477ba67492272f2bcd2d4ec7dafa1d353e2cde7fcab8df9ec26d6ddad24df14bad681362cb9f4d8919579ebcde6e0d474de35128b5a7a89b81205a83947a5e4062293529c098b27e4d69688aebf61dcc48d19ea6b3435c0dbd9c57cdeda58e0b3188ea597069d3b1cb348ff00689a93591ec0b093ce2a2b04509c9109c0f313ae514e09a9ccdaf7a43bb6c67626fc2e39d0a76401b49be714afef357a0cce9dd94f84e1dda5fbda218c0053698faeb0d0b6b0e93d840225ca90b372e26a81244f81a15fff655a0e2103748cda87ca77e4025c52134c6439af7dd469a2ed140a70cbcc48188e357ac82ddaed2eea231e67d4557eeb8cb4c64e99fc487a2bbc785452157dff72d91635dce69d9f68b8ec1167536e0a1aa610ef8865c46b8e354290c63b1a0b70ac7768087b1d16baab67a85be628186a50740350151d0298dc00071dd41bccfcc504174412042aca505d14635deb611eb2e695b76dd7608632d19a3df241ee4958';
        $domain        = 'residencesatthehardrockhoteldavos';

        $amoApi = Oauthapi::setInstance([
            'domain'        => $domain,
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri'  => 'https://'.$domain.'.kommo.com/settings/widgets/',
            'zone'          => 'com',
        ]);

        $amoApi = Oauthapi::getInstance($client_id);

        for ($i = 1; ;$i++) {

            echo 'page : '.$i."\n";

            $events = $amoApi->ajax()->get('/api/v4/events', [
                'limit' => 100,
                'page'  => $i,
                'filter' => [
                    'entity' => 'leads',
                    'value_after' => [
                        'leads_statuses' => [
                            [],
                            [],
                        ],
                    ],
                ],
            ]);

            if (count($events->_embedded->events) == 0) {

                dd('end');
            }

            foreach ($events->_embedded->events as $event) {

                if ($event->entity_type == 'lead' &&
                    $event->type == 'lead_status_changed') {

                    try {

                        Event::query()->create([
                            'change_at'   => Carbon::parse($event->created_at)->format('Y-m-d H:i:s'),
                            'lead_id'     => $event->entity_id,
                            'status_at'   => $event->value_before[0]->lead_status->id,
                            'pipeline_at' => $event->value_before[0]->lead_status->pipeline_id,
                            'status_to'   => $event->value_after[0]->lead_status->id,
                            'pipeline_to' => $event->value_after[0]->lead_status->pipeline_id,
                        ]);

                    } catch (Throwable $exception) {

                        dd($event, $exception->getMessage().' '.$exception->getLine());
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
