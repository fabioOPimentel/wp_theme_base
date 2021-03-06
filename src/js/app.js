/** Modules */
import 'core-js/stable'
import 'regenerator-runtime/runtime'
import 'intersection-observer' // Optional
import Vue from 'vue';
import axios from 'axios';
import VueCookies from 'vue-cookies'

/** Components */
Vue.use(VueCookies)

/** Styles */

const app = new Vue({
    el: '#main',
    components: {},
    data:  {
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
        increaseFontSize() {
            let self = this;
            self.fontSizeState < 4 &&
                (document.querySelectorAll("p, span, a, h1, h2, h3, h4, h5, h6, li").forEach(function (t, e) {
                    let a = parseInt(getComputedStyle(t).fontSize);
                    (a = a + 1 + "px"),
                    t.style.fontSize = a;
                }), (self.fontSizeState += 1));
        },
        decreaseFontSize() {
            let self = this;
            -2 < self.fontSizeState &&
                (document.querySelectorAll("p, span, a, h1, h2, h3, h4, h5, h6, li").forEach(function (t, e) {
                    let a = parseInt(getComputedStyle(t).fontSize);
                    (a = a - 1 + "px"),
                    t.style.fontSize = a;
                }), (self.fontSizeState -= 1));
        },
        toogleContrast(e) {

            if(this.$cookies.isKey('highcontrast')){
                this.$cookies.remove('highcontrast');
                document.querySelector('body').classList.remove('contrast');
            } else{
                this.$cookies.set('highcontrast',"input_value",'7d');
                document.querySelector('body').classList.add('contrast');
            }
            e.target.focus();

        },
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

            axios.post(baseUrl.ajaxurl, formData).then(function (response) {
                self.contatoStatus = response.status;
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

                axios
                    .get('https://ipapi.co/json/')
                    .then( function(response){
                        self.geoip = response.ip;
                    });

                var formData = new FormData();
                formData.append('response', response);
                formData.append('remoteip', self.geoip);
                formData.append('action', 'gCaptcha');
                axios.post(ajaxUrl, formData).then(function (data){
                    self.recaptchaValidate = data.msg;
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
        },
        togglemenu(e) {
            let element = e.currentTarget,
                target = element.getAttribute('data-target'),
                classname = element.getAttribute('data-classname'),
                allelement = document.querySelectorAll(classname);

            allelement.forEach((el) => {
                el.classList.toggle('change');
            })
            document.querySelector(target).classList.toggle('change');
            document.querySelector('.sla__the_content').classList.toggle('change');
        },
        siblingschanges(e) {
            let el = e.target;
            let brothers = el.parentNode.children;
            for (let i = 0; i < brothers.length; i++) {
                brothers[i].classList.remove('changed');
            }
            el.classList.add('changed');
        },
        loadmore (posttype,limit,text,e) {
            let self = this;
            let targetEl = e.target.parentElement.previousElementSibling;
            let childCount = targetEl.childElementCount;

            let formData = new FormData();
            formData.append('posttype',posttype);

            formData.append('tags',self.trabalhos_tags);
            formData.append('orderby',self.trabalhos_order);
            formData.append('offset',childCount);
            formData.append('limit',limit);
            formData.append('action', 'load_more');

            e.target.innerHTML = '<div class="loader loader-4" id="loader-4"><span></span><span></span><span></span></div>';

            axioss
                .post(baseUrl.ajaxurl, formData)
                .then( function(response){
                    if(response.data){
                        targetEl.lastChild.insertAdjacentHTML('afterend',response.data);
                        e.target.innerText = text;
                    }else{
                        e.target.innerText = 'Não há mais conteudo'
                        e.target.setAttribute('disabled','disabled');
                    }
                });
            
        },
    },
    mounted() {
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

window.app = app;