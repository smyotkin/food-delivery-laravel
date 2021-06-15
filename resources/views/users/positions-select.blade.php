<select class="form-select" id="position" name="position_id" required {{ !old('status') ? 'disabled' : '' }}>
    @if(!empty($positions) && $positions->count())
        <option disabled selected>Ничего не выбрано</option>

        @foreach($positions as $position)
            <option value="{{ $position->id }}" {{ isset($role->id) && $role->id == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
        @endforeach
    @else
        <option disabled selected>Ничего не выбрано</option>
    @endif
</select>
