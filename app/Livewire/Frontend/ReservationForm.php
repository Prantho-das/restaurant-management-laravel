<?php

namespace App\Livewire\Frontend;

use App\Models\Reservation;
use App\Services\MetaService;
use Livewire\Component;

class ReservationForm extends Component
{
    public $name = '';

    public $phone = '';

    public $date = '';

    public $guests = '02 PERS';

    public $arrangement = 'CASUAL DINING';

    public $notes = '';

    public $isSuccess = false;

    protected $rules = [
        'name' => 'required|min:2',
        'phone' => 'required|min:6',
        'date' => 'required|date',
        'guests' => 'required',
        'arrangement' => 'required',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        // Set default date to today
        $this->date = date('Y-m-d');
    }

    public function submit(MetaService $metaService)
    {
        $this->validate();

        $reservation = Reservation::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'date' => $this->date,
            'guests' => $this->guests,
            'arrangement' => $this->arrangement,
            'notes' => $this->notes,
            'status' => 'pending',
        ]);

        // Trigger conversion events
        $this->dispatch('conversion-event', name: 'Lead', data: [
            'content_name' => 'Table Reservation',
            'content_category' => $this->arrangement,
        ]);

        // Server-side tracking (CAPI)
        $metaService->sendEvent('Lead', [
            'content_name' => 'Table Reservation',
        ], [
            'fn' => hash('sha256', strtolower(trim($this->name))),
            'ph' => hash('sha256', preg_replace('/[^0-9]/', '', $this->phone)),
        ]);

        $this->isSuccess = true;
        $this->reset(['name', 'phone', 'notes']);

        $this->dispatch('notify',
            message: 'Reservation Requested Successfully',
            type: 'success'
        );
    }

    public function render()
    {
        return view('livewire.frontend.reservation-form');
    }
}
