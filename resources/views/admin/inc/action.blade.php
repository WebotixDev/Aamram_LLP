<div class="action-div">
    @isset($data)
        @isset($edit)
            @if (isset($data->system_reserve) ? !$data->system_reserve : true)
                <a href="{{ route($edit, $data) }}" class="edit-icon">
                    <i data-feather="edit"></i>
                @else
                    <a href="javascript:void(0)" class="lock-icon">
                        <i data-feather="lock"></i>
                    </a>
            @endif
        @endisset

        @isset($delete)
            @if (isset($data->system_reserve) ? !$data->system_reserve : true)
                <a href="#confirmationModal{{ $data->id }}" data-bs-toggle="modal" class="delete-svg">
                    <i data-feather="trash-2" class="remove-icon delete-confirmation"></i>
                </a>
                <!-- Remove File Confirmation-->
                <div class="modal fade" id="confirmationModal{{ $data->id }}" tabindex="-1" role="dialog"
                    aria-labelledby="confirmationModalLabel{{ $data->id }}" aria-hidden="true">
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
                                <form action="{{ route($delete, $data->id) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-danger delete spinner-btn"
                                        type="submit">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endisset

    @endisset
    @isset($toggle)
        <label class="switch">
            <input data-route="{{ route($route, $toggle->id) }}" data-id="{{ $toggle->id }}"
                class="form-check-input toggle-status" type="checkbox" name="{{ $name }}"
                value="{{ $value }}" {{ $value ? 'checked' : '' }}
                @if ($toggle->system_reserve) disabled @endif>
            <span class="switch-state"></span>
        </label>
    @endisset

    @isset($assign)
    @if (isset($data->system_reserve) ? !$data->system_reserve : true)
        <a href="{{ route($assign, $data) }}" class="btn btn-primary">
            assign </a>
        @else
            <a href="javascript:void(0)" class="lock-icon">
                <i data-feather="lock"></i>
            </a>
    @endif
@endisset
</div>
