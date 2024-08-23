console.log("powerslip interact loaded");

window.initDraggable = function(){
    // target elements with the "draggable" class
    interact('.draggable')
        .draggable({
            // enable inertial throwing
            inertia: true,
            // keep the element within the area of it's parent
            restrict: {
                restriction: "parent",
                endOnly: true,
                elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
            },
            // enable autoScroll
            autoScroll: true,

            // call this function on every drag move event
            onmove: dragMoveListener,

            // call this function on every drag end event
            onend: function (event) {

                let myDraggable = event.target;
                let cmFromLeftAndFromTop = getCmFromLeftAndFromTop(myDraggable);

                /*
                 * index
                 */
                let index = event.target.getAttribute("data-index");
                console.log("index is: ", index);

                window.vm.updateField(cmFromLeftAndFromTop.cmX, cmFromLeftAndFromTop.cmY, index);

            }
        });
}


/**
 * règle de 3
 * see one note
 */
function getCmFromLeftAndFromTop(element) {
    let targetX = element.getBoundingClientRect().x;
    let targetY = element.getBoundingClientRect().y;

    let canvas = document.querySelector('#preview-box');
    let canvasBoundingRect = canvas.getBoundingClientRect();

    let xDiff = targetX - canvasBoundingRect.x;
    let yDiff = targetY - canvasBoundingRect.y;

    //XXX
    //TODO Use 21 and 29.7 values from the template settings. Do not assume A4 format.
    let cmX       = xDiff * 21 / canvasBoundingRect.width;
    let cmY       = yDiff * 29.7 / canvasBoundingRect.height;

    return {
        cmX,
        cmY
    }
}


//Works with delta (from previous position)
function dragMoveListener(event) {
    let target = event.target,
        // keep the dragged position in the data-x/data-y attributes
        x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx,
        y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

    // translate the element
    target.style.webkitTransform =
        target.style.transform =
            'translate(' + x + 'px, ' + y + 'px)';

    // update the position attributes
    target.setAttribute('data-x', x);
    target.setAttribute('data-y', y);
}


window.dragMoveListener = dragMoveListener;