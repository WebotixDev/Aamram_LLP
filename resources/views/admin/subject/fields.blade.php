<div class="form theme-form">
    <!-- General Information Section -->

          <div class="row">


            <div class="form-group col-md-4">
                <label>Expense Category <span class="required" style="color:red;">*</span></label>
                <select class="form-select select2" id="expense_name" name="expense_name" data-placeholder="Select " required>
                    <option value="">Select Expense</option>
                    @foreach(DB::table('expense_category')->get() as $expense)
                        <option value="{{ $expense->id }}"
                            @if(isset($subject->expense_name) && $subject->expense_name == $expense->id)
                                selected
                            @endif
                        >
                            {{ $expense->name }}
                        </option>
                    @endforeach
                </select>
            </div>

                    <div class="form-group col-md-4">
                    <label>Expense Sub-Category <span class="required" style="color:red;">*</span></label>
                    <input type="text" id="subject" name="subject_name" class="form-control" value="{{  isset($subject->subject_name) ? $subject->subject_name : old('subject_name') }}" required>
                            @error('exp_type')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
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
