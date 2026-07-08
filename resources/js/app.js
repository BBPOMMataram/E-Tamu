import './bootstrap';

import Alpine from 'alpinejs';
import { CountUp } from 'countup.js';
import { Chart, BarController, BarElement, CategoryScale, LinearScale, Title, Tooltip, Legend } from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Title, Tooltip, Legend);

window.Alpine = Alpine;
window.CountUp = CountUp;
window.Chart = Chart;

Alpine.start();

