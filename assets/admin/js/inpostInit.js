import {DEFAULT_SELECTORS} from '../../common/js/geowidget/config';
import {GeoWidget} from '../../common/js/geowidget';
import {SaveChangedPoint} from './saveChangedPoint';

if (document.querySelectorAll('[data-bb-target="inpost-geowidget"]').length > 0) {
    new GeoWidget().init();
    new SaveChangedPoint(DEFAULT_SELECTORS.button).init();
}
