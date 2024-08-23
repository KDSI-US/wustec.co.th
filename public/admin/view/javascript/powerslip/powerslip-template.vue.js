console.log("powerslip vue loaded");
if(! window.vue_data['fields']){
    window.vue_data['fields'] = [];
}

window.vm = new Vue({
    data: window.vue_data,
    el: "#template-form",
    methods: {
        addTableRow: function (slug){
            console.log("slug: ", slug);
            this.$data.fields.push({"slug":slug,"left":"","right":"","top":"", "uuid": Math.random().toString(36).substring(7)})
        },
        removeTableRow : function (index){
            console.log("row to delete: ", index);
            this.$data.fields.splice(index, 1);
        },

        calculateNewLeft : function(index){
            let left = this.$data.fields[index].left;
            return this.convertLeftFromCmToPx(left);
        },
        calculateNewTop : function(index){
            let top = this.$data.fields[index].top;
            return this.convertTopFromCmToPx(top);

        },
        convertLeftFromCmToPx(cm){
            let canvas = document.querySelector('#preview-box');
            let canvasBoundingRect = canvas.getBoundingClientRect();
            //canvasBoundingRect has different when page loads. We must wait for it
            //for now, we make sure to load the css powerslip-interact.css before this script powerslip_template.vue.js
            //console.log("canvasBoundingRect.width: ", canvasBoundingRect.width);
            let leftInPx = canvasBoundingRect.width * cm / this.$data.width;
            return leftInPx;

        },convertTopFromCmToPx(cm){
            let canvas = document.querySelector('#preview-box');
            let canvasBoundingRect = canvas.getBoundingClientRect();
            let topInPx = canvasBoundingRect.height * cm / this.$data.height;
            return topInPx;
        },

        moveFieldByCssAll: function () {
            let self = this;
            this.$data.fields.forEach(function(field, index){
                console.log("Field index: ", index, " field: ", field);
                self.moveFieldByCss(index);
            });
        },
        moveFieldByCss: function (index) {
            let leftPx = this.calculateNewLeft(index);
            let topPx = this.calculateNewTop(index);
            //console.log("leftPx: ", leftPx, " topPx", topPx);

            let transform = `matrix(1, 0, 0, 1, ${leftPx}, ${topPx})`;
            $(".draggable[data-index=" + index + "]").css({"transform": transform})
                .attr('data-x', leftPx)
                .attr('data-y', topPx);
        },
        myKeypress: function(index, which){
            console.log(this.$data.fields[index]);
            this.moveFieldByCss(index);
        },
        updateField: function(cmX, cmY, index){
            console.log("inside update field, cmX", cmX, " cmY", cmY);
            this.$data.fields[index].left = cmX;
            this.$data.fields[index].top  = cmY;
        },

        onBoardDimensionChange: function (event) {
            console.log("board dimension changed ", event.target.value);
            console.log("width: ", this.$data.width);
            console.log("height: ", this.$data.height);

            //do nothing if no value is provided
            if (! this.$data.width || ! this.$data.height){
                return;
            }

            let canvas = document.querySelector('#preview-box');
            let canvasBoundingRect = canvas.getBoundingClientRect();
            //canvasBoundingRect.width  → vue width (ex: 19 cm)
            //canvas.style.height  ???  → vue height(ex: 21 cm)
            //rule of 3:
            // css height cm = vue height * css width / vue width cm
            let height = this.$data.height * canvasBoundingRect.width / this.$data.width;
            console.log("calculated height is: ", height);
            canvas.style.height = height + "px" ;

            //reflect change on all toasts
            this.moveFieldByCssAll();
        }
    },
    mounted : function (){
        console.log("vue instance mounted");
        initDraggable();

        this.moveFieldByCssAll();
    },
});