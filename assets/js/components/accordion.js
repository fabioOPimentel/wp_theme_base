var accordion = {
    props: [
        'title'
    ],
    data() {
        return {
        janela: window.innerWidth,
        active: false,
        }
    },
    template: `
                <div class="d-block mx-auto" v-bind:class="{accordion:janela < 992}">
                    <div class="tab__header">
                        <a href="#" class="tab__link p-4 block no-underline border-b-2 d-flex justify-content-between" @click.prevent="active = !active" v-if="janela < 992">
                            {{title}}
                            <span class="down-Arrow" v-show="!active">&#9660;</span>
                            <span class="up-Arrow" v-show="active">&#9650;</span>
                        </a>
                        <h3 v-else>{{title}}</h3>
                    </div>
                    <div class="tab__content p-2" v-show="active" v-if="janela < 992"><slot /></div>
                    <div class="tab__content p-2" v-else><slot /></div>          
                </div>
    `,
    mounted: function(){
        var self =  this;
        window.addEventListener('resize',function(){
            self.janela = window.innerWidth;
        })
    }
}