<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class SocialProfiles extends Component
{

    public ?Collection $identities = null;
    
    public bool $hasIdentities = false;

    public function mount()
    {
        $this->refresh();
    }

    public function refresh()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $this->identities = $user->identities;

        $this->hasIdentities = $this->identities->isNotEmpty();
    }

    public function render()
    {
        return view('livewire.social-profiles');
    }
}
