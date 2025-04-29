<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;



class Avaterupload extends Component
{
    use WithFileUploads;

    public $avatar;

    public function uploadavatar () {
        if (!auth()->check()) { 
            abort(403, 'Unauthorized action.');
        }

         $user = auth()->user();
 
         $fileName = $user->id . "-" . uniqid() . ".jpg";
 
         $manager = new ImageManager(new Driver());
         $image = $manager->read($this->avatar);
         $imageData = $image->cover(120, 120)->toJpeg();
 
         Storage::disk('public')->put('avatars/' . $fileName, $imageData);
 
         $oldAvatar = $user->avatar;
 
         // $request->file('avatar')->store('avatars', 'public');
 
         $user->avatar = $fileName;
         $user->save();
 
         if ($oldAvatar != null) {
             Storage::disk('public')->delete(str_replace("/storage/", "", $oldAvatar));
         }

         session()->flash('success', 'Your avatar has been updated');
 
         return $this->redirect('/manage-avatar', navigate: true);
        
    }
    public function render()
    {
        return view('livewire.avaterupload');
    }
}
