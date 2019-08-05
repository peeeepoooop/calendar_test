<?php

namespace App\Http\Controllers;


use App\Models\Calendar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CalendarController extends Controller
{

    public function __construct()
    {

    }

    public function index()
    {
        return view('calendars.index');
    }

    public function store(Request $request) {
        $input = $request->all();

        $event_name = Calendar::where('event_name', $input['event_name'])->first();
        if($event_name == null) {
            $event = Calendar::create([
                'event_name' => $input['event_name'],
                'start_date' => $input['start_date'],
                'end_date' => $input['end_date'],
                'week_days' => json_encode($input['week_days']),
            ]);
            $data = [
              'success' => true,
              'message' => 'Event saved successfully.',
              'data' => $event,
            ];

            return response()->json($data);
        }
        else{
            return response()->json(array(
                'error' => true,
                'message' => 'Event is existing.'
            ));
        }
    }
}