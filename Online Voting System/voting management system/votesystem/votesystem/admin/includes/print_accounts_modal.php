<!-- Add -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">

            <form class="form-horizontal" method="POST" action="print_account_add.php">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><b>Add Authorized User</b></h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Full Name</label>

                        <div class="col-sm-9">
                            <input type="text"
                                   class="form-control"
                                   name="fullname"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Username</label>

                        <div class="col-sm-9">
                            <input type="text"
                                   class="form-control"
                                   name="username"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Password</label>

                        <div class="col-sm-9">
                            <input type="text"
                                   class="form-control"
                                   name="password"
                                   required>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-default pull-left"
                            data-dismiss="modal">
                        <i class="fa fa-close"></i> Close
                    </button>

                    <button type="submit"
                            name="add"
                            class="btn btn-primary">
                        <i class="fa fa-save"></i> Save
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>


<!-- Edit -->
<!-- Edit -->
<div class="modal fade" id="edit">
    <div class="modal-dialog">
        <div class="modal-content">

            <form class="form-horizontal" method="POST" action="print_account_edit.php">

                <input type="hidden" class="id" name="id">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><b>Edit Authorized User</b></h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Full Name</label>

                        <div class="col-sm-9">
                            <input type="text"
                                   class="form-control"
                                   id="edit_fullname"
                                   name="fullname"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Username</label>

                        <div class="col-sm-9">
                            <input type="text"
                                   class="form-control"
                                   id="edit_username"
                                   name="username"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Current Password</label>

                        <div class="col-sm-9">
                            <input type="password"
                                   class="form-control"
                                   id="edit_current_password"
                                   name="current_password"
                                   autocomplete="off"
                                   required>
                            <p class="help-block">Enter the current password to confirm changes.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">New Password</label>

                        <div class="col-sm-9">
                            <input type="password"
                                   class="form-control"
                                   id="edit_password"
                                   name="password"
                                   autocomplete="new-password">
                            <p class="help-block">Leave blank to keep the current password.</p>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-default pull-left"
                            data-dismiss="modal">
                        <i class="fa fa-close"></i> Close
                    </button>

                    <button type="submit"
                            name="edit"
                            class="btn btn-success">
                        <i class="fa fa-check"></i> Update
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>


<!-- Delete -->
<div class="modal fade" id="delete">
    <div class="modal-dialog">
        <div class="modal-content">

            <form class="form-horizontal" method="POST" action="print_account_delete.php">

                <input type="hidden" class="id" name="id">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><b>Delete Authorized User</b></h4>
                </div>

                <div class="modal-body">

                    <p class="text-center">
                        Are you sure you want to delete this authorized user?
                    </p>

                    <h3 class="text-center fullname"></h3>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-default pull-left"
                            data-dismiss="modal">
                        <i class="fa fa-close"></i> Cancel
                    </button>

                    <button type="submit"
                            name="delete"
                            class="btn btn-danger">
                        <i class="fa fa-trash"></i> Delete
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>