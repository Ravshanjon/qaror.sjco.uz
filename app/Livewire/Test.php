<?php

// app/Livewire/Test.php
namespace App\Livewire;

use Livewire\Component;

class Test extends Component
{
    public $text = '';

    public function render()
    {
        return view('livewire.test');
    }
}
