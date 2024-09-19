@extends('layouts.app')

@section('title', 'Profile - Barta App')

@section('content')
      <!-- Cover Container -->
      <section class="bg-white border-2 p-8 border-gray-800 rounded-xl min-h-[400px] space-y-8 flex items-center flex-col justify-center">
        <!-- Profile Info -->
        <div class="flex gap-4 justify-center flex-col text-center items-center">
          @if (session('status'))
              <div class="font-bold md:text-xl text-green-500">
                  {{ session('status') }}
              </div>
          @endif
          <div>
            <h1 class="font-bold md:text-2xl">{{$user->firstname .' '. $user->lastname}}</h1>
            <p class="text-gray-700">{{$user->bio ?? "Hello! I am ".$user->firstname .' '. $user->lastname .'.'}}</p>
          </div>
          <!-- / User Meta -->
        </div>
        
        <a href="{{route('profile.edit')}}" type="button" class="-m-2 flex gap-2 items-center rounded-full px-4 py-2 font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path>
          </svg>

          Edit Profile
        </a>
      </section>
@endsection