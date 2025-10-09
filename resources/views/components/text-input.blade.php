@props(['disabled' => false])

<input 
    @disabled($disabled) 
    {{ $attributes->merge([
        'class' => 'border border-gray-300 focus:border-gray-800 focus:ring-gray-800 bg-gray-100 text-gray-900 rounded-md shadow-sm'
    ]) }} 
/>
