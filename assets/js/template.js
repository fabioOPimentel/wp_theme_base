var myapp = new Vue({
    el: '#main',
    data: {
        formValidate: false,
        recaptchaState: false,
        isDisabled: false,
        recaptchaValidate: ''
    },
    watch: {
        formValidate(){
            this.isDisabled = this.formDisabled();
        },
        recaptchaState(){
            this.isDisabled = this.formDisabled();
        }
    },
    methods: {
        sendContact: function (e,action,btnid) {
            var self = this,
                formData = new FormData(),
                forms = e.target,
                btn = document.querySelector(btnid);
            
            var formData = new FormData(forms);
            formData.append('action', action);

            btn.setAttribute("disabled", "");

            self.formIsValidForSend();

            self.captchaIsValid();

            self.formValidate = true;
            btn.classList.remove(btnid)
            btn.classList.add('btn-info');
            btn.innerHTML = '<div class="loader loader-4" id="loader-4"><span></span><span></span><span></span></div>';

            self.$http.post(baseUrl.ajaxurl, formData).then(function (response) {
                self.contatoStatus = response.body.status;
                if(self.contatoStatus == 'success'){
                    btn.classList.remove('btn-info')
                    btn.classList.add('btn-success');
                    btn.innerText = 'Enviado!';
                }else{
                    btn.classList.remove('btn-info')
                    btn.classList.add('btn-danger');
                    btn.removeAttribute("disabled");
                    btn.innerText = 'Erro ao enviar!';
                }

                forms.reset();
                grecaptcha.reset(widgetId);
                self.formValidate = false;
                self.recaptchaState = false;
                self.recaptchaValidate = '';

                setTimeout(function(){
                    btn.classList.remove('btn-success');
                    btn.classList.remove('btn-danger');
                    btn.classList.remove('btn-warning');
                    btn.classList.add(btnid);
                    btn.innerText = 'Enviar';
                }, 2000);
            });
        },
        captchaIsValid: function(){
            if (self.recaptchaValidate !== 'Recaptcha Valido') {
                $('#html_element div div iframe').css({
                    'border': '1px solid #dc3545',
                });
                setTimeout(function(){ 
                    btn.removeAttribute("disabled"); 
                }, 200);
                return;
            }
        },
        correctCaptcha: function(response){
            var self = this,
                ajaxUrl = baseUrl.ajaxurl;
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
                    self.recaptchaState = true;
                });
                
                resolve();
            })
        },
        formDisabled(){
            return this.formValidate && this.recaptchaState;
        },
        fildIsValid: function(e) {
            var self = this;
            var forms = e.target;
            if(forms.checkValidity() === false){
                self.formValidate = false;
            }else{
                self.formValidate = true;
            }
        },
        formIsValid: function (e) {
            self = this;
            var form = e.currentTarget;

            if (form.checkValidity() === false) {
                form.classList.add('was-validated')
                self.formValidate = false;
            } else {
                form.classList.remove('was-validated')
                self.formValidate = true;
            }
        },
        formIsValidForSend(forms) {
            if(forms.checkValidity() === false){
                self.formValidate = false;
                forms.classList.add('was-validated');
                return;
            }
        }
    },
    mounted: function(){
        lozad('.lozad', {
            load: function (el) {
                el.src = el.dataset.src;
                el.onload = function () {
                    el.classList.add('lazyfade')
                }
            }
        }).observe();
    }
});


