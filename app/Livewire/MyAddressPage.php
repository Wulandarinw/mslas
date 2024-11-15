<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Address;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Auth;


class MyAddressPage extends Component
{
    public $showModal = false;
    public $name;
    public $phone;
    public $provinsi;
    public $kabupaten;
    public $kecamatan;
    public $kelurahan;
    public $kodepos;
    public $street_address;
    public $type = 'Rumah';
    
    public $provinces = [];
    public $kabupatens = [];
    public $kecamatans = [];
    public $kelurahans = [];
    
    public $addresses = [];

    protected $rules = [
        'name' => 'required|string',
        'phone' => 'required|string',
        'provinsi' => 'required|string',
        'kabupaten' => 'required|string',
        'kecamatan' => 'required|string',
        'kelurahan' => 'required|string',
        'kodepos' => 'required|string',
        'street_address' => 'required|string',
        'type' => 'required|string',
    ];

    public function mount()
    {
        $this->provinces = Address::select('provinsi')
            ->distinct()
            ->orderBy('provinsi')
            ->get();
            
        $this->loadAddresses();
    }

    public function loadAddresses()
    {
        $customer = Auth::user()->customers;
        $this->addresses = CustomerAddress::with('address')
            ->where('customer_id', $customer->customer_id)
            ->get();
    }

    public function updatedProvinsi($value)
    {
        $this->kabupatens = Address::where('provinsi', $value)
            ->select('kabupaten')
            ->distinct()
            ->orderBy('kabupaten')
            ->get();
        $this->kabupaten = '';
        $this->resetKecamatan();
    }

    public function updatedKabupaten($value)
    {
        $this->kecamatans = Address::where('provinsi', $this->provinsi)
            ->where('kabupaten', $value)
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->get();
        $this->kecamatan = '';
        $this->resetKelurahan();
    }

    public function updatedKecamatan($value)
    {
        $this->kelurahans = Address::where('provinsi', $this->provinsi)
            ->where('kabupaten', $this->kabupaten)
            ->where('kecamatan', $value)
            ->select('kelurahan', 'kodepos')
            ->distinct()
            ->orderBy('kelurahan')
            ->get();
        $this->kelurahan = '';
        $this->kodepos = '';
    }

    public function updatedKelurahan($value)
    {
        $address = Address::where('provinsi', $this->provinsi)
            ->where('kabupaten', $this->kabupaten)
            ->where('kecamatan', $this->kecamatan)
            ->where('kelurahan', $value)
            ->first();
            
        if ($address) {
            $this->kodepos = $address->kodepos;
        }
    }

    private function resetKecamatan()
    {
        $this->kecamatans = [];
        $this->kecamatan = '';
        $this->resetKelurahan();
    }

    private function resetKelurahan()
    {
        $this->kelurahans = [];
        $this->kelurahan = '';
        $this->kodepos = '';
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['name', 'phone', 'provinsi', 'kabupaten', 'kecamatan', 
                     'kelurahan', 'kodepos', 'street_address']);
        $this->type = 'Rumah';
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $address = Address::where([
            'provinsi' => $this->provinsi,
            'kabupaten' => $this->kabupaten,
            'kecamatan' => $this->kecamatan,
            'kelurahan' => $this->kelurahan,
            'kodepos' => $this->kodepos,
        ])->first();

        if (!$address) {
            $address = Address::create([
                'provinsi' => $this->provinsi,
                'kabupaten' => $this->kabupaten,
                'kecamatan' => $this->kecamatan,
                'kelurahan' => $this->kelurahan,
                'kodepos' => $this->kodepos,
            ]);
        }

        CustomerAddress::create([
            'customer_id' => Auth::user()->customers->customer_id,
            'fullName' => $this->name,
            'phone' => $this->phone,
            'address_id' => $address->address_id,
            'address_detail' => $this->street_address,
            'mark_as' => $this->type,
        ]);

        $this->closeModal();
        $this->loadAddresses();
        $this->emit('addressSaved');
    }

    public function render()
    {
        return view('livewire.my-address-page');
    }
}
