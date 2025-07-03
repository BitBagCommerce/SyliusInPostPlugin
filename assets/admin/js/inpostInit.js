import {DEFAULT_SELECTORS} from '../../common/js/geowidget/config';
import {GeoWidget} from '../../common/js/geowidget';
import {SaveChangedPoint} from './saveChangedPoint';

console.log('geowidget', document.querySelectorAll('[data-bb-target="inpost-geowidget"]'));
if (document.querySelectorAll('[data-bb-target="inpost-geowidget"]').length > 0) {
    console.log('wchodzi i robi init');
    new GeoWidget().init();
    new SaveChangedPoint(DEFAULT_SELECTORS.button).init();
}
