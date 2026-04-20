<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Flux\Flux;

new class extends Component
{
    use WithPagination;
    public string $search = '';
    public string $roleFilter = 'all';
    public string $sortBy = '';
    public string $sortDirection = 'asc';


    //returns users that are filtered

    public function with() : array {
    $query = User::with('roles')->latest();

        //filter the search query
        if($this->search){
            $query->where('name','like','%'.$this->search.'%')
            ->orWhere('email','like','%'.$this->search.'%');
        }

        //filter the statues query
        
         if($this->roleFilter!=='all'){
            $query->whereHas('roles', function($q){
            $q->where('name',$this->roleFilter);});
        }

        //authorization : authors can see only their own users
        /**if(auth()->user()->hasRole('author')){
            $query->where('user_id',auth()->user()->id);
        }*/
        return [
            'users' => $query->paginate(10),
            'roles' => Role::all(),
        ];
    }

    public function updatingSearch() {
        $this->resetPage();
    }
    public function updatingRoleFilter() {
        $this->resetPage();
    }

    public function deleteuser(User $user){
        //authorize
        if($user->id === auth()->id()){
            session()->flash('error','You cannot delete your account!');
            return ;
        }

        if(auth()->user()->can('manage users')){
            $user->delete();

            //session()->flash('sucess','user has been deleted successfully');
            Flux::toast('Your changes have been saved.');
        }
    }
    public function sort($column) {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }
};
?>

<div>
    <!-- header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">users</h1>
        <p class="mt-1 text-sm text-grey-600">Manage user accounts and roles</p>
    </div>
    <!-- end of header -->

    {{-- filters --}}
    <div class="mb-6 rounded-lg   p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <flux:input kbd="⌘K" icon="magnifying-glass" placeholder="Search users..." 
                wire:model.live.debounce.300ms="search"/>
            </div>
            <div class="sm:w-48">
                <flux:select wire:model.live="roleFilter" placeholder="Choose industry...">
                    <flux:select.option value="all">All users</flux:select.option>
                    @foreach ($roles as $role)
                        <flux:select.option value="{{$role->name}}">{{ ucfirst($role->name) }}</flux:select.option>   
                    @endforeach
                    
                </flux:select>
            </div>
            @can('manage users')
                <div>
                    <flux:button
                        href="{{ route('users.create') }}"
                        icon="plus"
                    >
                        New User
                    </flux:button>
                </div>        
            @endcan
        </div>
    </div>
    

    

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p4" wire:transition>
            <p class="text-sm text-green-800"> {{ session('success') }}</p>
        </div>
        
    @endif
    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p4" wire:transition>
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
        
    @endif

    <div class="rounded-lg   overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table :paginate="$users">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'title'" :direction="$sortDirection" wire:click="sort('user')">User</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'author'" :direction="$sortDirection" wire:click="sort('email')">Email</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'roleFilter'" :direction="$sortDirection" wire:click="sort('roles')">Roles</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('joined')">Joined</flux:table.column>
                    <flux:table.column >Actions</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ( $users as $user )
                        <flux:table.row :key="$user->id" wire:transition >
                            <flux:table.cell class="px-6 py-4 whitespace-nowrap">
                               <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ $user->name }}&background=4f46e5&color=fff" alt="" />
                                     </div>
                                    <div class="ml-4"> 
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                        </div>
                                    </div>
                               </div>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">
                               <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap gap-1">
                                @forelse ($user->roles as $role)
                                    <span class="px-2 inkine-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @empty
                                    <span class="px-2 inkine-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        No role
                                    </span>
                                @endforelse
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('M d,Y') }}
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-start gap-2">
                                    @if (auth()->user()->can('manage users'))
                                        <flux:button
                                            href="{{ route('users.edit',$user) }}"
                                          
                                            variant="primary"
                                            size="sm"
                                            
                                        >
                                            Edit
                                        </flux:button>
                                    @endif
                                    @if (auth()->id() !== $user->id)
                                        <flux:button
                                            wire:click="deleteuser({{ $user->id }})"
                                            icon:trailing="trash"
                                            wire:confirm="Are  you sure you want to delete this user?"
                                            variant="danger"
                                            size="sm"
                                        >
                                            Delete
                                        </flux:button>
                                    @endif
                                </div>
                            </flux:table.cell>

                        </flux:table.row>
                      @empty
                        <flux:table.row>
                            <flux:table.cell class="px-6 py-12 text-center text-gray-400" colspan="6">
                                    No user found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

        </div>
    </div>
</div>