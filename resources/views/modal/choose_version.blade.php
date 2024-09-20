<style>
    @media only screen and (max-width: 720px) {
        #pregnant-content {
            width: 100%;
        }
    }
</style>

<div class="modal fade" role="dialog" id="pregnantchooseVersionModal" style="text-align: center">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" id="version-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
                <i class="fa fa-user-secret"></i> SELECT OPTION
            </div>
            <div class="modal-body">
                <button  data-target="#pregnantFormModal" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-warning col-sm-6">
                <img src="{{ url('resources/img/forms_icon.png') }}" width="100" />
                    <br />
                    Version 1
                </button>
                <button data-target="#revisedpregnantFormModal" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-info col-sm-6">
                <img src="{{ url('resources/img/forms_ver2_icon.png') }}" width="100" />
                    <br />
                    Version 2
                </button>
            </div>
            <div class="modal-footer"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="nonPregnantChooseVersionModal" style="text-align: center">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" id="version-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
                <i class="fa fa-user-secret"></i> SELECT OPTION
            </div>
            <div class="modal-body">
            <button  data-target="#normalFormModal" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-warning col-sm-6" onclick="setClinicalFormTile('normal')">
                    <img src="{{ url('resources/img/forms_icon.png') }}" width="100" />
                    <br />
                    Version 1
                </button>
                <button data-target="#revisednormalFormModal" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-info col-sm-6" onclick="setClinicalFormTile('normal')">
                <img src="{{ url('resources/img/forms_ver2_icon.png') }}" width="100" />
                    <br />
                    Version 2
                </button>
            </div>
            <div class="modal-footer"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->