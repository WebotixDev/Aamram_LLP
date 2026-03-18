<div class="form theme-form">
    <!-- General Information Section -->

          <div class="row">

            <div class="form-group col-md-4">
                <label>Season <span class="required" style="color:red;">*</span></label>
                <select class="form-select select2" id="season" name="season" data-placeholder="Select" required>
                    <option value="">Select Year</option>
                    @php
                        $currentYear = date('Y');
                    @endphp
                    @for ($i = 0; $i <= 4; $i++)
                        @php $year = $currentYear + $i; @endphp
                        <option value="{{ $year }}"  @if(isset($Season->season) && $Season->season == $year)
                                selected
                            @endif
                        >
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>


                    <div class="row">
                        <div class="col">
                        <div class="text-center pt-5">
                                            <button type="submit" class="btn btn-primary">Save</button>

                                        <a href="{{ route('admin.subject.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                        </div>
                        </div>
                    </div>
          </div>
          </div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
