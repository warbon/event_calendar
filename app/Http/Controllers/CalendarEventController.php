<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\CalendarEvent;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalendarEventController extends Controller
{
    public function storeEvents(Request $request) {

        $fromDate = new Carbon($request->fromDate);
        $toDate = new Carbon($request->toDate);
        $selectedDays = collect($request->days)->filter(function ($day) {
            return $day['selected'];
        });
        $selectedDays = $selectedDays->map(function ($day) {
            return $day['key'];
        });

        for($d = $fromDate; $d <= $toDate; $d->modify('+1 day')){
            if ($selectedDays->contains($d->dayOfWeek)) {
                Log::info($d);
                CalendarEvent::updateOrCreate(
                    ['event_date' => $d],
                    ['event_name' => $request->eventName]
                );
            } else {
                CalendarEvent::where('event_date', $d)->delete();
            }
        }

        return  Response::HTTP_OK;
    }

    public function getEvents(Request $request) {
        $events = CalendarEvent::all();

        return $events;
    }
}
