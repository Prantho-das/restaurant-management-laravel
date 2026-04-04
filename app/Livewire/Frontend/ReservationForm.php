<?php

namespace App\Livewire\Frontend;

use App\Models\Reservation;
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

    public function submit()
    {
        $this->validate();

        Reservation::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'date' => $this->date,
            'guests' => $this->guests,
            'arrangement' => $this->arrangement,
            'notes' => $this->notes,
            'status' => 'pending',
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
