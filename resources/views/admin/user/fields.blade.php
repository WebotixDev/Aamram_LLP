<div class="form theme-form">
    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>First Name<span> *</span></label>
                <input class="form-control" type="text" name="first_name"
                    value="{{ isset($user->first_name) ? $user->first_name : old('first_name') }}"
                    placeholder="Enter First Name">
                @error('first_name')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Last Name<span> *</span></label>
                <input class="form-control" type="text" name="last_name"
                    value="{{ isset($user->last_name) ? $user->last_name : old('last_name') }}"
                    placeholder="Enter Last Name">
                @error('last_name')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Email <span> *</span></label>
                <input class="form-control" type="email" id="email"
                    value="{{ isset($user->email) ? $user->email : old('email') }}" name="email"
                    placeholder="Enter Email">
                @error('email')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Phone <span> *</span></label>
                <div class="row phone-select">
                    <div class="col-10 ps-0">
                        <input class="form-control" type="number" name="phone"
                            value="{{ isset($user->phone) ? $user->phone : old('phone') }}" placeholder="Enter Phone">
                        @error('phone')
                            <span class="text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!isset($user->id))
        <div class="row">
            <div class="col-sm-6">
                <div class="mb-3">
                    <label>Password <span> *</span></label>
                    <input class="form-control" type="password" id="password" name="password" placeholder="Enter Password"
                        autocomplete="off">
                    @error('password')
                        <span class="text-danger">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label>Confirm Password <span> *</span></label>
                    <input class="form-control" type="password" id="confirm_password" name="confirm_password"
                        placeholder="Enter Confirm Password" autocomplete="off">
                    @error('confirm_password')
                        <span class="text-danger">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>
    @endif

    <div class="row">
       

    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Gender</label>
                <select class="form-select" name="gender">
                    <option value="" selected hidden disabled>Select Gender</option>
                    <option value="male"
                        @if (isset($user->gender)) @if ('male' == $user->gender) selected @endif @endif {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                    <option value="female"
                        @if (isset($user->gender)) @if ('female' == $user->gender) selected @endif @endif {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                </select>
                @error('gender')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-sm-6">
            <div class="mb-3">
                @php
                    $image = $user->getFirstMedia('image');
                @endphp
                <label>Avatar</label>
                <input class="form-control" type="file" name="image">

                @isset($user)
                    <div class="mt-3 comman-image">
                        @if ($image)
                            <img src="{{ $image->getUrl() }}" alt="Image" class="img-thumbnail img-fix" height="50%"
                                width="50%">
                            <div class="dz-preview">
                                <a href="{{ route('admin.user.removeImage', $user?->id) }}" class="dz-remove text-danger"
                                    data-bs-target="#tooltipmodal" data-bs-toggle="modal">Remove</a>
                            </div>
                        @endif
                    </div>

                    <!-- Remove File Confirmation-->
                    <div class="modal fade" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Confirm delete</h4>
                                    <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><b> Are you sure want to delete ?</b></p>
                                    <p>This Item Will Be Deleted Permanently. You Can not Undo This Action.</p>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Close</button>
                                    @if ($user->id)
                                        <a href="{{ route('admin.user.removeImage', $user->id) }}"
                                            class="btn btn-danger">Delete</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endisset

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Role <span> *</span></label>
                <select class="form-select" name="role_id">
                    <option value="" selected disabled hidden>Select Role</option>
                    @foreach ($roles as $key => $role)
                        <option value="{{ $role->id }}"
                            @if (isset($user->roles)) @selected(old('role_id', $user->roles->pluck('id')->first()) == $role->id) @endif>{{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Status</label>
                <select class="form-select" name="status">
                    <option value="1" {{ !($user->status ?? 1) == 0 ? 'selected' : '' }}>{{ __('Active') }}
                    </option>
                    <option value="0" {{ ($user->status ?? 1) == 0 ? 'selected' : '' }}>{{ __('Deactive') }}
                    </option>
                </select>
            </div>
        </div>
    </div>




    <div class="row">
        <div class="col">
        <div class="text-center pt-5">
                            <button type="submit" class="btn btn-primary">Save</button>
                 
                        <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
        </div>
        </div>
    </div>
</div>
