<?php
// debug($results);
use Cake\Core\Configure;

echo $this->HtmlCustomSite->boxTitle(['title' => __("Email"), 'subtitle' => 'Sollecito pagamento alla richiesta di pagamento num°'.$request_payment->num]);

echo '<section class="content">';

if(count($request_payment->summary_payments)==0) {
    echo '<div class="row">';
    echo '<div class="col-md-12">';
    echo $this->element('msg', ['msg' => "Per la richiesta di pagamento num° $request_payment->num non ci sono gasisti nello stato SOLLECITO a cui inviare la email"]);
    echo '</div>';
    echo '</div>'; // row
}
else {
    echo '<div class="row">';
    echo '<div class="col-md-12">';
    echo $this->element('msg', ['msg' => "Elenco dei gasisti in stato SOLLECITO a cui inviare la mail"]);
    echo '</div>';
    echo '</div>'; // row

    echo '<div class="row">';
    echo '<div class="col-md-12">';
    echo '<div class="box box-primary">';
    echo '<div class="box-header with-border">';
    echo '<h3 class="box-title">'.__('List').' ('.count($request_payment->summary_payments).')</h3>';
    echo '</div>';
    echo $this->Form->create(null, ['role' => 'form', 'id' => 'frm']);
    echo $this->Form->hidden('request_payment_id', ['value' => $request_payment->id]);
    echo '<fieldset>';
    echo '<legend></legend>';
    echo '<div class="box-body">';


    echo '<div class="row">';
    echo '<div class="col-md-12">';

    echo '<table class="table table-striped table-bordered">';
    echo '<thead>';
    echo '<tr>';
    echo '<th><input type="checkbox" name="all-ids" value="" id="all-ids" onclick="toggleCheckboxes(this)" /></th>';
    echo '<th>'.__('Name').'</th>';
    echo '<th>'.__('Mail').'</th>';
    echo '<th>'.__('Tesoriere Stato Pay').'</th>';
    echo '<th>'.__('data_mail_send').'</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach($request_payment->summary_payments as $summary_payment) {
        echo '<tr>';
        echo '<td>';
        if(!empty($summary_payment->user->email))
            echo '<input type="checkbox" name="ids" value="'.$summary_payment->user->id.'" id="ids" />';
        echo '</div>';
        echo '</td>';
        echo '<td>';
        echo $summary_payment->user->name;
        echo '</td>';
        echo '<td>';
        echo $this->HtmlCustom->mail($summary_payment->user->email);
        if(empty($summary_payment->user->email))
            echo $this->element('msg', ['msg' => "Il gasista non ha una mail associata", 'class' => 'danger']);
        echo '</td>';
        echo '<td>';
        $class = 'info';
        switch ($summary_payment->stato) {
            case 'SOLLECITO1':
                $class = 'warning';
                break;
            case 'SOLLECITO2':
                $class = 'danger';
                break;
        }
        echo '<span class="label label-'.$class.'">';
        echo __($summary_payment->stato);
        echo '</span>';
        echo '</td>';
        echo '<td>';
        if(empty($summary_payment->data_send))
            echo 'Mai';
        else
            echo $summary_payment->data_send->i18nFormat('eeee d MMMM');;
        echo '</td>';
        echo '</tr>';

    }
    echo '</tbody>';
    echo '</table>';

    echo '</div>';
    echo '</div>'; // row

    echo $this->Form->control('mail_subject', ['type' => 'text', 'value' => $mail_subject]);
    echo $this->Form->control('mail_body_pre', ['type' => 'textarea', 'rows' => 2, 'value' => $mail_body_pre, 'disabled' => true, 'label' => 'Testo introduttivo']);
    echo $this->Form->control('mail_body', ['type' => 'textarea', 'value' => $mail_body]);
    echo $this->Form->control('mail_body_post', ['type' => 'textarea', 'value' => $mail_body_post, 'disabled' => true, 'label' => 'Testo conclusivo']);

    echo '<div class="row">';
    echo '<div class="col col-md-12 col-12">';

    echo '<div style="display: none;" class="msg-send-KO alert alert-danger"></div>';
    echo '<div style="display: none;" class="msg-send-OK alert alert-info"></div>';

    echo '<div style="display: none;text-align: center;" class="run run-send">';
    echo '<div class="spinner"></div>';
    echo '</div>';
    echo '<button type="submit" class="btn btn-primary btn-block btn-send"><i class="fa fa-envelope"></i> '.__('invio').'</button>';
    echo '</div>';
    echo '</div>';
    echo '</fieldset>';
    echo $this->Form->end();
    echo '</div>';
    echo '</div>';
    echo '</div>';  // row
}
echo '</section>';


$js = "
function toggleCheckboxes(source) {
    let ids = document.querySelectorAll('input[name=\"ids\"]');
    for(var i = 0; i < ids.length; i++) {
        ids[i].checked = source.checked;
    }
}
$( function() {
    $('#frm').on('submit', function (e) {
        // e.preventDefault();

        let user_ids = [];
        let ids = document.querySelectorAll('input[name=\"ids\"]');
        for(var i = 0; i < ids.length; i++) {
           if(ids[i].checked)
             user_ids.push(ids[i].value);
        }

        if(user_ids.length == 0) {
            alert('Seleziona almeno un gasista al quale invidare la mail');
            return false;
        }


        let datas = $(this).serializeArray();
        datas.push({name: 'user_ids', value: user_ids});
        // console.table(datas, 'datas');
        let ico_spinner = 'fa-lg fa fa-spinner fa-spin';
        let ajaxUrl = '/admin/api/mails/request-payments';

        $('.msg-send-OK').hide();
        $('.msg-send-OK').html('');
        $('.msg-send-KO').hide();
        $('.msg-send-KO').html('');
        $('.btn-send').hide();
        $('.run-send').show();
        $('.run-send .spinner').addClass(ico_spinner);

        $.ajax({url: ajaxUrl,
            data: datas,
            type: 'POST',
            dataType: 'json',
            cache: false,
            headers: {
              'X-CSRF-Token': csrfToken
            },
            success: function (response) {
                console.log(response, 'response');

                /*
                 * invii fallito KO
                 */
                let msg_ko = '';
                if(typeof response.results.KO !== undefined) {
                    console.log(response.results.KO.length, 'KO.length');
                    response.results.KO.forEach(function (result) {
                        msg_ko += result+' non inviata!<br>';
                    });

                    if(response.results.KO.length > 0) {
                        $('.msg-send-KO').show();
                        $('.msg-send-KO').html(msg_ko);
                    }
                }

                /*
                 * invii corretti OK
                 */
                let msg_ok = '';
                if(typeof response.results.OK !== undefined) {
                    console.log(response.results.OK.length, 'KO.length');
                    response.results.OK.forEach(function (result) {
                        msg_ok += result+' inviata correttamente<br>';
                    });

                    if(response.results.OK.length > 0) {
                        $('.msg-send-OK').show();
                        $('.msg-send-OK').html(msg_ok);
                    }
                }
            },
            error: function (e) {
                console.log(e);
                $('.msg-send-KO').show();
                $('.msg-send-KO').html('Errore nel invio!');
                return false;
            },
            complete: function (e) {
                $('.btn-send').show();
                $('.run-send').hide();
                return false;
            }
        });

        return false;
    });
});
";
$this->Html->scriptBlock($js, ['block' => true]);
