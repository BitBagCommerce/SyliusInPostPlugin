import {GeoWidgetPreview} from '.';
import triggerCustomEvent from '../utilities/triggerCustomEvent';

const DEFAULT_MODAL_CONFIG = {
    width: window.innerWidth > 1000 ? window.innerWidth * 0.5 : 500,
    height: window.innerHeight * 0.75,
};

export class GeoWidgetButton {
    constructor(node, modalConfig) {
        this.button = node;
        this.container = document.querySelector(this.button.dataset.bbTarget);
        this.modalConfig = Object.assign({}, DEFAULT_MODAL_CONFIG, modalConfig);

        this.init();
    }

    init() {
        this.button.addEventListener('click', this._onClickSelectorButton.bind(this));
    }

    _onClickSelectorButton(event) {
        event.preventDefault();

        triggerCustomEvent(this.button, 'inpost.point.modal.open');

        easyPack.modalMap((point, modal) => this.onModalClose({point, modal}), this.modalConfig);
    }

    async onModalClose({point, modal}) {
        const path = this.button.dataset.bbPath;
        const savedPoint = await this._savePoint(`${path}?name=${point.name}`);

        if (this.container !== 'undefined') {
            new GeoWidgetPreview(this.container).renderTemplate(savedPoint);
        }

        triggerCustomEvent(this.button, 'inpost.point.modal.close');

        modal.closeModal(savedPoint);
    }

    async _savePoint(path) {
        triggerCustomEvent(this.button, 'inpost.point.save.before');

        try {
            const response = await fetch(path);

            if (!response.ok) throw Error(response.statusText);
            const data = await response.json();

            triggerCustomEvent(this.button, 'inpost.point.save.completed', data);
            triggerCustomEvent(this.button, 'inpost.point.save.after');

            return data;
        } catch (error) {
            triggerCustomEvent(this.button, 'inpost.point.save.error', error);
            triggerCustomEvent(this.button, 'inpost.point.save.after');
        }
    }
}

export default GeoWidgetButton;
