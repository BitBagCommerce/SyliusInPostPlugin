import {DEFAULT_SELECTORS} from '../../common/js/geowidget/config';

export class ValidateNextBtn {
    constructor({nodeSelector = '#next-step', node}) {
        this.submit = document.querySelector(nodeSelector);
        this.input = node;
        this.previewSelector = DEFAULT_SELECTORS.preview;
        this.button = document.querySelector(DEFAULT_SELECTORS.button);
    }

    init() {
        if (!this.submit) {
            throw new Error('BitBagInPostPlugin - The specified submit selector could not be found in the DOM');
        }
        if (!this.input) {
            throw new Error('BitBagInPostPlugin - The specified input node could not be found in the DOM');
        }
        this._handleSubmitListeners();
    }

    _handleSubmitListeners() {
        this.input.addEventListener('bb.inpost.point.selected', () => {
            if (document.querySelector(this.previewSelector)) {
                return;
            }
            this._turnOffSubmit();
        });
        this.input.addEventListener('bb.inpost.point.deselected', () => {
            this._turnOnSubmit();
        });
        this.button.addEventListener('bb.inpost.point.save.completed', () => {
            this._turnOnSubmit();
        });
        this.button.addEventListener('bb.inpost.point.save.error', () => {
            this._turnOffSubmit();
        });
    }

    _turnOnSubmit() {
        this.submit.disabled = false;
        this.submit.classList.add('enabled');
        this.submit.classList.remove('disabled');
    }

    _turnOffSubmit() {
        this.submit.disabled = true;
        this.submit.classList.add('disabled');
        this.submit.classList.remove('enabled');
    }
}

export default ValidateNextBtn;
