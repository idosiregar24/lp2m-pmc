import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import contributorsField from './components/contributorsField';
import facultyStudyProgram from './components/facultyStudyProgram';
import lecturerPicker from './components/lecturerPicker';
import barChart from './components/barChart';
import donutChart from './components/donutChart';
import excelImporter from './components/excelImporter';

Alpine.plugin(focus);
Alpine.data('contributorsField', contributorsField);
Alpine.data('facultyStudyProgram', facultyStudyProgram);
Alpine.data('lecturerPicker', lecturerPicker);
Alpine.data('barChart', barChart);
Alpine.data('donutChart', donutChart);
Alpine.data('excelImporter', excelImporter);

window.Alpine = Alpine;
Alpine.start();
