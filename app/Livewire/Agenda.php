<?php

namespace App\Livewire;

use App\Models\Session;
use Livewire\Attributes\Url;
use Livewire\Component;

class Agenda extends Component
{
    #[Url]
    public string $type = '';

    #[Url]
    public string $track = '';

    #[Url]
    public string $date = '';

    public function resetFilters(): void
    {
        $this->reset(['type', 'track', 'date']);
    }

    public function render()
    {
        $sessions = Session::with(['hall', 'speakers'])
            ->published()
            ->forCurrentEvent()
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->track, fn ($q) => $q->where('track', $this->track))
            ->when($this->date, fn ($q) => $q->whereDate('date', $this->date))
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn (Session $session) => $session->date?->toDateString());

        $availableDates = Session::published()->forCurrentEvent()
            ->whereNotNull('date')
            ->distinct()
            ->orderBy('date')
            ->pluck('date');

        $availableTracks = Session::published()->forCurrentEvent()
            ->whereNotNull('track')
            ->distinct()
            ->pluck('track');

        return view('livewire.agenda', [
            'sessionsByDate' => $sessions,
            'availableDates' => $availableDates,
            'availableTracks' => $availableTracks,
            'types' => Session::TYPES,
        ]);
    }
}
