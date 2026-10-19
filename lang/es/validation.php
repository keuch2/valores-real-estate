<?php

return [
    'required' => 'Completá el campo :attribute.',
    'email' => 'Ingresá un email válido.',
    'string' => 'El campo :attribute no es válido.',
    'file' => 'El archivo :attribute no es válido.',
    'mimes' => 'El archivo :attribute debe ser de tipo: :values.',
    'uploaded' => 'No se pudo subir el archivo :attribute.',
    'max' => [
        'string' => 'El campo :attribute no puede superar los :max caracteres.',
        'file' => 'El archivo :attribute no puede superar los :max kilobytes.',
        'numeric' => 'El campo :attribute no puede ser mayor a :max.',
        'array' => 'El campo :attribute no puede tener más de :max elementos.',
    ],
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El archivo :attribute debe tener al menos :min kilobytes.',
    ],
    'unique' => 'El valor de :attribute ya está en uso.',
    'url' => 'Ingresá una URL válida en :attribute.',
    'image' => 'El archivo :attribute debe ser una imagen.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'regex' => 'El formato de :attribute no es válido.',
    'in' => 'El valor seleccionado en :attribute no es válido.',
    'attributes' => [],
];
