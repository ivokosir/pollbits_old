<div class="input-group mb-3 poll-option">
    <div class="input-group-prepend">
        <span class="input-group-text poll-option-id">{{ $index ?? '' }}</span>
    </div>
    <input type="text" class="form-control poll-option-input" value="{{ $value ?? '' }}" placeholder="Poll Option" name="options[]" required>
    @if (!($edit ?? false))
        <div class="input-group-append">
            <button class="btn btn-danger poll-option-delete" tabindex="-1" type="button">Delete</button>
        </div>
    @endif
</div>
