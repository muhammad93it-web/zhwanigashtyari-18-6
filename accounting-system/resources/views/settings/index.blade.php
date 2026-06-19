@extends('layouts.app')

@section('title', 'ڕێکخستنی سیستەم')
@section('page-title', 'ڕێکخستنی سیستەم')
@section('page-subtitle', 'باکئەپ، تیم، و تەنظیماتی سیستەم')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ===== BACKUP DOWNLOAD ===== --}}
    <div class="card p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-800">داگرتنی باکئەپ</h2>
                <p class="text-xs text-slate-500">داگرتنی هەموو داتاکانی سیستەم</p>
            </div>
        </div>

        @if($dbExists)
        <div class="bg-slate-50 rounded-lg p-4 mb-4 space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">قەبارەی داتابەیس</span>
                <span class="font-semibold text-slate-800">{{ $dbSizeMb }} MB</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">کاتی دوایین گۆڕان</span>
                <span class="font-semibold text-slate-800">{{ $dbModified }}</span>
            </div>
        </div>
        <a href="{{ route('settings.backup.download') }}"
           target="_blank" rel="noopener"
           class="btn-primary w-full justify-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
            داگرتنی باکئەپی تەواو
        </a>
        @else
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-amber-700 text-sm">
            فایلی داتابەیس نەدۆزرایەوە. تەنها لە دۆخی SQLite کار دەکات.
        </div>
        @endif
    </div>

    {{-- ===== BACKUP IMPORT ===== --}}
    <div class="card p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-green-600 to-emerald-700 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-800">هێنانی باکئەپ</h2>
                <p class="text-xs text-slate-500">گۆڕینی داتای سیستەم بە باکئەپێکی پێشتر</p>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4 flex items-start gap-2">
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <p class="text-xs text-amber-700 font-medium">ئەگەر باکئەپ هێنبێت، داتای ئێستا دەگۆڕدرێت. ئەم کارە گەڕاندنەوەی نییە.</p>
        </div>

        <form method="POST" action="{{ route('settings.backup.import') }}" enctype="multipart/form-data" id="import-form">
            @csrf
            <div class="mb-4">
                <label class="label" for="backup_file">فایلی باکئەپ (.sqlite)</label>
                <div class="relative">
                    <input type="file" id="backup_file" name="backup_file" accept=".sqlite,.db"
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-slate-300 rounded-lg p-2 cursor-pointer transition-colors"
                           onchange="updateFileName(this)">
                </div>
                <div id="file-name" class="text-xs text-slate-400 mt-1 hidden"></div>
                @error('backup_file')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="button" onclick="confirmImport()" class="btn-warning w-full justify-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                </svg>
                هێنانی باکئەپ
            </button>
        </form>
    </div>

    {{-- ===== TELEGRAM AUTO-DELIVERY LINK ===== --}}
    <div class="card p-6 lg:col-span-2">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">ناردنی خۆکار بۆ تێلێگرام</h2>
                    <p class="text-xs text-slate-500">ناردنی باکئەپ و ڕاپۆرتەکان بە شێوەی خۆکار یان دەستی بۆ تێلێگرام</p>
                </div>
            </div>
            <a href="{{ route('telegram.index') }}" class="btn-primary justify-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/></svg>
                کردنەوەی ڕێکخستنی تێلێگرام
            </a>
        </div>
    </div>

    {{-- ===== COLOR THEME ===== --}}
    <div class="card p-6 lg:col-span-2">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-800">ڕەنگی سیستەم</h2>
                <p class="text-xs text-slate-500">هەڵبژاردنی پالێتی ڕەنگ بۆ هەموو سیستەمەکە</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3" id="palette-grid">
            <!-- Rendered by JS -->
        </div>

        <p class="text-xs text-slate-400 mt-3 text-center">ڕەنگ لە ئامێرەکەتدا پاشکەوت دەبێت — هەموو بەکارهێنەران دەتوانن ڕەنگی خۆیان هەڵبژێرن</p>
    </div>

    {{-- ===== FONT SIZE ===== --}}
    <div class="card p-6 lg:col-span-2">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-green-600 to-emerald-700 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-800">قەبارەی فۆنت</h2>
                <p class="text-xs text-slate-500">گەورە یان بچووک کردنی خوێندنەوەی سیستەم</p>
            </div>
        </div>

        <div class="flex items-center justify-center gap-4">
            <button type="button" onclick="changeFontSize(-1)"
                    class="w-12 h-12 rounded-xl border-2 border-slate-200 hover:border-green-400 hover:bg-green-50 flex items-center justify-center transition-all font-bold text-slate-600 text-lg hover:text-green-700">
                ا-
            </button>

            <div class="flex-1 max-w-xs">
                <div class="flex justify-between text-xs text-slate-400 mb-2">
                    <span>بچووک</span>
                    <span id="font-label" class="font-semibold text-slate-700"></span>
                    <span>گەورە</span>
                </div>
                <input type="range" id="font-slider" min="0" max="8" step="1" value="4"
                       class="w-full h-2 rounded-lg appearance-none cursor-pointer accent-green-600"
                       oninput="applyFontSize(parseInt(this.value)); updateFontLabel()">
            </div>

            <button type="button" onclick="changeFontSize(1)"
                    class="w-12 h-12 rounded-xl border-2 border-slate-200 hover:border-green-400 hover:bg-green-50 flex items-center justify-center transition-all font-bold text-slate-600 text-lg hover:text-green-700">
                ا+
            </button>
        </div>

        <div class="mt-4 p-4 bg-slate-50 rounded-xl text-center">
            <p id="font-preview" class="text-slate-700 font-medium transition-all duration-200">ئەمە نمونەیەکی خوێندنەوەی سیستەمە — سیستەمی ژمێریاری ژوانی گەشتیاری</p>
        </div>

        <div class="flex justify-center mt-3">
            <button type="button" onclick="applyFontSize(4); document.getElementById('font-slider').value=4; updateFontLabel();"
                    class="btn-outline text-xs">
                گەڕانەوەبۆ بنەڕەت
            </button>
        </div>
    </div>

</div>

{{-- Confirm import modal --}}
<div id="confirm-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md animate-slide-in">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">دڵنیابوون لە هێنانی باکئەپ</h3>
        </div>
        <p class="text-sm text-slate-600 mb-6">داتای ئێستای سیستەم بە داتای باکئەپ جێگیر دەبێت. ئایا دڵنیایت؟</p>
        <div class="flex gap-3">
            <button type="button" onclick="document.getElementById('import-form').submit()" class="btn-warning flex-1 justify-center">بەڵێ، هێنان بکە</button>
            <button type="button" onclick="closeModal()" class="btn-outline flex-1 justify-center">نەخێر</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateFileName(input) {
    const nameEl = document.getElementById('file-name');
    if (input.files.length > 0) {
        nameEl.textContent = 'هەڵبژێردراو: ' + input.files[0].name;
        nameEl.classList.remove('hidden');
    }
}

function confirmImport() {
    const file = document.getElementById('backup_file');
    if (!file.files.length) {
        alert('تکایە یەکەم فایلێک هەڵبژێرە.');
        return;
    }
    document.getElementById('confirm-modal').classList.remove('hidden');
    document.getElementById('confirm-modal').classList.add('flex');
}

function closeModal() {
    document.getElementById('confirm-modal').classList.add('hidden');
    document.getElementById('confirm-modal').classList.remove('flex');
}

/* Palette Grid */
const PALETTE_LABELS = {
    green: 'سووری', blue: 'شین', purple: 'مۆر',
    teal: 'فیرۆزەیی', orange: 'نارنجی', rose: 'گوڵناری'
};
const PALETTE_SAMPLES = {
    green: '#16a34a', blue: '#2563eb', purple: '#9333ea',
    teal: '#0d9488', orange: '#ea580c', rose: '#e11d48'
};

function buildPaletteGrid() {
    const grid = document.getElementById('palette-grid');
    const current = localStorage.getItem('jwani_theme') || 'green';
    grid.innerHTML = '';
    Object.entries(PALETTE_SAMPLES).forEach(([key, color]) => {
        const isActive = (key === current);
        const div = document.createElement('button');
        div.type = 'button';
        div.className = 'palette-btn group flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-all duration-200 cursor-pointer ' +
            (isActive ? 'border-current shadow-md scale-105' : 'border-slate-200 hover:border-slate-300 hover:shadow-sm');
        div.style.borderColor = isActive ? color : '';
        div.dataset.key = key;
        div.innerHTML = `
            <div class="w-12 h-12 rounded-full shadow-inner transition-transform duration-200 group-hover:scale-110"
                 style="background: radial-gradient(circle at 35% 35%, ${color}cc, ${color})"></div>
            <span class="text-xs font-semibold text-slate-700">${PALETTE_LABELS[key]}</span>
            ${isActive ? '<span class="text-[10px] text-white px-2 py-0.5 rounded-full font-bold" style="background:'+color+'">✓ چالاک</span>' : ''}
        `;
        div.onclick = () => {
            applyTheme(key);
            buildPaletteGrid();
        };
        grid.appendChild(div);
    });
}

/* Font slider sync */
function updateFontLabel() {
    const FONT_STEPS = [12, 13, 14, 15, 16, 17, 18, 19, 20];
    const slider = document.getElementById('font-slider');
    const idx = parseInt(slider.value, 10);
    document.getElementById('font-label').textContent = FONT_STEPS[idx] + 'px';
}

document.addEventListener('DOMContentLoaded', () => {
    buildPaletteGrid();
    const savedFont = parseInt(localStorage.getItem('jwani_font') || '4', 10);
    document.getElementById('font-slider').value = savedFont;
    updateFontLabel();
});
</script>
@endpush
