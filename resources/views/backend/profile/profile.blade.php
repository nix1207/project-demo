@extends("backend.layouts.main")
@section("title", "Profile")
@section("css")
    body{
    background: #f5f5f5;
    margin-top:20px;
    }
    .ui-w-80 {
    width: 80px !important;
    height: auto;
    }
    .btn-default {
    border-color: rgba(24,28,33,0.1);
    background: rgba(0,0,0,0);
    color: #4E5155;
    }
    label.btn {
    margin-bottom: 0;
    }
    .btn-outline-primary {
    border-color: #26B4FF;
    background: transparent;
    color: #26B4FF;
    }
    .btn {
    cursor: pointer;
    }
    .text-light {
    color: #babbbc !important;
    }
    .btn-facebook {
    border-color: rgba(0,0,0,0);
    background: #3B5998;
    color: #fff;
    }
    .btn-instagram {
    border-color: rgba(0,0,0,0);
    background: #000;
    color: #fff;
    }
    .card {
    background-clip: padding-box;
    box-shadow: 0 1px 4px rgba(24,28,33,0.012);
    }
    .row-bordered {
    overflow: hidden;
    }
    .account-settings-fileinput {
    position: absolute;
    visibility: hidden;
    width: 1px;
    height: 1px;
    opacity: 0;
    }
    .account-settings-links .list-group-item.active {
    font-weight: bold !important;
    }
    html:not(.dark-style) .account-settings-links .list-group-item.active {
    background: transparent !important;
    }
    .account-settings-multiselect ~ .select2-container {
    width: 100% !important;
    }
    .light-style .account-settings-links .list-group-item {
    padding: 0.85rem 1.5rem;
    border-color: rgba(24, 28, 33, 0.03) !important;
    }
    .light-style .account-settings-links .list-group-item.active {
    color: #4e5155 !important;
    }
    .material-style .account-settings-links .list-group-item {
    padding: 0.85rem 1.5rem;
    border-color: rgba(24, 28, 33, 0.03) !important;
    }
    .material-style .account-settings-links .list-group-item.active {
    color: #4e5155 !important;
    }
    .dark-style .account-settings-links .list-group-item {
    padding: 0.85rem 1.5rem;
    border-color: rgba(255, 255, 255, 0.03) !important;
    }
    .dark-style .account-settings-links .list-group-item.active {
    color: #fff !important;
    }
    .light-style .account-settings-links .list-group-item.active {
    color: #4E5155 !important;
    }
    .light-style .account-settings-links .list-group-item {
    padding: 0.85rem 1.5rem;
    border-color: rgba(24,28,33,0.03) !important;
    }

@endsection
@section("main_content")
    @if(session('success'))
    @endif
    <div class="main_content_iner ">
        <div class="container light-style flex-grow-1 container-p-y">

            <h4 class="font-weight-bold py-3 mb-4">
                Account settings (My profile)
            </h4>
            <form action="{{ route("user.save" , ['id' => $my_account->id]) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="card overflow-hidden">
                    <div class="row no-gutters row-bordered row-border-light">
                        <div class="col-md-3 pt-0">
                            <div class="list-group list-group-flush account-settings-links">
                                <a class="list-group-item list-group-item-action active" data-toggle="list"
                                   href="#account-general">General</a>
                                <a class="list-group-item list-group-item-action" data-toggle="list"
                                   href="#account-change-password">Change password</a>
                                <a class="list-group-item list-group-item-action" data-toggle="list"
                                   href="#account-info">Info</a>
                                <a class="list-group-item list-group-item-action" data-toggle="list"
                                   href="#account-social-links">Social links</a>
                                <a class="list-group-item list-group-item-action" data-toggle="list"
                                   href="#account-connections">Connections</a>
                                <a class="list-group-item list-group-item-action" data-toggle="list"
                                   href="#account-notifications">Notifications</a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="account-general">

                                    <div class="card-body media align-items-center">

                                        @if($my_account->avatar === "")
                                            <img
                                                src="https://anhdep123.com/wp-content/uploads/2021/05/hinh-avatar-trang.jpg"
                                                alt="" class="d-block ui-w-80">
                                        @else
                                            <img src="{{ asset("$my_account->avatar") }}" alt=""
                                                 class="d-block ui-w-80">
                                        @endif
                                        <div class="media-body ml-4">
                                            <label class="btn btn-outline-primary">
                                                Upload new photo
                                                <input type="file" name="avatar" class="account-settings-fileinput">
                                            </label> &nbsp;
                                            <button type="button" class="btn btn-default md-btn-flat">Reset</button>

                                            <div class="text-light small mt-1">Allowed JPG, GIF or PNG. Max size of
                                                800K
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="border-light m-0">

                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label">Full name</label>
                                            <input disabled name="name" type="text" class="form-control mb-1"
                                                   value="{{ $my_account->name }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Só điện thoại</label>
                                            <input disabled name="phone_number" type="number" class="form-control"
                                                   value="{{ $my_account->phone_number }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">E-mail</label>
                                            <input disabled name="email" type="text" class="form-control mb-1"
                                                   value="{{ $my_account->email }}">
                                            <div class="alert alert-warning mt-3">
                                                Your email is not confirmed. Please check your inbox.<br>
                                                <a href="javascript:void(0)">Resend confirmation</a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="account-change-password">
                                    <div class="card-body pb-2">

                                        <div class="form-group">
                                            <label class="form-label">Current password</label>
                                            <input value="{{ old("current_password" , "") }}" name="current_password" type="password" class="form-control">
                                        </div>


                                        <div class="form-group">
                                            <label class="form-label">New password</label>
                                            <input value="{{ old("password" , "") }}" name="password" type="password" class="form-control">
                                        </div>
                                        @if($errors->has("password"))
                                            <h6 class="card-subtitle mb-2 mb-2"><code>{{ $errors->first("password") }}</code></h6>
                                        @endif

                                        <div class="form-group">
                                            <label class="form-label">Repeat new password</label>
                                            <input value="{{ old("password_confirmation" , "") }}" name="password_confirmation" type="password" class="form-control">
                                        </div>
                                        @if($errors->has("password_confirmation"))
                                            <h6 class="card-subtitle mb-2 mb-2"><code>{{ $errors->first("password_confirmation") }}</code></h6>
                                        @endif

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="account-info">
                                    <div class="card-body pb-2">

                                        <div class="form-group">
                                            <label class="form-label">Bio</label>
                                            <textarea class="form-control" rows="5">Nguyễn Mạnh Quân , đẹp trai , vui tính ,  chưa có người yêu
                                            </textarea>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Birthday</label>
                                            <input type="text" class="form-control" value="Aug 7, 2001">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Country</label>
                                            <select class="custom-select">
                                                <option selected>Việt Nam</option>
                                                <option>UK</option>

                                            </select>
                                        </div>


                                    </div>
                                    <hr class="border-light m-0">
                                    <div class="card-body pb-2">

                                        <h6 class="mb-4">Contacts</h6>
                                        <div class="form-group">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" value="0971.399.424">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Website</label>
                                            <input type="text" class="form-control" value=""
                                                   placeholder="Địa chỉ website của bạn...">
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="account-social-links">
                                    <div class="card-body pb-2">

                                        <div class="form-group">
                                            <label class="form-label">Twitter</label>
                                            <input type="text" class="form-control" value="https://twitter.com/user">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Facebook</label>
                                            <input type="text" class="form-control"
                                                   value="https://www.facebook.com/manhquan782001/">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Google+</label>
                                            <input type="text" class="form-control" value="">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">LinkedIn</label>
                                            <input type="text" class="form-control" value="">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Instagram</label>
                                            <input type="text" class="form-control"
                                                   value="https://www.instagram.com/user">
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="account-connections">
                                    <div class="card-body">
                                        <button type="button" class="btn btn-twitter">Connect to
                                            <strong>Twitter</strong></button>
                                    </div>
                                    <hr class="border-light m-0">
                                    <div class="card-body">
                                        <h5 class="mb-2">
                                            <a href="javascript:void(0)" class="float-right text-muted text-tiny"><i
                                                    class="ion ion-md-close"></i> Remove</a>
                                            <i class="ion ion-logo-google text-google"></i>
                                            You are connected to Google:
                                        </h5>
                                        nmaxwell@mail.com
                                    </div>
                                    <hr class="border-light m-0">
                                    <div class="card-body">
                                        <button type="button" class="btn btn-facebook">Connect to
                                            <strong>Facebook</strong></button>
                                    </div>
                                    <hr class="border-light m-0">
                                    <div class="card-body">
                                        <button type="button" class="btn btn-instagram">Connect to
                                            <strong>Instagram</strong></button>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="account-notifications">
                                    <div class="card-body pb-2">

                                        <h6 class="mb-4">Activity</h6>

                                        <div class="form-group">
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher-input" checked="">
                                                <span class="switcher-indicator">
                      <span class="switcher-yes"></span>
                      <span class="switcher-no"></span>
                    </span>
                                                <span class="switcher-label">Email me when someone comments on my article</span>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher-input" checked="">
                                                <span class="switcher-indicator">
                      <span class="switcher-yes"></span>
                      <span class="switcher-no"></span>
                    </span>
                                                <span class="switcher-label">Email me when someone answers on my forum thread</span>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher-input">
                                                <span class="switcher-indicator">
                      <span class="switcher-yes"></span>
                      <span class="switcher-no"></span>
                    </span>
                                                <span class="switcher-label">Email me when someone follows me</span>
                                            </label>
                                        </div>
                                    </div>
                                    <hr class="border-light m-0">
                                    <div class="card-body pb-2">

                                        <h6 class="mb-4">Application</h6>

                                        <div class="form-group">
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher-input" checked="">
                                                <span class="switcher-indicator">
                      <span class="switcher-yes"></span>
                      <span class="switcher-no"></span>
                    </span>
                                                <span class="switcher-label">News and announcements</span>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher-input">
                                                <span class="switcher-indicator">
                      <span class="switcher-yes"></span>
                      <span class="switcher-no"></span>
                    </span>
                                                <span class="switcher-label">Weekly product updates</span>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher-input" checked="">
                                                <span class="switcher-indicator">
                      <span class="switcher-yes"></span>
                      <span class="switcher-no"></span>
                    </span>
                                                <span class="switcher-label">Weekly blog digest</span>
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-primary">Save changes</button>&nbsp;
                    <a href="{{ route("user.index") }}" class="btn btn-default">Cancel</a>
                </div>
            </form>


        </div>
    </div>


@endsection
@section("append_js")

@endsection
