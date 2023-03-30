<div class="card-body">
    <!-- <input type="hidden" name="id"> -->
    <div class="msg"></div>
    <div class="form-group">
        <label class="control-label">User Type</label>
        <select name="user_type" class="form-select user_type" onchange="get_usertype(this)">
            <option value="" disabled hidden selected></option>
            <?php $user_roles = ['Admin','Faculty']; 
            foreach($user_roles as $data_role){ ?>
                <option value="<?php echo $data_role ?>"><?php echo $data_role ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group">
        <label class="control-label">Faculty ID</label>
        <input type="text" class="form-control faculty_id" name="faculty_id" id="faculty_id">
    </div>
    <div class="form-group">
        <label class="control-label">Name</label>
        <input type="text" class="form-control faculty_name" name="faculty_name" id="faculty_name">
    </div>
    <div class="form-group">
        <label class="control-label">Username</label>
        <input type="text" class="form-control username" name="username" id="username">
    </div>
    <div class="form-group">
        <label class="control-label">Email</label>
        <input type="text" class="form-control email" name="email" id="email">
    </div>
    <div id="add_field"></div>

</div>

<div class="card-footer">
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-sm btn-primary col-sm-4 offset-md-4" type="button" id="add_user_account"> ADD </button>
        </div>
    </div>
</div>