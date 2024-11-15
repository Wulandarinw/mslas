<?php

namespace App\Livewire;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyAccountPage extends Component
{
    public $FName;
    public $LName;
    public $email;
    public $phone;
    public $gender;
    public $date_of_birth;
    public $profile_icon;
    
    protected $rules = [
        'FName' => 'required|min:2',
        'LName' => 'required|min:2',
        'email' => 'required|email',
        'phone' => 'required',
        'gender' => 'required|in:Laki-laki,Perempuan',
        'date_of_birth' => 'required|date',
    ];

    public function mount()
    {
        $customer = Auth::user()->customers;
        
        if ($customer) {
            $this->FName = $customer->FName;
            $this->LName = $customer->LName;
            $this->email = $customer->email;
            $this->phone = $customer->phone;
            $this->gender = $customer->gender;
            $this->date_of_birth = $customer->date_of_birth;
            $this->profile_icon = $customer->profile_icon;
        }
    }

    public function save()
    {
        $this->validate();

        $customer = Auth::user()->customers;
        
        if (!$customer) {
            $customer = new Customer();
            $customer->user_id = Auth::id();
        }

        $customer->FName = $this->FName;
        $customer->LName = $this->LName;
        $customer->email = $this->email;
        $customer->phone = $this->phone;
        $customer->gender = $this->gender;
        $customer->date_of_birth = $this->date_of_birth;
        $customer->save();

        session()->flash('message', 'Profile updated successfully!');
    }

    public function render()
    {
        return view('livewire.my-account-page');
    }
}
