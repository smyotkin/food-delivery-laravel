<label for="status" class="form-label fw-bold">Статус</label>

<select class="form-select" id="status" name="status" required>
    <option disabled selected>Ничего не выбрано</option>
    @foreach ($statuses as $slug => $status)
        @if (Auth::user()->isRoot() || in_array($slug, $available_statuses) || (isset($role->status) && $slug == $role->status))
            <option value="{{ $slug }}" {{ old('status') == $slug || (isset($role->status) && $role->status == $slug) ? 'selected' : '' }}>{{ $status['name'] }}</option>
        @endif
    @endforeach
</select>
