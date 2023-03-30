<div class="card-body">
    <!-- <input type="hidden" name="id"> -->
    <div class="msg"></div>
    <div class="form-group">
        <label class="control-label">Faculty ID</label>
        <input type="text" class="form-control faculty_id" name="faculty_id" id="faculty_id">
    </div>
    <div class="form-group">
        <label class="control-label">Faculty Name</label>
        <input type="text" class="form-control name" name="name" id="name">
    </div>
    <div class="form-group">
        <label class="control-label">Department</label>
        <input type="text" class="form-control department" name="department" id="department">
    </div>
    <div class="form-group">
        <label class="control-label">Email</label>
        <input type="text" class="form-control email" name="email" id="email">
    </div>
    <div class="form-group">
        <label class="control-label">Status</label>
        <select name="status" class="form-select status">
            <option value="" disabled hidden selected>~ Choose Status ~</option>
            <?php
            $stat = ['Full Time', 'Part Time'];
            foreach ($stat as $value) { ?>
                <option value="<?php echo $value ?>"><?php echo $value ?></option>
            <?php } ?>
        </select>
    </div>
</div>

<div class="card-footer">
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-sm btn-primary col-sm-4 offset-md-4" type="button" id="add_faculty"> ADD </button>
        </div>
    </div>
</div>