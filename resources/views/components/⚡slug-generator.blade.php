<?php

use Livewire\Component;

new class extends Component
{
    public string $input = '';
    public string $separator = '-';
    public bool $removeStopWords = false;
    public bool $removeNumbers = false;
    public string $slug = '';
    public bool $copied = false;

    protected array $stopWords = [
        // Articles
        'a', 'an', 'the',
        // Prepositions
        'at', 'for', 'in', 'of', 'on', 'to', 'with', 'by', 'about', 'against',
        'between', 'into', 'through', 'during', 'before', 'after', 'above',
        'below', 'from', 'up', 'down',
        // Conjunctions
        'and', 'or', 'but', 'so', 'yet', 'nor',
        // Pronouns
        'he', 'she', 'it', 'they', 'we', 'you', 'him', 'her', 'them', 'us',
        'i', 'me', 'my', 'his', 'its', 'their', 'this',
        // Common/Weak Verbs
        'is', 'are', 'was', 'were', 'be', 'been', 'being', 'does', 'do', 'did',
        'has', 'have', 'had', 'having', 'can', 'could', 'would', 'should',
        'may', 'might',
        // Other/Filler Words
        'not', 'very', 'such', 'rather', 'using', 'what', 'which', 'why',
        'if', 'that', 'here', 'there',
    ];

    public function updated(): void
    {
        $this->generateSlug();
    }

    public function generateSlug(): void
    {
        $text = $this->input;

        // 1. Convert to lowercase
        $text = mb_strtolower($text);

        // 2. Remove special characters: %, &, ?, !, '
        $text = preg_replace("/[%&?!']/u", '', $text);

        // 3. Remove other non-alphanumeric characters except spaces and hyphens/underscores
        $text = preg_replace('/[^\p{L}\p{N}\s\-_]/u', ' ', (string) $text);

        // 4. Remove stop words if enabled
        if ($this->removeStopWords) {
            $words = preg_split('/\s+/', (string) $text, -1, PREG_SPLIT_NO_EMPTY);
            $words = array_filter($words, fn($word) => !in_array(mb_strtolower((string) $word), $this->stopWords));
            $text = implode(' ', $words);
        }

        // 5. Remove numbers if enabled
        if ($this->removeNumbers) {
            $text = preg_replace('/\d+/', ' ', (string) $text);
        }

        // 6. Replace spaces and existing separators with chosen separator
        $sep = $this->separator;
        $text = preg_replace('/[\s\-_]+/', $sep, trim((string) $text));

        // 7. Remove leading/trailing separators
        $this->slug = trim((string) $text, $sep);
    }
};
?>

<div class="card" wire:init="generateSlug">
    {{-- Input --}}
    <label class="field-label" for="slug-input">Your Text</label>
    <input
        id="slug-input"
        type="text"
        class="text-input"
        wire:model.live="input"
        placeholder="e.g. The Quick Brown Fox & 100 Lazy Dogs!"
        autocomplete="off"
        spellcheck="false"
    >

    {{-- Separator Radio --}}
    <div style="margin-top:1.5rem;">
        <span class="field-label">Separator Character</span>
        <div class="sep-group">
            <label class="sep-label {{ $separator === '-' ? 'active' : '' }}">
                <input type="radio" wire:model.live="separator" value="-">
                <span class="sep-badge">—</span>
                <span class="sep-text">Dash<br><small style="font-weight:400;color:var(--muted)">hello-world</small></span>
            </label>
            <label class="sep-label {{ $separator === '_' ? 'active' : '' }}">
                <input type="radio" wire:model.live="separator" value="_">
                <span class="sep-badge">_</span>
                <span class="sep-text">Underscore<br><small style="font-weight:400;color:var(--muted)">hello_world</small></span>
            </label>
        </div>
    </div>

    {{-- Checkboxes --}}
    <div style="margin-top:1.5rem;">
        <span class="field-label">Options</span>
        <div class="check-group">
            <label class="check-label">
                <input type="checkbox" wire:model.live="removeStopWords">
                <span class="check-box"></span>
                <span class="check-text">Remove stop words
                    <small style="font-weight:400;color:var(--muted);display:block;font-family:var(--mono);font-size:0.7rem;">articles, prepositions, conjunctions, pronouns…</small>
                </span>
            </label>
            <label class="check-label">
                <input type="checkbox" wire:model.live="removeNumbers">
                <span class="check-box"></span>
                <span class="check-text">Remove numbers
                    <small style="font-weight:400;color:var(--muted);display:block;font-family:var(--mono);font-size:0.7rem;">strips all digit sequences</small>
                </span>
            </label>
        </div>
    </div>

    <hr class="divider">

    {{-- Output --}}
    <div class="output-label">
        <label class="field-label" style="margin:0;">Generated Slug</label>
        <button
            class="copy-btn"
            onclick="copySlug()"
            {{ empty($slug) ? 'disabled' : '' }}
            id="copy-btn"
        >
            {{ $copied ? '✓ Copied' : 'Copy' }}
        </button>
    </div>

    <div class="output-box" id="slug-output">
        @if($slug)
            <span class="slug-text">{{ $slug }}</span>
        @else
            <span class="output-empty">your-slug-will-appear-here</span>
        @endif
    </div>

    @if($slug)
        <div class="stats-bar">
            <span class="stat-item">
                <strong>{{ strlen($slug) }}</strong> chars
            </span>
            <span class="stat-item">
                <strong>{{ count(explode($separator, $slug)) }}</strong> words
            </span>
            <span class="stat-item">
                <strong>{{ mb_strtolower($slug) === $slug ? '✓' : '✗' }}</strong> lowercase
            </span>
        </div>
    @endif

    <script>
        function copySlug() {
            const text = document.querySelector('#slug-output .slug-text');
            if (!text) return;
            navigator.clipboard.writeText(text.textContent.trim()).then(() => {
                const btn = document.getElementById('copy-btn');
                btn.textContent = '✓ Copied';
                btn.disabled = true;
                setTimeout(() => {
                    btn.textContent = 'Copy';
                    btn.disabled = false;
                }, 1800);
            });
        }
    </script>
</div>