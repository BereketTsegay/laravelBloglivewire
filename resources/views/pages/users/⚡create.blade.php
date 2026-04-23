<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use Spatie\Permission\Models\Role;
use App\Models\User;
// use illuminate\Support\Facades\Hash;

new class extends Component
{
    //
    #[Validate('required|string|max:255')]
    public string $name='';
    #[Validate('required|string|email|max:255|unique:users')]
    public string $email='';
    #[Validate('required|string|confirmed|min:8')]
    public string $password ='';
    #[Validate('required|same:password')]
    public string $password_confirmation ='';
    #[Validate('required|array|min:1')]
    public array $selectedRoles = [];


    public function with(): array {
        return [
            'roles'=>Role::all(),
        ];
    }

    public function save(){
        $this->validate();

        $user = User::create([
            'name'=> $this->name,
            'email'=> $this->email,
            'password'=> Hash::make($this->password),
        ]);

        $user->assignRole($this->selectedRoles);

        session()->flash('success','New User has been created');

        $this->redirect(route('users.index'), navigate:true);
    }
};
?>

<div>
     <!-- header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create New User</h1>
        <p class="mt-1 text-sm text-grey-600">Add new user to the system</p>
    </div>

    {{-- form --}}
    <form wire:submit="save" class="space-y-6">
        <flux:field>
            <flux:input placeholder="Full Name" type="text" label="Name" wire:model.live.debounce="name"/>
            <flux:error name="name" />
        </flux:field>
        <flux:field>
            <flux:input placeholder="youremail@ex.domain" type="email" label="email"
            wire:model.live="email"/>
            <flux:error name="eamil" />

        </flux:field>
        <flux:field>

            <flux:input
            placeholder="password"
            type="password"
            label="Password"
            wire:model="password"/>
            <flux:error name="password" />
        </flux:field>
        <flux:field>

            <flux:input
                placeholder="Confirm Password"
                type="password"
                label="Confirm Password"
                wire:model.live="password_confirmation"/>
            <flux:error name="password_confirmation" />
        </flux:field>
        <flux:fieldset>
            <flux:checkbox.group wire:model="selectedRoles" label="Roles">
            <flux:description>Choose the role for your user.</flux:description>
               <div class="flex gap-4 *:gap-x-2">
                    @foreach ($roles as $role)


                                <flux:checkbox :key="$role->id" value="{{$role->name}}" label="{{$role->name}}" />



                    @endforeach
                </div>
            </flux:checkbox.group>
            <flux:error name="selectedRoles" />
        </flux:fieldset>

    <div class="flex gap-3">
        <flux:button type="submit" variant="primary">Create User</flux:button>
        <flux:button
         href="{{ route('users.index') }}"
        variant="danger">Cancel</flux:button>
    </div>
    </form>
    {{-- end of form --}}
</div>
