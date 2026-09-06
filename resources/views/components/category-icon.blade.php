@props([
    'category' => null,
    'type' => null,
    'name' => '',
    'class' => 'w-5 h-5'
])

@php
    $catName = strtolower($category->name ?? $name ?? '');
    $catIcon = strtolower($category->icon ?? '');
    $txType = is_string($type) ? $type : ($type->value ?? '');
    
    // Detect icon type
    $iconType = 'default';
    
    if ($txType === 'transfer' || str_contains($catName, 'transfer')) {
        $iconType = 'transfer';
    } elseif (str_contains($catName, 'makan') || str_contains($catName, 'minum') || str_contains($catName, 'restoran') || str_contains($catName, 'kuliner') || $catIcon === 'utensils') {
        $iconType = 'food';
    } elseif (str_contains($catName, 'kopi') || str_contains($catName, 'coffee') || str_contains($catName, 'cafe')) {
        $iconType = 'coffee';
    } elseif (str_contains($catName, 'transpor') || str_contains($catName, 'bensin') || str_contains($catName, 'ojek') || str_contains($catName, 'parkir') || $catIcon === 'car') {
        $iconType = 'transport';
    } elseif (str_contains($catName, 'belanja') || str_contains($catName, 'pasar') || str_contains($catName, 'mall') || str_contains($catName, 'baju') || $catIcon === 'shopping-bag') {
        $iconType = 'shopping';
    } elseif (str_contains($catName, 'hibur') || str_contains($catName, 'nonton') || str_contains($catName, 'bioskop') || str_contains($catName, 'game') || str_contains($catName, 'film') || $catIcon === 'film') {
        $iconType = 'entertainment';
    } elseif (str_contains($catName, 'tagihan') || str_contains($catName, 'listrik') || str_contains($catName, 'air') || str_contains($catName, 'wifi') || str_contains($catName, 'pulsa') || $catIcon === 'receipt') {
        $iconType = 'bills';
    } elseif (str_contains($catName, 'sehat') || str_contains($catName, 'obat') || str_contains($catName, 'dokter') || str_contains($catName, 'rumah sakit') || $catIcon === 'heart-pulse') {
        $iconType = 'health';
    } elseif (str_contains($catName, 'gaji') || str_contains($catName, 'salary') || str_contains($catName, 'upah') || $catIcon === 'banknotes') {
        $iconType = 'salary';
    } elseif (str_contains($catName, 'invest') || str_contains($catName, 'dividen') || str_contains($catName, 'saham') || str_contains($catName, 'reksa') || $catIcon === 'arrow-trending-up') {
        $iconType = 'investment';
    } elseif (str_contains($catName, 'bonus') || str_contains($catName, 'freelance') || str_contains($catName, 'hadiah') || $catIcon === 'sparkles') {
        $iconType = 'bonus';
    } elseif (str_contains($catName, 'utang') || str_contains($catName, 'hutang') || str_contains($catName, 'piutang') || str_contains($catName, 'pinjam')) {
        $iconType = 'debt';
    } elseif (str_contains($catName, 'cukur') || str_contains($catName, 'salon') || str_contains($catName, 'barber')) {
        $iconType = 'scissors';
    } elseif ($txType === 'income') {
        $iconType = 'salary';
    }
@endphp

@switch($iconType)
    @case('transfer')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
        </svg>
        @break

    @case('food')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v6a3.75 3.75 0 007.5 0V3m-3.75 9.75V21" />
        </svg>
        @break

    @case('coffee')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 8h1a3 3 0 013 3 3 3 0 01-3 3h-1m-14 0h14v4a4 4 0 01-4 4H8a4 4 0 01-4-4V6h14v2" />
        </svg>
        @break

    @case('transport')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V3.75m0 3.75H9.75m4.5 0h2.25m-6.75 0V3.75m0 3.75H5.625c-.621 0-1.125.504-1.125 1.125v4.5" />
        </svg>
        @break

    @case('shopping')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25c-.669 0-1.189-.578-1.119-1.243l1.263-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
        </svg>
        @break

    @case('entertainment')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M12 10.875v2.25m0-2.25c0-.621.504-1.125 1.125-1.125h7.5c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-7.5A1.125 1.125 0 0112 13.125z" />
        </svg>
        @break

    @case('bills')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
        </svg>
        @break

    @case('health')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
        </svg>
        @break

    @case('salary')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        @break

    @case('investment')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
        </svg>
        @break

    @case('bonus')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
        </svg>
        @break

    @case('debt')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        @break

    @case('scissors')
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.848 8.25l1.536.887M7.848 8.25a3 3 0 11-5.196-3 3 3 0 015.196 3zm1.536.887a2.165 2.165 0 011.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.078 1.2M9.384 9.137L20.25 15.41M11.462 10.337l2.154 1.244m-2.154-1.244a2.16 2.16 0 00-1.078 1.844c-.005.35-.054.694-.14 1.023m3.372-1.623l5.378-3.105M13.616 11.58l-1.536.887m1.536-.887a3 3 0 115.196 3 3 3 0 01-5.196-3zm-1.536.887L1.875 6.09" />
        </svg>
        @break

    @default
        <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
        </svg>
@endswitch
