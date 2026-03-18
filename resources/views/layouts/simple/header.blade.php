     <!-- Bootstrap CSS -->
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}


     <!-- Page Header Start-->
      <div class="page-header">
          <div class="header-wrapper row m-0">
              <form class="form-inline search-full col" action="#" method="get">
                  <div class="form-group w-100">
                      <div class="Typeahead Typeahead--twitterUsers">
                          <div class="u-posRelative">
                              <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text"
                                  placeholder="Search Riho .." name="q" title="" autofocus>
                              <div class="spinner-border Typeahead-spinner" role="status"><span
                                      class="sr-only">Loading... </span></div><i class="close-search"
                                  data-feather="x"></i>
                          </div>
                          <div class="Typeahead-menu"> </div>
                      </div>
                  </div>
              </form>
              <div class="header-logo-wrapper col-auto p-0">
                  <div class="logo-wrapper"> <a href="{{ route('admin.dashboard') }}"><img class="img-fluid for-light"
                              src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="logo-light"><img
                              class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo.png') }}"
                              alt="logo-dark"></a></div>
                  <div class="toggle-sidebar"> <i class="status_toggle middle sidebar-toggle"
                          data-feather="align-center"></i></div>
              </div>
              <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
                  <div> <a class="toggle-sidebar" href="#"> <i class="iconly-Category icli"> </i></a>
                      <div class="d-flex align-items-center gap-2 ">
                          <h4 class="f-w-600">Welcome {{ ucfirst(auth()?->user()?->first_name) }} </h4><img class="mt-0"
                              src="{{ asset('assets/images/hand.gif') }}" alt="hand-gif">
                       <button type="button" class="btn btn-primary btn-sm" id="openSeasonModal">
                        Season
                    </button>

<h3>
                    @php
    $season = session('selected_season');

        echo $season;
@endphp

</h3>
                      </div>


                  </div>

              </div>


              <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
                  <ul class="nav-menus">

                      <li class="profile-nav onhover-dropdown">
                          <div class="media profile-media"><img class="b-r-10">
                                  <!-- src="{{ asset('assets/images/dashboard/profile.png') }}" alt="" -->
                              <div class="media-body d-xxl-block d-none box-col-none">
                                  <div class="d-flex align-items-center gap-2"> <span>{{ ucfirst(auth()?->user()?->first_name) }} </span><i
                                          class="middle fa fa-angle-down"> </i></div>
                                  <p class="mb-0 font-roboto"></p>
                              </div>
                          </div>
                          <ul class="profile-dropdown onhover-show-div">
                            @can('profile.edit')
                            <li><a href="{{ route('admin.user.edit-profile',auth()->user()->role->name) }}"><i data-feather="user"></i><span>My Profile</span></a>
                            </li>
                            @endcan
                              {{-- <li><a href=""><i data-feather="mail"></i><span>Inbox</span></a></li>
                              <li> <a href="{{ route('admin.edit_profile') }}"> <i
                                          data-feather="settings"></i><span>Settings</span></a></li> --}}
                              <li>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-pill btn-outline-primary btn-sm">
                                    Log Out
                                </a>
                                <form action="{{route('logout')}}" method="POST" class="d-none" id="logout-form">
                                    @csrf
                                </form>
                              </li>
                          </ul>
                      </li>
                      <li>
                          <div class="mode"><i class="moon" data-feather="moon"> </i></div>
                      </li>
                  </ul>
              </div>
              <script class="result-template" type="text/x-handlebars-template">
            <div class="ProfileCard u-cf">
            <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
            <div class="ProfileCard-details">
            <div class="ProfileCard-realName">name</div>
            </div>
            </div>
          </script>
              <script class="empty-template" type="text/x-handlebars-template"><div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div></script>
          </div>
      </div>


      <!-- Season Modal -->
<div class="modal fade" id="seasonModal" tabindex="-1" aria-labelledby="seasonModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="seasonModalLabel">Select Season</h5>
                </div>
                <div class="modal-body">
                    <label>Season <span class="required" style="color:red;">*</span></label>
                    <select class="form-select select2" id="season" name="season" data-placeholder="Select" required>
                        <option value="">Select Year</option>
                        @foreach(DB::table('season')->get() as $season)
                            <option value="{{ $season->season }}">{{ $season->season }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="setSeason" data-bs-dismiss="modal">Submit</button>
                </div>
                </div>
            </div>
            </div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var seasonModal = new bootstrap.Modal(document.getElementById('seasonModal'));

        @if(session('show_season_popup'))
            seasonModal.show();
        @php
            session()->forget('show_season_popup'); 
        @endphp
        @endif

        // ✅ Button click open
        document.getElementById('openSeasonModal')?.addEventListener('click', function () {
            seasonModal.show();
        });
    });
</script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
<script>
    document.getElementById('setSeason').addEventListener('click', function () {
        var season = document.getElementById('season').value;

      //  alert(season);
        if (!season) {
            alert('Please select a season.');
            return;
        }

        fetch("{{ route('admin.set.season.session') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ season: season })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Season set successfully!');
                var modal = bootstrap.Modal.getInstance(document.getElementById('seasonModal'));
                modal.hide();
                window.location.reload();

            }
        });
    });
</script>
