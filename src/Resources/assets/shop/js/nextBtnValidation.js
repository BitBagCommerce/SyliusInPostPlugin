export class ValidateNextBtn {
    constructor(nodeSelector = '#next-step') {
        this.submit = nodeSelector;
    }

    init(node) {
        this.handleListeners(node);
    }

    handleListeners(eventHandler) {
        eventHandler.addEventListener('bb.inpost.point.selected', () => {
            console.log('1');
            this._turnOnListener();
        });
        eventHandler.addEventListener('bb.inpost.point.deselected', () => {
            console.log('2');
            this._turnOffListener();
        });
    }

    _turnOnListener() {}

    _turnOffListener() {}
}

export default ValidateNextBtn;
