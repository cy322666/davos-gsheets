<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function hook(Request $request)
    {
        $data = $request->toArray()['leads']['status'][0];

        Event::query()->create([
            'change_at'   => '',
            'lead_id'     => $data['id'],
            'status_at'   => $data['old_status_id'],
            'status_to'   => $data['status_id'],
            'pipeline_at' => $data['old_pipeline_id'],
            'pipeline_to' => $data['pipeline_id'],
        ]);
    }
}
