<div>
    <label class="label">ناوی پڕۆژە *</label>
    <input type="text" name="name" value="{{ old('name', $project->name ?? '') }}" class="input-field" required>
    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="label">کڕیار (ئیختیاری)</label>
        <select name="client_id" class="input-field">
            <option value="">— هیچ —</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" @selected(old('client_id', $project->client_id ?? '')==$client->id)>{{ $client->name }}</option>
            @endforeach
        </select>
        @error('client_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label">دۆخ *</label>
        <select name="status" class="input-field" required>
            @foreach(\App\Models\Project::STATUSES as $k => $v)
                <option value="{{ $k }}" @selected(old('status', $project->status ?? 'active')==$k)>{{ $v }}</option>
            @endforeach
        </select>
        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="label">شوێن</label>
        <input type="text" name="location" value="{{ old('location', $project->location ?? '') }}" class="input-field">
        @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label">بودجە</label>
        <input type="number" step="0.01" name="budget" value="{{ old('budget', $project->budget ?? '') }}" class="input-field">
        @error('budget') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="label">تێبینی</label>
    <textarea name="notes" rows="3" class="input-field">{{ old('notes', $project->notes ?? '') }}</textarea>
    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<label class="flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $project->is_active ?? true)) class="w-4 h-4 rounded border-slate-300 text-green-600 focus:ring-green-500">
    چالاکە
</label>
