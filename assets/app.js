/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

import './styles/app.css';
import 'datatables.net-bs5';
import '@fortawesome/fontawesome-free/css/all.css';
import $ from 'jquery';
import { Application } from '@hotwired/stimulus';
import HelloController from './controllers/hello_controller.js';

const app = Application.start();
app.register('hello', HelloController);

// Active DataTables sur tous les tableaux avec la classe .datatable
$(document).ready(function() {
    $('.datatable').DataTable();

    
});
