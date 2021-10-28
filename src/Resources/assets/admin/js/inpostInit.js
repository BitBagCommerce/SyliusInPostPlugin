import {DEFAULT_SELECTORS} from '../../common/js/geowidget/config';
import triggerCustomEvent from '../../common/js/utilities/triggerCustomEvent';
import {GeoWidget} from '../../common/js/geowidget';

new GeoWidget().init();

class SaveChangedPoint {
    constructor(nodeSelector) {
        this.saveBtn = document.querySelector(nodeSelector);
    }

    init() {
        if (!this.saveBtn) {
            throw new Error('Please pass proper node selector, into class constructor - couldnt find given one.');
        }
        this._saveNewShipping();
    }

    _saveNewShipping() {
        this.saveBtn.addEventListener('bb.inpost.point.save.completed', (event) => {
            const data = this.saveBtn.dataset;
            const url = `/admin${data.bbPath}?orderId=${data.bbOrder}&name=${event.detail.name}`;
            this._postNewPoint(url);
        });
    }

    async _postNewPoint(path = '/') {
        const settings = {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        };
        try {
            const response = await fetch(path, settings);

            if (!response.ok) throw Error(response.statusText);
            const data = await response.json();

            triggerCustomEvent(this.saveBtn, 'inpost.point.order.save.completed', data);
            triggerCustomEvent(this.saveBtn, 'inpost.point.order.save.after');

            return data;
        } catch (error) {
            triggerCustomEvent(this.saveBtn, 'inpost.point.order.save.error', error);
            triggerCustomEvent(this.saveBtn, 'inpost.point.order.save.after');
        }
    }
}

new SaveChangedPoint(DEFAULT_SELECTORS.button).init();
