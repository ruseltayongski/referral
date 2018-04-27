<?php
    $search_referral = Session::get('search_referral');
?>
<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">Filter Results</h3>
    </div>
    <div class="panel-body">
        <form action="{{ url('doctor/referral/') }}" method="POST">
            {{ csrf_field() }}
            <div class="form-group">
                <input type="text" placeholder="Enter fullname or lastname..." class="form-control" value="{{ $search_referral['keyword'] }}" name="keyword"/>
            </div>
            <div class="form-group">
                <select class="form-control" name="option">
                    <option value="">Select All</option>
                    <option {{ ($search_referral['option']=='referred') ? 'selected': '' }} value="referred">New Referral</option>
                    <option {{ ($search_referral['option']=='accepted') ? 'selected': '' }} value="accepted">Accepted</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-block btn-flat btn-success">
                    <i class="fa fa-filter"></i> Filter Result
                </button>
            </div>
        </form>
    </div>
</div>