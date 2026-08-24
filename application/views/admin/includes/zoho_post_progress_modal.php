<script>
    if (typeof window.openZohoPostProgress === 'undefined') {
        window.ensureZohoPostProgressModal = function () {
            if ($('#zoho_post_progress_modal').length) {
                return;
            }

            $('body').append(
                '<div class="modal fade" id="zoho_post_progress_modal" tabindex="-1" role="dialog" aria-hidden="true">' +
                '  <div class="modal-dialog" role="document">' +
                '    <div class="modal-content">' +
                '      <div class="modal-header">' +
                '        <button type="button" class="close zoho-post-close" data-dismiss="modal" aria-label="Close" disabled><span aria-hidden="true">&times;</span></button>' +
                '        <h4 class="modal-title" id="zoho_post_progress_title">Posting to Zoho</h4>' +
                '      </div>' +
                '      <div class="modal-body">' +
                '        <div class="zoho-post-log"></div>' +
                '      </div>' +
                '      <div class="modal-footer">' +
                '        <button type="button" class="btn btn-default zoho-post-close" data-dismiss="modal" disabled>Close</button>' +
                '      </div>' +
                '    </div>' +
                '  </div>' +
                '</div>'
            );
        };

        window.openZohoPostProgress = function (title) {
            window.ensureZohoPostProgressModal();
            $('#zoho_post_progress_title').text(title || 'Posting to Zoho');
            $('#zoho_post_progress_modal .zoho-post-log').html('');
            $('#zoho_post_progress_modal .zoho-post-close').prop('disabled', true);
            $('#zoho_post_progress_modal').modal({
                backdrop: 'static',
                keyboard: false
            });
        };

        window.addZohoPostProgress = function (type, message) {
            window.ensureZohoPostProgressModal();

            var alertClass = 'alert-info';
            if (type === 'success') {
                alertClass = 'alert-success';
            } else if (type === 'error') {
                alertClass = 'alert-danger';
            } else if (type === 'warning') {
                alertClass = 'alert-warning';
            }

            $('#zoho_post_progress_modal .zoho-post-log').append(
                '<div class="alert ' + alertClass + ' mbottom10">' +
                $('<div>').text(message).html() +
                '</div>'
            );
        };

        window.finishZohoPostProgress = function () {
            $('#zoho_post_progress_modal .zoho-post-close').prop('disabled', false);
        };
    }
</script>
