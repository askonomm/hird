<?php

use Askonomm\Bouncer\Bouncer;

test('Validate a correct e-mail address', function () {
    $fields = ['email' => 'asko@bien.ee'];
    $rules = ['email' => 'email'];
    $bouncer = new Bouncer($fields, $rules);

    expect($bouncer->fails())->toBeFalse();
});

test('Validate an incorrect e-mail address', function () {
    $fields = ['email' => 'this-is-not-right'];
    $rules = ['email' => 'email'];
    $bouncer = new Bouncer($fields, $rules);

    expect($bouncer->fails())->toBeTrue();
});

test('Validate a correct length of string', function () {
    $fields = ['string' => 'i-am-fine-as-i-am-long'];
    $rules = ['string' => 'len:8'];
    $bouncer = new Bouncer($fields, $rules);

    expect($bouncer->fails())->toBeFalse();
});

test('Validate an incorrect length of string', function () {
    $fields = ['string' => 'i-am-short'];
    $rules = ['string' => 'len:15'];
    $bouncer = new Bouncer($fields, $rules);

    expect($bouncer->fails())->toBeTrue();
});

test('Validate a correct required string', function () {
    $fields = ['string' => 'i-am-required'];
    $rules = ['string' => 'required'];
    $bouncer = new Bouncer($fields, $rules);

    expect($bouncer->fails())->toBeFalse();
});

test('Validate an incorrect required string', function () {
    $fields = ['string' => ''];
    $rules = ['string' => 'required'];
    $bouncer = new Bouncer($fields, $rules);

    expect($bouncer->fails())->toBeTrue();
});
