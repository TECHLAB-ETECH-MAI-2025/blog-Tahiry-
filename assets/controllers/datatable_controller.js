import { Controller } from '@hotwired/stimulus';
import $ from 'jquery';
import 'datatables.net';
import 'datatables.net-bs5';

export default class extends Controller {
    connect() {
        $(this.element).DataTable({
            serverSide: true,
            ajax: this.element.dataset.source,
            columns: JSON.parse(this.element.dataset.columns)
        });
    }
}