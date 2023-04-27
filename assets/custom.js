if (document.querySelector('.select-team')) {
    document.querySelector('.select-team').addEventListener('change', function () {
        let teamId = this.value;
        if (teamId) {
            ajax('Post', '/get-team-players', {id: teamId}).then(function (response) {
                let list = document.querySelector('.for-players ul');
                list.innerHTML = '';
                let listItem = '';

                response.players.forEach(function (value) {
                    listItem = `<li class=\"list-group-item d-flex justify-content-between\">
                                    <span>${value.name} ${value.surname}</span> 
                                    <p>$ ${value.price}</p> 
                                    <button class="btn bg-primary buy-player" data-id="${value.id}">Buy</button>
                                </li>`;

                    list.insertAdjacentHTML('afterbegin', listItem)

                    let elem = document.querySelector('.buy-player');
                    elem.addEventListener('click', buyPlayer);
                });
            })
        }
    })
}

if (document.querySelector('.available-for-sale')) {
    addEventByClassName('available-for-sale', 'change', changeIsAvailableSale);
}

if (document.querySelector('.new-player-row')) {
    addEventByClassName('new-player-row', 'click', addPlayerRowToForm);
}

// Functions

async function ajax(type, url, data = {}) {
    const rawResponse = await fetch(url, {
        method: type,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });
    return await rawResponse.json();
}

function buyPlayer() {
    let _this = this;
    let playerId = _this.getAttribute('data-id')
    let to = document.querySelector('.buy-to').value
    ajax(
        'Post',
        '/buy-player',
        {
            id: playerId,
            to: to
        })
        .then(function (response) {
            if (response.success) {
                let fromName = document.querySelector('.select-team');
                let toName = document.querySelector('.buy-to');

                fromName.options[fromName.selectedIndex].text = response.fromText;
                toName.options[toName.selectedIndex].text = response.toText;

                _this.parentElement.remove();
            }
        })
}

function changeIsAvailableSale() {
    let _this = this;
    let playerId = _this.value
    let checked = _this.checked;

    ajax(
        'Post',
        '/change-is-available-sale',
        {
            id: playerId,
            checked: checked
        }
    ).then(function (response) {

    })

}

function addEventByClassName(className, eventType, functionName) {
    document.querySelectorAll('.' + className).forEach(function (value) {
        value.addEventListener(eventType, functionName)
    })
}

function addPlayerRowToForm() {
    let count = document.querySelectorAll('.new-player-row').length;
    let container = document.querySelector('.players');
    let row = `<div class="mb-3 row ">
                    <div class="col-4">
                        <input type="text" class="form-control" name="players[${count + 1}][name]" id="playerName"
                               placeholder="Player name"
                               value="" required>
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control" name="players[${count + 1}][surename]" id="surName"
                               placeholder="Player surname"
                               value="" required>
                    </div>
                    <div class="col-3">
                        <input type="number" class="form-control" name="players[${count + 1}][price]" id="price"
                               placeholder="Player price"
                               value="" required>
                    </div>

                    <div class="col-1 d-flex justify-content-end">
                        <button class="btn bg-primary new-player-row" type="button"> <i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>`

    container.insertAdjacentHTML('afterend', row);

    addEventByClassName('new-player-row', 'click', addPlayerRowToForm);
}