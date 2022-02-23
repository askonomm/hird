<?php

use Askonomm\Hird\Hird;

test('Validate a correct e-mail address', function () {
    $fields = ['email' => 'asko@bien.ee'];
    $rules = ['email' => 'email'];
    $hird = new Hird($fields, $rules);

    expect($hird->fails())->toBeFalse();
});

test('Validate an incorrect e-mail address', function () {
    $fields = ['email' => 'this-is-not-right'];
    $rules = ['email' => 'email'];
    $hird = new Hird($fields, $rules);

    expect($hird->errors())->toBe([
        'email is not a valid e-mail address.'
    ]);
});

test('Validate a correct length of string', function () {
    $fields = ['string' => 'i-am-fine-as-i-am-long'];
    $rules = ['string' => 'len:8'];
    $hird = new Hird($fields, $rules);

    expect($hird->fails())->toBeFalse();
});

test('Validate an incorrect length of string', function () {
    $fields = ['string' => 'i-am-short'];
    $rules = ['string' => 'len:15'];
    $hird = new Hird($fields, $rules);

    expect($hird->errors())->toBe([
        'string is shorter than the required 15 characters.'
    ]);
});

test('Validate a correct required string', function () {
    $fields = ['string' => 'i-am-required'];
    $rules = ['string' => 'required'];
    $hird = new Hird($fields, $rules);

    expect($hird->fails())->toBeFalse();
});

test('Validate an incorrect required string', function () {
    $fields = [
        'empty-string' => '',
        'null-value' => null,
        'false-value' => false,
    ];

    $rules = [
        'empty-string' => 'required',
        'null-value' => 'required',
        'false-value' => 'required',
    ];

    $hird = new Hird($fields, $rules);

    expect($hird->errors())->toBe([
        'empty-string is required.',
        'null-value is required.',
    ]);
});
