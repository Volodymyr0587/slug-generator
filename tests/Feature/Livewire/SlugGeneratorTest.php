<?php

use Livewire\Livewire;

// --- 1. Initial state ---
it('renders the slug generator page and sets the correct initial state', function () {
    Livewire::test('slug-generator')
        ->assertStatus(200)
        ->assertSeeHtml('wire:model.live="input"')
        ->assertSet('slug', '')
        ->assertSet('separator', '-')
        ->assertSet('removeStopWords', false)
        ->assertSet('removeNumbers', false);
});

// --- 2. Basic generation ---
it('generates a correct slug from simple input', function (string $input, string $expected) {
    Livewire::test('slug-generator')
        ->set('input', $input)
        ->assertSee('slug', $expected);
})->with([
    'single world' => ['hello', 'hello'],
    'two words' => ['hello world', 'hello-world'],
    'three words' => ['foo bar baz', 'foo-bar-baz'],
]);

// --- 3. Lowercase conversion ---
it('always converts input to lowercase', function (string $input, string $expected) {
    Livewire::test('slug-generator')
        ->set('input', $input)
        ->assertSee('slug', $expected);
})->with([
    'all caps' => ['HELLO', 'hello'],
    'mixed case' => ['Hello World', 'hello-world'],
    'camelCase' => ['helloWorld', 'helloworld'],
]);

// --- 4. Special characters ---
it('removes special character', function (string $input, string $expected) {
    Livewire::test('slug-generator')
        ->set('input', $input)
        ->assertSee('slug', $expected);
})->with([
    'percent sign' => ['hello%world', 'hello-world'],
    'ampersand' => ['foo & bar', 'foo-bar'],
    'question mark' => ['What?', 'what'],
    'exclamation' => ['hello!', 'hello'],
    'apostrophe' => ["don't", 'dont'],
    'multiple mixed' => ["it's 50% off!", 'its-50-off'],
]);

// --- 5. Separators ---
it('uses dash as default separator', function () {
    Livewire::test('slug-generator')
        ->set('input', 'hello world')
        ->assertSet('separator', '-')
        ->assertSet('slug', 'hello-world');
});

it('uses underscore separator when selected', function () {
    Livewire::test('slug-generator')
        ->set('input', 'hello world')
        ->set('separator', '_')
        ->assertSet('slug', 'hello_world');
});

it('recalculates slug when separator changes', function () {
    Livewire::test('slug-generator')
        ->set('input', 'hello world')
        ->assertSet('slug', 'hello-world')
        ->set('separator', '_')
        ->assertSet('slug', 'hello_world');
});

// --- 6. Stop words ---
it('does not remove stop words by default', function () {
    Livewire::test('slug-generator')
        ->set('input', 'the quick brown fox')
        ->assertSet('slug', 'the-quick-brown-fox');
});

it('removes stop words when option is enabled', function (string $input, string $expected) {
    Livewire::test('slug-generator')
        ->set('removeStopWords', true)
        ->set('input', $input)
        ->assertSet('slug', $expected);
})->with([
    'article at start' => ['the quick brown fox', 'quick-brown-fox'],
    'article uppercase' => ['The Quick Brown Fox', 'quick-brown-fox'],
    'preposition' => ['fox in the box', 'fox-box'],
    'conjunction' => ['cats and dogs',  'cats-dogs'],
    'pronoun' => ['he said she said', 'said-said'],
    'weak verb' => ['this is very good', 'good'],
    'multiple categories' => ['the fox and a cat are here', 'fox-cat'],
    'no stop words at all' => ['quick brown fox', 'quick-brown-fox'],
]);

it('stop word removal is case insensitive', function (string $input, string $expected) {
    Livewire::test('slug-generator')
        ->set('removeStopWords', true)
        ->set('input', $input)
        ->assertSet('slug', $expected);
})->with([
    'uppercase article' => ['THE fox', 'fox'],
    'mixed case article' => ['The fox', 'fox'],
    'uppercase pronoun' => ['HE runs', 'runs'],
]);

it('re-generates slug when stop words option is toggled', function () {
    Livewire::test('slug-generator')
        ->set('input', 'the quick fox')
        ->assertSet('slug', 'the-quick-fox')
        ->set('removeStopWords', true)
        ->assertSet('slug', 'quick-fox')
        ->set('removeStopWords', false)
        ->assertSet('slug', 'the-quick-fox');
});

// --- 7. Remove Numbers ---
it('does not remove numbers by default', function () {
    Livewire::test('slug-generator')
        ->set('input', 'top 10 movies of 2024')
        ->assertSet('slug', 'top-10-movies-of-2024');
});

it('removes numbers when option is enabled', function (string $input, string $expected) {
    Livewire::test('slug-generator')
        ->set('removeNumbers', true)
        ->set('input', $input)
        ->assertSet('slug', $expected);
})->with([
    'number between words' => ['top 10 movies',      'top-movies'],
    'number at start' => ['3 blind mice',        'blind-mice'],
    'number at end' => ['world war 2',         'world-war'],
    'multiple numbers' => ['1 fox 2 cats 3 dogs', 'fox-cats-dogs'],
    'number only' => ['12345',               ''],
]);

it('removes numbers and stop words together', function () {
    Livewire::test('slug-generator')
        ->set('removeNumbers', true)
        ->set('removeStopWords', true)
        ->set('input', 'the 3 best movies of 2024')
        ->assertSet('slug', 'best-movies');
});

// --- 8. Edge Cases ---
it('returns empty slug for empty input', function () {
    Livewire::test('slug-generator')
        ->set('input', '')
        ->assertSet('slug', '');
});

it('returns empty slug for whitespace only input', function () {
    Livewire::test('slug-generator')
        ->set('input', '     ')
        ->assertSet('slug', '');
});

it('returns empty slug for special characters only', function () {
    Livewire::test('slug-generator')
        ->set('input', '!!! ??? %%% &&&')
        ->assertSet('slug', '');
});

it('returns empty slug when all words are stop words', function () {
    Livewire::test('slug-generator')
        ->set('removeStopWords', true)
        ->set('input', 'the and or but')
        ->assertSet('slug', '');
});

it('does not produce leading or trailing separators', function (string $input) {
    Livewire::test('slug-generator')
        ->set('input', $input)
        ->assertSet('slug', fn ($slug) => ! str_starts_with((string) $slug, '-')
                                       && ! str_ends_with((string) $slug, '-'));
})->with([
    ['  hello world  '],
    ['---hello---'],
    ['  !!! hello !!!  '],
]);

it('handles very long input without errors', function () {
    $input = str_repeat('hello world ', 500);

    Livewire::test('slug-generator')
        ->set('input', $input)
        ->assertSet('slug', fn ($slug) => (string) $slug !== '');
});
