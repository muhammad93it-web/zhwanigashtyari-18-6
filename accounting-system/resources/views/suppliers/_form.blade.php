<div>
    <label class="label">ناو *</label>
    <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" class="input-field" required>
    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="label">مۆبایل</label>
    <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" class="input-field">
    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="label">تێبینی</label>
    <textarea name="notes" rows="3" class="input-field">{{ old('notes', $supplier->notes ?? '') }}</textarea>
    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<label class="flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $supplier->is_active ?? true)) class="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500">
    چالاکە
</label>
