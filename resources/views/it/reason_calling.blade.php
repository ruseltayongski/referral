@if($reason == 'offline')
    <div style="padding-right: 3%;padding-left: 3%;">
        <table class="table">
            <tr>
                <td style="width: 50%">
                    <small class="text-green">
                        Reason
                    </small>
                    <select class="form-control select2" multiple="multiple">
                        <option>Unstable Internet Connection</option>
                        <option>No Point Person </option>
                        <option>Facility is under maintenance</option>
                        <option>Lack of Manpower</option>
                        <option>No Internet Connection</option>
                        <option>Lack of knowledge on how to use the system</option>
                        <option>Lack of Computer Equipment</option>
                        <option>Requesting for retraining</option>
                        <option>Limited Doctor on duty</option>
                        <option>No one answer, phone just ringing</option>
                        <option>Phone no. cannot be reach</option>
                        <option>Facility is under renovation</option>
                        <option>Hang-up call</option>
                        <option>No trained personnel</option>
                        <option>No User account</option>
                        <option>No idea about CVeHRS</option>
                        <option>Log in only if they have referral</option>
                    </select>
                </td>
                <td>
                    <small class="text-green">
                        Others
                    </small>
                    <input type="text" class="form-control">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <small class="text-green">
                        Action Taken:
                    </small>
                    <textarea name="reason_notes" id="" rows="5" class="form-control"></textarea>
                </td>
            </tr>
        </table>
    </div>
@elseif($reason == 'walkin')
    <div style="padding-right: 3%;padding-left: 3%;">
        @foreach(\Illuminate\Support\Facades\Session::get('walkin_action') as $action)
            <?php
            $remark_by = \App\User::find($action->remark_by);
            ?>
            <p class="message">
                <span class="text-green" style="font-size: 10pt;">
                    {{ $remark_by->fname }} {{ $remark_by->lname }}
                </span>
                <small class="text-warning">({{ date('H:i',strtotime($action->created_at)) }})</small><br>
                <small style="margin-left: 2%">
                {{ $action->remarks }}
            </small>
            </p>
        @endforeach
    </div>
    <table class="table">
        <tbody id="walkin_action_body">
            <tr>
                <td width='10%'>
                    <small class='pull-right text-green'>
                        Action Taken:
                    </small>
                </td>
                <td>
                    <textarea name='reason_notes' id='' rows='5' class='form-control'></textarea>
                </td>
                <td width='5%'>
                </td>
            </tr>
        </tbody>
    </table>
    <button class="btn btn-sm btn-primary pull-right" type="button" style="margin-bottom: 3%" onclick="addWalkinAction();"><i class="fa fa-plus"></i> Add more action</button>
@else
    <table class="table">
        <tr>
            <td width="10%">
                <small class="pull-right text-green">
                    Notes:
                </small>
            </td>
            <td>
                <textarea name="reason_notes" id="" rows="5" class="form-control"></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <small class="pull-right text-green">
                    Action Taken:
                </small>
            </td>
            <td>
                <textarea name="reason_notes" id="" rows="5" class="form-control"></textarea>
            </td>
        </tr>
    </table>
@endif
<script>
    $(".select2").select2({ width: '100%' });
    function addWalkinAction(){
        $("#walkin_action_body").append("<tr>\n" +
            "            <td width='10%'>\n" +
            "                <small class='pull-right text-green'>\n" +
            "                    Action Taken:\n" +
            "                </small>\n" +
            "            </td>\n" +
            "            <td>\n" +
            "                <textarea name='reason_notes' id='' rows='5' class='form-control'></textarea>\n" +
            "            </td>\n" +
            "            <td width='5%'>\n" +
            "                <button class='btn btn-sm btn-danger pull-right' type='button' onclick='removeWalkinAction($(this))' style='margin-bottom: 2%'><i class='fa fa-times'></i> Remove this action</button>\n" +
            "            </td>\n" +
            "        </tr>");
    }

    function removeWalkinAction(data){
        data.parent().parent().remove();
    }

</script>