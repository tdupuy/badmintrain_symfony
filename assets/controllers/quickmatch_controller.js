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
    }
    
    createQuickMatch(e) {
        const subs = e.currentTarget.dataset.subs;
        const idtournament = e.currentTarget.dataset.idtournament;
        const turn = e.currentTarget.dataset.turn
        this.callMatchesController(subs, idtournament, turn);
    }

    async callMatchesController(subs, idtournament, turn) {
        const response = await fetch('/create-quickmatch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ subs, idtournament, turn })
        });
        const data = await response.json();
        if(data.code = "success"){
            window.location.reload();
        }
    }
}
