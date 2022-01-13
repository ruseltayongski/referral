{{ csrf_field() }}
<fieldset>
    <legend><i class="fa fa-plus"></i> Add ICD</legend>
</fieldset>
<div class="form-group">
    <label>Code:</label>
    <input type="text" class="form-control" id="code_add" name="code" required>
</div>
<div class="form-group">
    <label>Description:</label>
    <textarea rows="2" class="form-control" name="description" required></textarea>
</div>
<div class="form-group">
    <label>Group:</label>
    <input type="text" class="form-control" name="group">
</div>
<div class="form-group">
    <label>Case Rate:</label>
    <input type="text" class="form-control" name="case_rate">
</div>
<div class="form-group">
    <label>Professional Fee:</label>
    <input type="text" class="form-control" name="professional_fee">
</div>
<div class="form-group">
    <label>Health Care Fee:</label>
    <input type="text" class="form-control" name="health_care_fee">
</div>
<div class="form-group">
    <label>Source:</label>
    <input type="text" class="form-control" name="source">
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
    <button type="submit" value="true" name="add_btn" id="add_icd_btn" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Save</button>
</div>
