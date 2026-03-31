# Slug Generator — Laravel 13 + Livewire 4

A real-time slug generator with live preview, built with Laravel 13 and Livewire 4.

## Features

- ⚡ **Live preview** — slug updates as you type (no page reload)
- **Separator choice** — dash (`-`) or underscore (`_`)
- **Remove stop words** — strips articles, prepositions, conjunctions, pronouns, weak verbs, filler words
- **Remove numbers** — strips all digit sequences
- **Auto lowercase** — always applied
- **Special character removal** — strips `%`, `&`, `?`, `!`, `'`
- **Copy to clipboard** button
- Stats bar: character count, word count, lowercase confirmation

## ⚙️ Installation

### Prerequisites

- PHP >= 8.2
- Composer
- Laravel
- Node.js & npm

### Steps

1. 🧬 Clone the repository

    `git clone https://github.com/Volodymyr0587/slug-generator`

    `cd slug-generator`

2. 📦 Install dependencies

    `composer install`

    `npm install`

3. 📝 Set up the environment

    `cp .env.example .env`

    `php artisan key:generate`

4. 🗄️ Database setup

    Using SQLite for simplicity. Update your .env file accordingly:

    `DB_CONNECTION=sqlite`

5. 🚀 Serve the Application

    You can start the application in two ways:
    - Option 1 — Run each service manually:

        `npm run dev`

        `php artisan serve`

    - Option 2 — Use a single Composer command:

        `composer run dev`

        This command will automatically start the Vite dev server and prepare the app for local development.

Visit [http://localhost:8000](http://localhost:8000)

---

## Project Structure

```
resources/
  views/
    components/
      slug-generator.blade.php ← Single-file component (view and logic)
    layouts/
      app.blade.php            ← Main HTML layout

routes/
  web.php                      ← Single route → slug-generator component

tests/
  Feature/
    Livewire/
      SlugGeneratorTest.php    ← All tests for SlugGenerator
```

## How It Works

The `slug-generator` Livewire component reacts to every property change via `wire:model.live`. The `updated()` hook triggers `generateSlug()` which:

1. Converts input to lowercase (`mb_strtolower`)
2. Removes special chars: `%`, `&`, `?`, `!`, `'`
3. Removes non-alphanumeric characters (Unicode-safe via `\p{L}\p{N}`)
4. Optionally strips stop words (word-boundary matching)
5. Optionally strips number sequences
6. Collapses whitespace/separators into chosen separator
7. Trims leading/trailing separators

## Stop Words Covered

| Category          | Words                                                                                                                          |
| ----------------- | ------------------------------------------------------------------------------------------------------------------------------ |
| Articles          | a, an, the                                                                                                                     |
| Prepositions      | at, for, in, of, on, to, with, by, about, against, between, into, through, during, before, after, above, below, from, up, down |
| Conjunctions      | and, or, but, so, yet, nor                                                                                                     |
| Pronouns          | he, she, it, they, we, you, him, her, them, us, i, me, my, his, its, their, this                                               |
| Common/Weak Verbs | is, are, was, were, be, been, being, does, do, did, has, have, had, having, can, could, would, should, may, might              |
| Filler Words      | not, very, such, rather, using, what, which, why, if, that                                                                     |

---

## 📜 License

Personal tool. Use, modify, and adapt freely.
