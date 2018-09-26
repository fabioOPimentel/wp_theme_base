var testapp = new Vue({
    el: '#main',
    data: {
        contato: {
            nome: '',
            tel: '',
            email: '',
            msg: ''
        }
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
                if (response.data.status === 'success') {
                    self.contato.nome = '';
                    self.contato.email = '';
                    self.contato.tel = '';
                    self.contato.msg = '';
                }
            });
        }
    }
});


