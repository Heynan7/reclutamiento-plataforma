<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de validación en español
    |--------------------------------------------------------------------------
    */

    'accepted'             => 'El campo :attribute debe ser aceptado.',
    'active_url'           => 'El campo :attribute no es una URL válida.',
    'after'                => 'El campo :attribute debe ser una fecha posterior a :date.',
    'alpha'                => 'El campo :attribute solo puede contener letras.',
    'alpha_num'            => 'El campo :attribute solo puede contener letras y números.',
    'array'                => 'El campo :attribute debe ser un arreglo.',
    'before'               => 'El campo :attribute debe ser una fecha anterior a :date.',
    'between'              => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'file'    => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
        'string'  => 'El campo :attribute debe tener entre :min y :max caracteres.',
        'array'   => 'El campo :attribute debe tener entre :min y :max elementos.',
    ],
    'boolean'              => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed'            => 'La confirmación de :attribute no coincide.',
    'date'                 => 'El campo :attribute no es una fecha válida.',
    'email'                => 'El campo :attribute debe ser una dirección de correo válida.',
    'unique'               => 'El campo :attribute ya ha sido registrado.', // 👈 Aquí en español
    'required'             => 'El campo :attribute es obligatorio.',

    /*
    |--------------------------------------------------------------------------
    | Personalización de atributos
    |--------------------------------------------------------------------------
    | Aquí puedes traducir los nombres de los campos
    */
    'attributes' => [
        'email'    => 'correo electrónico',
        'password' => 'contraseña',
        'name'     => 'nombre',
    ],

];
