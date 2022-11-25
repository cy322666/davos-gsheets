<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function hook(Request $request)
    {
        $data = $request->toArray()['leads']['status'][0];

        Event::query()->create([
            'change_at'   => Carbon::now('Europe/Moscow')->format('Y-m-d H:i:s'),
            'lead_id'     => $data['id'],
            'status_at'   => $data['old_status_id'],
            'status_to'   => $data['status_id'],
            'pipeline_at' => $data['old_pipeline_id'],
            'pipeline_to' => $data['pipeline_id'],
        ]);
    }
}
