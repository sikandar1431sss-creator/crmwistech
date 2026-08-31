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
           id="zoho_client_secret"
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
    <p class="text-muted">
        Leave this blank when using a Zoho Self Client. For server-based clients, this must match the redirect URI configured in Zoho.
    </p>
</div>

<hr/>

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
    <label for="zoho_refresh_token" class="control-label">Zoho Refresh Token</label>
    <input type="text"
           id="zoho_refresh_token"
           name="settings[zoho_refresh_token]"
           class="form-control"
           value="<?= get_option('zoho_refresh_token'); ?>"/>
</div>
<hr/>

<div class="form-group">
    <label for="zoho_api_domain" class="control-label">Zoho API Domain</label>
    <input type="text"
           id="zoho_api_domain"
           name="settings[zoho_api_domain]"
           class="form-control"
           value="<?= get_option('zoho_api_domain') != '' ? get_option('zoho_api_domain') : 'https://www.zohoapis.com'; ?>"/>
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
    <label for="zoho_vat_id" class="control-label"><?= _l("settings_zoho_vat_id"); ?> (Standard 5%)</label>
    <input type="text"
           id="zoho_vat_id"
           name="settings[zoho_vat_id]"
           class="form-control"
           value="<?= get_option('zoho_vat_id'); ?>"/>
</div>
<div class="form-group">
    <label for="zoho_zero_vat_id" class="control-label">Zoho Zero Rate Tax ID (0%)</label>
    <input type="text"
           id="zoho_zero_vat_id"
           name="settings[zoho_zero_vat_id]"
           class="form-control"
           value="<?= get_option('zoho_zero_vat_id'); ?>"/>
</div>
