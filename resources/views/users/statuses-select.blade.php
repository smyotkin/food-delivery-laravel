<label for="status" class="form-label fw-bold">Статус</label>

<select class="form-select" id="status" name="status" required>
    <option disabled selected>Ничего не выбрано</option>
    @foreach ($statuses as $key => $status)
        <option value="{{ $key }}" {{ isset($role->status) && $role->status == $key || old('status') == $key ? 'selected' : '' }}>{{ $status['name'] }}</option>
    @endforeach
</select>
