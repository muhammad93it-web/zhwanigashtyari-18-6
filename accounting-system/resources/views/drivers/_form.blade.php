<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="label">ناو *</label>
        <input type="text" name="name" value="{{ old('name', $driver->name ?? '') }}" class="input-field" required>
        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="label">مۆبایل</label>
        <input type="text" name="phone" value="{{ old('phone', $driver->phone ?? '') }}" class="input-field">
        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="label">ناونیشان</label>
        <input type="text" name="address" value="{{ old('address', $driver->address ?? '') }}" class="input-field">
        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="label">ژمارەی ئۆتۆمبێل</label>
        <input type="text" name="vehicle_number" value="{{ old('vehicle_number', $driver->vehicle_number ?? '') }}" class="input-field">
        @error('vehicle_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="label">جۆری ئۆتۆمبێل</label>
        <input type="text" name="vehicle_type" value="{{ old('vehicle_type', $driver->vehicle_type ?? '') }}" class="input-field" placeholder="نمونە: شاحینە، تیپەر، پیکاب">
        @error('vehicle_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="label">تێبینی</label>
    <textarea name="notes" rows="3" class="input-field">{{ old('notes', $driver->notes ?? '') }}</textarea>
    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<label class="flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $driver->is_active ?? true)) class="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500">
    چالاکە
</label>
