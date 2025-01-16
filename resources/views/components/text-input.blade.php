@props(['disabled' => false])

<style>
    .custom-input {
        border: 1px solid #ccc; /* Default gray border */
        border-radius: 6px;
        box-shadow: none;
        transition: all 0.3s ease;
    }

    .custom-input:focus {
        border-color: #01ad9d;
        outline: none;
        box-shadow: 0 0 8px rgba(1, 173, 157, 0.5);
    }
</style>

<input 
    @disabled($disabled) 
    {{ $attributes->merge([
        'class' => 'custom-input'
    ]) }}>

