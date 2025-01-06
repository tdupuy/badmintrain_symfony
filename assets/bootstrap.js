import { startStimulusApp } from '@symfony/stimulus-bundle';
import { Modal } from 'bootstrap';

const app = startStimulusApp();

// Initialiser Bootstrap
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser les modals Bootstrap
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modalEl => new Modal(modalEl));
});
