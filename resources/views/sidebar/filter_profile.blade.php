<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">Filter Patients</h3>
    </div>
    <form action="{{ url('doctor/patient') }}" method="POST" class="form-submit">
    {{ csrf_field() }}
    <div class="panel-body">
        <div class="form-group">
            <input type="text" placeholder="Search Keyword..." class="form-control" name="keyword" required />
        </div>

        <div class="form-group">
            <select class="form-control province" name="province" onchange="filterSidebar($(this),'muncity')" required>
                <option value="">Select Province</option>
                @foreach($province as $prov)
                    <option value="{{ $prov->id }}">{{ $prov->description }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group muncity_holder">
            <select class="form-control muncity select2" name="muncity" onchange="filterSidebar($(this),'barangay')" required>

            </select>
        </div>

        <div class="form-group muncity_others_holder hide">
            <input type="text" name="others" class="form-control others" placeholder="Enter Address..." />
        </div>

        <div class="form-group barangay_holder">
            <select class="form-control barangay select2" name="brgy" required>
                <option value="">Select Barangay</option>
            </select>
        </div>

        <div class="form-group barangay_others_holder hide">
            <input type="text" name="others" class="form-control others" placeholder="Enter Address..." />
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-block btn-flat btn-success">
                <i class="fa fa-search"></i> Filter
            </button>
            <a href="{{ url('doctor/patient/add') }}" class="btn btn-block btn-flat btn-warning">
                <i class="fa fa-user-plus"></i> Add Patient
            </a>
        </div>
    </div>
    </form>
</div>