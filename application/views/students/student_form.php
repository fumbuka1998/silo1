<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Student Information</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="name" class="control-label">Name</label>
                            <input type="text" class="form-control" required name="name" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="age" class="control-label">Age</label>
                            <input type="text" class="form-control" required name="age" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="gender" class="control-label">Gender</label>
                            <select class="form-control" required name="gender" >
                                <option value="MALE">MALE</option>
                                <option value="FEMALE">FEMALE</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btn-sm">Save</button>
            </div>
        </form>
    </div>
</div>