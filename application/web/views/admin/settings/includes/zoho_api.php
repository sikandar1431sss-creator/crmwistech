<a class="btn btn-sm btn-info" href="https://accounts.zoho.com/apiauthtoken/create?SCOPE=ZohoBooks/booksapi" target="_blank" style="
     margin: 5px;float:right;">Get Auth Token</a>
<a class="btn btn-sm btn-info" href="https://accounts.zoho.com/developerconsole" target="_blank" style="float:right;
     margin: 5px;">Create APP </a>


<br/>
<br/>


<div class="form-group">
    <label for="settings[zoho_client_id]" class="control-label"><?= _l("settings_zoho_client_id"); ?></label>
    <input type="text"
           id="zoho_client_id"
           name="settings[zoho_client_id]"
           class="form-control"
           value="<?= get_option('zoho_client_id'); ?>"/>
</div>
<hr/>

<div class="form-group">
    <label for="zoho_client_secret" class="control-label"><?= _l("settings_zoho_client_secret"); ?></label>
    <input type="text"
           id="zoho_client_id"
           name="settings[zoho_client_secret]"
           class="form-control"
           value="<?= get_option('zoho_client_secret'); ?>"/>
</div>
<hr/>

<div class="form-group">
    <label for="zoho_redirect_uri" class="control-label"><?= _l("settings_zoho_redirect_uri"); ?></label>
    <input type="text"
           id="zoho_redirect_uri"
           name="settings[zoho_redirect_uri]"
           class="form-control"
           value="<?= get_option('zoho_redirect_uri'); ?>"/>
</div>

<hr/>

<p>
    <input style="float: right" type="button" class="btn btn-info" id="generate_code" value="Generate Code"/>
</p>

<br/>
<br/>
<div class="form-group">
    <label for="zoho_auth_code" class="control-label"><?= _l("settings_zoho_auth_code"); ?></label>
    <input type="text"
           id="zoho_auth_code"
           name="settings[zoho_auth_code]"
           class="form-control"
           value="<?= ($this->input->get("code") != "") ? $this->input->get("code") : get_option('zoho_auth_code'); ?>"/>
</div>
<hr/>
<div class="form-group">
    <label for="zoho_access_token" class="control-label"><?= _l("settings_zoho_access_token"); ?></label>
    <input type="text"
           id="zoho_access_token"
           name="settings[zoho_access_token]"
           class="form-control"
           value="<?= get_option('zoho_access_token'); ?>"/>
</div>
<hr/>

<div class="form-group">
    <label for="zoho_organization_id" class="control-label"><?= _l("settings_zoho_organization_id"); ?></label>
    <input type="text"
           id="zoho_organization_id"
           name="settings[zoho_organization_id]"
           class="form-control"
           value="<?= get_option('zoho_organization_id'); ?>"/>
</div>
<hr/>

<div class="form-group">
    <label for="zoho_access_token" class="control-label"><?= _l("settings_zoho_vat_id"); ?></label>
    <input type="text"
           id="zoho_vat_id"
           name="settings[zoho_vat_id]"
           class="form-control"
           value="<?= get_option('zoho_vat_id'); ?>"/>
</div>

