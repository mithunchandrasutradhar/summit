<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\View\View;

class CheckInController extends Controller
{
    public function scanner(): View
    {
        return view('checkin.scanner');
    }

    public function checkIn(string $token): View
    {
        $registration = Registration::where('qr_token', $token)->first();

        if (! $registration) {
            return view('checkin.result', ['status' => 'not_found']);
        }

        if ($registration->status === 'cancelled') {
            return view('checkin.result', ['status' => 'cancelled', 'registration' => $registration]);
        }

        $alreadyCheckedIn = (bool) $registration->checked_in_at;

        if (! $alreadyCheckedIn) {
            $registration->update(['checked_in_at' => now()]);
        }

        return view('checkin.result', [
            'status' => $alreadyCheckedIn ? 'already_checked_in' : 'checked_in',
            'registration' => $registration,
        ]);
    }
}
