<div class="form theme-form">
    <!-- General Information Section -->
    <div class="row">
        <div class="col-sm-4">
            <input type="hidden" name="country_id" id="country_id" value="101">
            <div class="mb-3">
                <label>State<span> *</span></label>
                <select class="form-select" name="state_id" id="state_id">
                    <option value="" selected disabled hidden>Select State</option>
                    @foreach ($states as $key => $states)
                        <option value="{{ $key }}"
                            @if (isset($row->id) && old('id', $row->state_id) == $key) @selected(true) @endif>
                            {{ $states }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="mb-3">
                <label>District Name<span> *</span></label>
                <input class="form-control" type="text" name="district_name" value="{{ isset($row->district_name) ? $row->district_name : old('district_name') }}" placeholder="Enter District Name">
                @error('district_name')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="district_status" name="district_status" value="1" 
                    {{ isset($row->district_status) && $row->district_status ==  0? 'checked' : '' }}>
                <label>Disable This District</label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
        <div class="text-center pt-5">
                            <button type="submit" class="btn btn-primary">Save</button>
                 
                        <a href="{{ route('admin.district.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
        </div>
        </div>
    </div>


<script>
    function updatevalue(){
        var checkbox = document.getElementById('district_status');
        if (checkbox.value == 1) {
            checkbox.value = 0; 
        } else {
            checkbox.value = 1; 
        }
    }
</script>
