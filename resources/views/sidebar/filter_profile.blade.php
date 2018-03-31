<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">Filter Patients</h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <input type="text" placeholder="Search Keyword..." class="form-control" />
        </div>
        <div class="form-group">
            <select class="form-control muncity filter_muncity">
                <option>Select Municipal/City...</option>
                @foreach($muncity as $m)
                    <option value="{{ $m->id }}">{{ $m->description }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <select class="form-control barangay">
                <option>Select Barangay...</option>
            </select>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-block btn-flat btn-success btn-success">
                <i class="fa fa-search"></i> Filter
            </button>
        </div>
    </div>
</div>