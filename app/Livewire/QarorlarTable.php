<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Qaror;

class QarorlarTable extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';
    protected string $pageName = 'p'; // 🔥 MUHIM

    public string $search = '';
    public string $number = '';
    public string $year   = '';
    public int $perPage = 25;

    protected $queryString = [
        'search'  => ['except' => ''],
        'number'  => ['except' => ''],
        'year'    => ['except' => ''],
        'perPage' => ['except' => 25],
    ];

    public function updatedSearch()  { $this->resetPage(); }
    public function updatedNumber()  { $this->resetPage(); }
    public function updatedYear()    { $this->resetPage(); }

    public function updatedPerPage($value)
    {
        $this->perPage = (int) $value;
        $this->resetPage();
    }

    public function render()
    {
        $qarorlar = Qaror::query()
            ->when(trim($this->search) !== '', function ($q) {
                $q->where('title', 'like', '%' . trim($this->search) . '%');
            })
            ->when(trim($this->number) !== '', function ($q) {
                $q->where('number', 'like', '%' . trim($this->number) . '%');
            })
            ->orderByDesc('number')
            ->when($this->year !== '', function ($q) {
                $q->whereYear('created_date', $this->year);
            })
            ->orderByRaw('CAST(number AS UNSIGNED) DESC')
           ->paginate($this->perPage);

        return view('livewire.qarorlar-table', compact('qarorlar'));
    }
    public function getYearsProperty()
    {
        return Qaror::query()
            ->selectRaw('YEAR(created_date) as y')
            ->whereNotNull('created_date')
            ->distinct()
            ->orderByDesc('y')
            ->pluck('y');
    }
}

