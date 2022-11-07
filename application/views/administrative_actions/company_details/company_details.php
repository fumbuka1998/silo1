
    <div class="box">
        <div class="box-header">

        </div>
        <div class="box-body">
            <?php
                $company_details = get_company_details();
            ?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-horizontal">

                        <div class="form-group col-md-6 col-sm-12">
                            <label  class="col-sm-3 control-label">Company Name:<span style="color: red">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="company_name" class="form-control " value="<?= $company_details ? $company_details->company_name : '' ?>">
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-sm-12">
                            <label  class="col-sm-3 control-label">Telephone:</label>
                            <div class="col-sm-9">
                                <input type="text" name="telephone" class="form-control " value="<?= $company_details ? $company_details->telephone : ''  ?>">
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-sm-12">
                            <label  class="col-sm-3 control-label">Mobile:</label>
                            <div class="col-sm-9">
                                <input type="text" name="mobile" class="form-control " value="<?=  $company_details ? $company_details->mobile : ''  ?>">
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-sm-12">
                            <label  class="col-sm-3 control-label">Fax:</label>
                            <div class="col-sm-9">
                                <input type="text" name="fax" class="form-control " value="<?=  $company_details ? $company_details->fax : ''  ?>">
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-sm-12">
                            <label  class="col-sm-3 control-label">Email: <span style="color: red">*</span></label>
                            <div class="col-sm-9">
                                <input type="email" name="email" class="form-control " value="<?=  $company_details ? $company_details->email : ''  ?>">
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-sm-12">
                            <label  class="col-sm-3 control-label">Website:</label>
                            <div class="col-sm-9">
                                <input type="text" name="website" class="form-control " value="<?=  $company_details ? $company_details->website : ''  ?>">
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-sm-12">
                            <label  class="col-sm-3 control-label">Address:<span style="color: red">*</span></label>
                            <div class="col-sm-9">
                                <textarea rows="3" name="address" class="form-control "><?=  $company_details ? br2nl($company_details->address) : ''  ?></textarea>
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-sm-12">
                            <label  class="col-sm-3 control-label">TIN :</label>
                            <div class="col-sm-9">
                                <input type="text" name="tin" class="form-control " value="<?= $company_details ? $company_details->tin : ''  ?>">
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-sm-12">
                            <label  class="col-sm-3 control-label">VRN :</label>
                            <div class="col-sm-9">
                                <input type="text" name="vrn" class="form-control " value="<?=  $company_details ? $company_details->vrn : ''  ?>">
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-sm-12">
                            <label  class="col-sm-3 control-label">Tagline :</label>
                            <div class="col-sm-9">
                                <textarea name="tagline" rows="2" class="form-control "><?=  $company_details ? $company_details->tagline : ''  ?></textarea>
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-sm-12">
                            <label  class="col-sm-3 control-label">Company Logo :</label>
                            <div class="col-sm-9">
                                <input type="file" name="company_logo" class="form-control ">
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <label  class="col-sm-3 control-label">Accent Color :</label>
                            <div class="col-sm-9">
                                <input type="color" name="corporate_color" class="form-control" value="<?= $company_details ? $company_details->corporate_color : ''   ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <button type="button" class="btn btn-sm btn-default pull-right save_company_details">Save Company Details</button>
        </div>
    </div>