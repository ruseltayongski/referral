
@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default" style="margin: 0 auto; border-radius: 18px; overflow: hidden; border: 1px solid #d9e2ec; box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12); background: #ffffff;">
                <div class="panel-body" style="padding: 40px 36px;">
                    <div style="text-align: center; margin-bottom: 28px;">
                        <h1 style="margin: 18px 0 12px; font-size: 30px; font-weight: 700; color: #1f2933; line-height: 1.2;">Create a new secure password</h1>
                        <p style="margin: 0 auto; max-width: 640px; color: #52606d; line-height: 1.7; font-size: 15px;">
                            Your account is still using the default password. For your protection, you must create a new password before you can continue.
                        </p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger" style="border-radius: 10px; margin-bottom: 24px; padding: 14px 16px; font-size: 14px;">{{ session('error') }}</div>
                    @endif

                    <form action="{{ url('security/change-password') }}" method="POST" style="max-width: 560px; margin: 0 auto;">
                        {{ csrf_field() }}

                        <div class="form-group" style="margin-bottom: 24px;">
                            <label for="current_password" style="display:block; margin-bottom: 8px; font-weight: 700; color: #1f2933;">Current Password</label>
                            <input type="password" id="current_password" name="current_password" autocomplete="current-password" required
                                   style="width:100%; padding:13px 15px; border-radius:10px; border:1px solid #cbd2d9; font-size:15px; box-sizing:border-box; background:#fff; transition: border-color 0.2s ease, box-shadow 0.2s ease; box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);">
                            <div style="font-size: 13px; color:#616e7c; margin-top:8px; line-height:1.5;">
                                If your account is still on the default password, enter <strong>123</strong>.
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 24px;">
                            <label for="new_password" style="display:block; margin-bottom: 8px; font-weight: 700; color: #1f2933;">New Password</label>
                            <input type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="8" required
                                   style="width:100%; padding:13px 15px; border-radius:10px; border:1px solid #cbd2d9; font-size:15px; box-sizing:border-box; background:#fff; transition: border-color 0.2s ease, box-shadow 0.2s ease; box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);">
                            <div style="font-size: 13px; color:#616e7c; margin-top:8px; line-height:1.5;">
                                Use at least 8 characters, including at least one letter, one number, and one special character.
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 28px;">
                            <label for="confirm_password" style="display:block; margin-bottom: 8px; font-weight: 700; color: #1f2933;">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" minlength="8" required
                                   style="width:100%; padding:13px 15px; border-radius:10px; border:1px solid #cbd2d9; font-size:15px; box-sizing:border-box; background:#fff; transition: border-color 0.2s ease, box-shadow 0.2s ease; box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);">
                        </div>

                        <div style="text-align:center; margin-top: 10px;">
                            <button type="submit" style="width:100%; max-width: 320px; border:0; border-radius:10px; background: linear-gradient(135deg, #1e7f4c 0%, #0f8b4d 100%); color:#fff; font-size:16px; font-weight:700; padding:14px 18px; cursor:pointer; box-shadow: 0 12px 24px rgba(30, 127, 76, 0.2); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                Update Password and Continue
                            </button>
                        </div>
                    </form>

                    <div style="text-align:center; margin-top: 20px; color:#52606d; font-size:13px;">
                        Your session will remain active after the password update is complete.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

