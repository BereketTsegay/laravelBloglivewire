<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Flux\Flux;

new class extends Component
{
    public User $user;
   //
    #[Validate('required|string|max:255')]
    public string $name='';
    #[Validate('required|string|email|max:255')]
    public string $email='';
    #[Validate('nullable|string|confirmed|min:8')]
    public string $password ='';
    #[Validate('nullable|same:password')]
    public string $password_confirmation ='';
    #[Validate('required|array|min:1')]
    public array $selectedRoles = [];

    public function mount(User $user){
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
    }

    public function rules(): array{
        return [

            'name'=>'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'password'=>'required|confirmed|max:255',
            'selectedRoles'=>'required|array|min:1'
    
        ];
    }
    

    public function with(): array {
        return [
            'roles'=>Role::all(),
        ];
    }

    public function save(){
        $this->validate();

        $this->user->name = $this->name;
        $this->user->email = $this->email;
        if($this->password)$this->user->password = Hash::make($this->password);

        $this->user->save();

        $this->user->syncRoles($this->selectedRoles);

        Flux::toast('Your changes have been saved.');

        $this->redirect(route('users.index'), navigate:true);
    }
};
?>

<div>
     <!-- header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
        <p class="mt-1 text-sm text-grey-600">Update user informaiton</p>
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
        <flux:button type="submit" variant="primary">Update User</flux:button>
        <flux:button 
         href="{{ route('users.index') }}"
        variant="danger">Cancel</flux:button>
    </div>
    </form>
    {{-- end of form --}}
</div>