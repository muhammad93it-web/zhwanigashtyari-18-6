@csrf

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="label">ژمارە *</label>
        <input type="text" name="reference_number" value="{{ old('reference_number', $letter->reference_number) }}" class="input-field" required>
        @error('reference_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label">بەروار *</label>
        <input type="date" name="letter_date" value="{{ old('letter_date', optional($letter->letter_date)->format('Y-m-d')) }}" class="input-field" required>
        @error('letter_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="label">بۆ بەڕێز</label>
    <input type="text" name="recipient" value="{{ old('recipient', $letter->recipient) }}" class="input-field" placeholder="ناوی وەرگر">
    @error('recipient') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="label">بابەت</label>
    <input type="text" name="subject" value="{{ old('subject', $letter->subject) }}" class="input-field">
    @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="label">دەقی نووسراو</label>
    <textarea name="body" rows="12" class="input-field">{{ old('body', $letter->body) }}</textarea>
    @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
