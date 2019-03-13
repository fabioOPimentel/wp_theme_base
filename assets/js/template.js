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
            var self = this;
            var formData = new FormData();
            formData.append('nome', self.contato.nome);
            formData.append('email', self.contato.email);
            formData.append('tel', self.contato.tel);
            formData.append('msg', self.contato.msg);
            formData.append('action', 'enviar_contato');
            self.$http.post(location.origin + '/wptest/wordpress/wp-admin/admin-ajax.php', formData).then(function (response) {
                console.log(response);
                self.feedbackMsg = response.data.message;
                self.contatoStatus = response.status;
                if (response.data.status === '' && self.recaptchaValidate !== 'Recaptcha Validado') {
                    $('#html_element div div iframe').addClass('error');
                }else{
                    self.contato.nome = '';
                    self.contato.email = '';
                    self.contato.tel = '';
                    self.contato.msg = '';
                }
            });
        },
        correctCaptcha: function(response){
            var self = this;
            $.getJSON('http://www.geoplugin.net/json.gp?jsoncallback=?', function(data) {
                self.geoip = data.geoplugin_request;
            });

            var formData = new FormData();
            formData.append('response', response);
            formData.append('remoteip', self.geoip);
            formData.append('action', 'gCaptcha');
            
            self.$http.post(location.origin + self.baseUrl, formData).then(function (data){
                self.recaptchaValidate = data.bodyText;
            });
        }
    },
    mounted: function(){
        const observer = lozad();
        observer.observe();
    }
});


