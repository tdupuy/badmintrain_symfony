import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {
        console.log('Le contrôleur Stimulus est connecté');
    }
    
    setWinner(e) {
        console.log('Element clické');
        const teamid = e.currentTarget.dataset.teamid;
        const matchid = this.element.dataset.matchid;
        console.log(matchid);
        this.callMatchesController(matchid, teamid);
    }

    async callMatchesController(matchid, teamid) {
        const response = await fetch('/set-winner', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ matchid, teamid })
        });
        const data = await response.json();
        console.log(data);
    }
}
