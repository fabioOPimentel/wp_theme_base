var testapp = new Vue({
    el: '#main',
    data: {
        contato: {
            nome: '',
            tel: '',
            email: '',
            msg: ''
        },
        recaptchaValidate: ''
    },
    methods: {
        sendContact: function () {
            var self = this,
                formData = new FormData(),
                forms = $('.form-container'),
                btn = $('.btn'),
                curriculo = $('#curriculo')[0].files[0]
                nonce = $('#security_contact').val();
            
            formData.append('nome', self.cadastro.nome);
            formData.append('datan', self.cadastro.datan);
            formData.append('tel', self.cadastro.tel);
            formData.append('email', self.cadastro.email);
            formData.append('crm', self.cadastro.crm);
            formData.append('espec', self.cadastro.espec);
            formData.append('rqe', self.cadastro.rqe);
            formData.append('curriculo', curriculo);
            formData.append('action', 'enviar_cadastro');
            formData.append('security_contact', nonce);

            btn.attr("disabled", true);

            if(forms[0].checkValidity() === false){
                self.formValidate = false;
                forms.addClass('was-validated');
                return;
            }

            if (self.recaptchaValidate !== 'Recaptcha Valido') {
                $('#html_element div div iframe').css({
                    'border': '1px solid #dc3545',
                });
                setTimeout(function(){ 
                    btn.removeAttr("disabled"); 
                }, 200);
                return;
            }

            self.formValidate = true;
            btn.removeClass('btn-amarelo').addClass('btn-info');
            btn.html('<div class="loader loader-4" id="loader-4"><span></span><span></span><span></span></div>');

            self.$http.post(loadmore_params.ajaxurl, formData).then(function (response) {
                self.contatoStatus = response.body.status;
                if(self.contatoStatus == 'success'){
                    forms[0].reset();
                    grecaptcha.reset(widgetId);
                    btn.removeClass('btn-info').addClass('btn-success');
                    btn.text('Enviado!');
                }else if(self.contatoStatus == 'danger'){
                    grecaptcha.reset(widgetId);
                    self.formValidate = false;
                    btn.removeClass('btn-info').addClass('btn-danger');
                    btn.removeAttr("disabled");
                    btn.text('Erro ao enviar!');
                }else{
                    self.formValidate = false;
                    forms.addClass('was-validated');
                    btn.removeClass('btn-info').addClass('btn-warning');
                    btn.text('Corrija os campos!');
                }

                setTimeout(function(){
                    btn.removeClass('btn-success');
                    btn.removeClass('btn-danger');
                    btn.removeClass('btn-warning');
                    btn.addClass('btn-amarelo');
                    btn.text('Enviar');
                }, 2000);
            });
        },
        correctCaptcha: function(response){
            var self = this,
                ajaxUrl = loadmore_params.ajaxurl;
            return new Promise(function(resolve, reject) { 
                $.getJSON('http://www.geoplugin.net/json.gp?jsoncallback=?', function(data) {
                    self.geoip = data.geoplugin_request;
                });

                var formData = new FormData();
                formData.append('response', response);
                formData.append('remoteip', self.geoip);
                formData.append('action', 'gCaptcha');
                self.$http.post(ajaxUrl, formData).then(function (data){
                    self.recaptchaValidate = data.body.msg;
                });
                
                resolve();
            })
        },
        fildIsValid: function(e) {
            var self = this;
            var forms = $(e);
            if(forms[0].checkValidity() === false){
                self.formValidate = false;
            }else{
                self.formValidate = true;
            }
        },
    },
    mounted: function(){
        const observer = lozad();
        observer.observe();
    }
});


