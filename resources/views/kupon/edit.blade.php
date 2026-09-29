@extends('layouts.sidebar')

@section('title', 'Edit Kupon — Zakira Admin')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between mb-6">
        <div class="font-medium text-zinc-800 dark:text-white text-2xl">Edit Kupon</div>
        <a href="{{ route('kupon.index') }}" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg ps-3 pe-4 inline-flex bg-transparent hover:bg-zinc-800/5 dark:hover:bg-white/15 text-zinc-800 dark:text-white transition">
            <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                <path fill-rule="evenodd" d="M14 8a.75.75 0 0 1-.75.75H4.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 0 1 1.06 1.06L4.56 7.25h8.69A.75.75 0 0 1 14 8Z" clip-rule="evenodd"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    @include('kupon._form', ['coupon' => $coupon, 'action' => route('kupon.update', $coupon), 'method' => 'PUT'])

</div>
@endsection