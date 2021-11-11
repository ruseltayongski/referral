<input type="hidden" value="{{ $reason->id }}" name="id">

<div class="form-group">
    <label >Reason Description</label>
    <textarea cols="20" rows="3" class="form-control" name="reason" required>
        <?php echo $reason->reason;?>
    </textarea>
</div>
