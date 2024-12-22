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

    addPlayer(e) {
        e.preventDefault();

        const turn = e.target.elements['turn'].value;
        console.log(turn);
        //this.callMatchesController(turn);
    }

    async callMatchesController(turn) {
        const response = await fetch('/add-player', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ turn })
        });
        const data = await response.json();
        if(data.code = "success"){
           // window.location.reload();
        }
    }
}
